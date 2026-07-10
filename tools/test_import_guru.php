<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Imports\GuruImport;
use Database\Seeders\RoleSeeder;
use Maatwebsite\Excel\Facades\Excel;

$seeder = new RoleSeeder();
$seeder->run();

$import = new GuruImport();
Excel::import($import, __DIR__.'/../Daftar_GTK (1).xlsx');

echo 'processed='.$import->processed.PHP_EOL;
echo 'created='.$import->created.PHP_EOL;
echo 'updated='.$import->updated.PHP_EOL;
echo 'skipped='.$import->skipped.PHP_EOL;
