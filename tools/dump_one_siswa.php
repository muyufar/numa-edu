<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$s = \App\Models\Siswa::query()->whereNotNull('nisn')->orderBy('id')->first();
if (! $s) {
    echo "no siswa found\n";
    exit(0);
}

echo json_encode($s->only([
    'nis',
    'nisn',
    'nik',
    'nama',
    'tempat_lahir',
    'tanggal_lahir',
    'tingkat_rombel',
    'umur',
    'status',
    'jenis_kelamin',
    'alamat',
    'no_telepon',
    'kebutuhan_khusus',
    'disabilitas',
    'nomor_kip_pip',
    'nama_ayah_kandung',
    'nama_ibu_kandung',
    'nama_wali',
]), JSON_PRETTY_PRINT).PHP_EOL;

