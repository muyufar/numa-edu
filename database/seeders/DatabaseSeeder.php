<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

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
    }
}
