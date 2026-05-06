<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = $argv[1] ?? null;
if (! $file) {
    fwrite(STDERR, "Usage: php tools/inspect_xlsx.php <path-to-xlsx>\n");
    exit(2);
}

$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();

$maxRow = min(15, (int) $sheet->getHighestRow());
$maxCol = min(30, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn()));

for ($r = 1; $r <= $maxRow; $r++) {
    $values = [];
    for ($c = 1; $c <= $maxCol; $c++) {
        $v = $sheet->getCellByColumnAndRow($c, $r)->getFormattedValue();
        $v = is_string($v) ? trim($v) : $v;
        if ($v !== '' && $v !== null) {
            $values[] = $v;
        }
    }
    if ($values) {
        echo 'R'.$r.': '.implode(' | ', $values).PHP_EOL;
    }
}

