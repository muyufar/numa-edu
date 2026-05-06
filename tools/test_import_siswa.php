<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;

$file = $argv[1] ?? null;
if (! $file) {
    fwrite(STDERR, "Usage: php tools/test_import_siswa.php <path-to-xlsx>\n");
    exit(2);
}

$import = new SiswaImport();
Excel::import($import, $file);

echo "processed={$import->processed} created={$import->created} updated={$import->updated}\n";

