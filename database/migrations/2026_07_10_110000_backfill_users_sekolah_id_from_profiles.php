<?php

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Guru::withoutGlobalScopes()
            ->whereNotNull('user_id')
            ->whereNotNull('sekolah_id')
            ->orderBy('id')
            ->get(['id', 'user_id', 'sekolah_id'])
            ->each(function (Guru $guru): void {
                User::query()
                    ->whereKey($guru->user_id)
                    ->whereNull('sekolah_id')
                    ->update(['sekolah_id' => $guru->sekolah_id]);
            });

        Siswa::withoutGlobalScopes()
            ->whereNotNull('user_id')
            ->whereNotNull('sekolah_id')
            ->orderBy('id')
            ->get(['id', 'user_id', 'sekolah_id'])
            ->each(function (Siswa $siswa): void {
                User::query()
                    ->whereKey($siswa->user_id)
                    ->whereNull('sekolah_id')
                    ->update(['sekolah_id' => $siswa->sekolah_id]);
            });
    }

    public function down(): void
    {
        // Data backfill tidak di-rollback.
    }
};
