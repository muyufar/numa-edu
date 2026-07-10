<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = $argv[1] ?? __DIR__.'/../Daftar_GTK (1).xlsx';
$spreadsheet = IOFactory::load($file);

foreach ($spreadsheet->getWorksheetIterator() as $index => $sheet) {
    echo '=== Sheet '.($index + 1).': '.$sheet->getTitle().' ==='.PHP_EOL;
    $maxRow = min(5, (int) $sheet->getHighestRow());
    $maxCol = min(20, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn()));

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
    echo 'Total rows: '.$sheet->getHighestRow().PHP_EOL.PHP_EOL;
}
