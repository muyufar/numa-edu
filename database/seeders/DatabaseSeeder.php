<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        Cabang::query()->whereKey(1)->update([
            'mou_lp_next_sequence' => 546,
            'mou_lp_number_digits' => 4,
            'mou_lp_number_suffix' => '/PC.1/LPM/E.11/V/2026',
            'mou_penandatangan_nama' => "H. M. Nurdin Syafi'i, S.Ag, M.Si.",
            'mou_penandatangan_jabatan' => "Ketua LP Ma'arif NU PCNU\nKab. Magelang",
            'mou_surat_kota' => 'Magelang',
        ]);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@numa-edu.local'],
            [
                'name' => 'Super Admin Numa-Edu',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['super_admin']);

        $pengurus = User::query()->firstOrCreate(
            ['email' => 'pengurus@numa-edu.local'],
            [
                'name' => 'Pengurus Cabang Demo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'cabang_id' => 1,
                'sekolah_id' => null,
            ]
        );

        $pengurus->forceFill([
            'cabang_id' => 1,
            'sekolah_id' => null,
        ])->save();

        $pengurus->syncRoles(['pengurus_cabang']);

        $adminPcnu = User::query()->firstOrCreate(
            ['email' => 'admin.pcnu@numa-edu.local'],
            [
                'name' => 'Admin PCNU',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'cabang_id' => 1,
                'sekolah_id' => null,
            ]
        );

        $adminPcnu->forceFill([
            'cabang_id' => 1,
            'sekolah_id' => null,
        ])->save();

        $adminPcnu->syncRoles(['pengurus_cabang']);

        $pengurusLpMaarifKab = User::query()->firstOrCreate(
            ['email' => 'pengurus.lpmaarif.kab@numa-edu.local'],
            [
                'name' => 'Pengurus LP Ma\'arif NU Kabupaten',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'cabang_id' => 1,
                'sekolah_id' => null,
            ]
        );

        $pengurusLpMaarifKab->forceFill([
            'cabang_id' => 1,
            'sekolah_id' => null,
        ])->save();

        $pengurusLpMaarifKab->syncRoles(['pengurus_cabang']);
    }
}
