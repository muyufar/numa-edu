<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Exports\SiswaExport;
use Maatwebsite\Excel\Facades\Excel;

$out = $argv[1] ?? (__DIR__.'/../storage/app/siswa-export-test.xlsx');

Excel::store(new SiswaExport(), $out, null, \Maatwebsite\Excel\Excel::XLSX);

echo "exported: {$out}\n";

