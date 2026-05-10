<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateUserCommand extends Command
{
    protected $signature = 'numa:create-user
                            {email : Alamat email login}
                            {name : Nama tampilan}
                            {--role=admin : super_admin|admin|guru|siswa|wali|pengurus_cabang}
                            {--password= : Jika kosong, akan ditanya (disarankan)}
                            {--cabang_id= : FK cabangs (default otomatis menurut role)}
                            {--sekolah_id= : FK sekolahs (default otomatis menurut role)}';

    protected $description = 'Buat atau perbarui user + role Spatie. Untuk pengurus LP Ma\'arif tingkat kabupaten (verifikasi pendaftaran lembaga, dsb.) gunakan role pengurus_cabang + cabang_id cabang terkait.';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $name = (string) $this->argument('name');
        $role = (string) $this->option('role');

        $allowed = ['super_admin', 'admin', 'guru', 'siswa', 'wali', 'pengurus_cabang'];
        if (! in_array($role, $allowed, true)) {
            $this->error('Role tidak valid. Pilih: '.implode(', ', $allowed));

            return self::FAILURE;
        }

        foreach ($allowed as $r) {
            Role::query()->firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        if (! Role::query()->where('name', $role)->where('guard_name', 'web')->exists()) {
            $this->error("Role {$role} tidak ditemukan setelah seed role.");

            return self::FAILURE;
        }

        $password = (string) $this->option('password');
        if ($password === '') {
            $password = (string) $this->secret('Password (minimal 8 karakter)');
        }
        if (strlen($password) < 8) {
            $this->error('Password minimal 8 karakter.');

            return self::FAILURE;
        }

        [$cabangId, $sekolahId] = $this->resolveTenantIds($role);

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'cabang_id' => $cabangId,
                'sekolah_id' => $sekolahId,
            ]
        );

        $user->syncRoles([$role]);

        $this->info("User siap: {$email} (role: {$role})");
        $cb = $user->cabang_id !== null ? (string) $user->cabang_id : 'null';
        $sk = $user->sekolah_id !== null ? (string) $user->sekolah_id : 'null';
        $this->line("cabang_id={$cb} | sekolah_id={$sk}");

        return self::SUCCESS;
    }

    /**
     * @return array{0: ?int, 1: ?int}
     */
    private function resolveTenantIds(string $role): array
    {
        $cabangOpt = $this->option('cabang_id');
        $sekolahOpt = $this->option('sekolah_id');

        if ($cabangOpt !== null && $cabangOpt !== '') {
            $cabangId = (int) $cabangOpt;
        } else {
            $cabangId = match ($role) {
                'super_admin' => null,
                'pengurus_cabang' => 1,
                default => 1,
            };
        }

        if ($sekolahOpt !== null && $sekolahOpt !== '') {
            $sekolahId = (int) $sekolahOpt;
        } else {
            $sekolahId = match ($role) {
                'super_admin', 'pengurus_cabang' => null,
                default => 1,
            };
        }

        return [$cabangId, $sekolahId];
    }
}
