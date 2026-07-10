<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = $argv[1] ?? __DIR__.'/../Daftar_GTK (1).xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();

$headers = [];
$maxCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());
for ($c = 1; $c <= $maxCol; $c++) {
    $headers[$c] = trim((string) $sheet->getCellByColumnAndRow($c, 1)->getFormattedValue());
}

echo 'Total rows (incl header): '.$sheet->getHighestRow().PHP_EOL;
echo 'Columns ('.count(array_filter($headers)).'):'.PHP_EOL;
foreach ($headers as $i => $h) {
    if ($h !== '') {
        echo '  '.$i.'. '.$h.PHP_EOL;
    }
}

$stats = [
    'data_rows' => 0,
    'no_nama' => 0,
    'no_email' => 0,
    'has_nip' => 0,
    'has_nuptk' => 0,
    'pns' => 0,
    'non_pns' => 0,
];

$colIndex = array_flip($headers);
$get = static function (array $row, string $header) use ($colIndex, $sheet): string {
    $idx = $colIndex[$header] ?? null;
    if ($idx === null) {
        return '';
    }

    return trim((string) $sheet->getCellByColumnAndRow($idx, $row)->getFormattedValue());
};

for ($r = 2; $r <= (int) $sheet->getHighestRow(); $r++) {
    $nama = $get($r, 'Nama Lengkap');
    if ($nama === '') {
        $stats['no_nama']++;
        continue;
    }

    $stats['data_rows']++;
    $email = $get($r, 'Email') ?: $get($r, 'Email Akun Madrasah Digital');
    if ($email === '') {
        $stats['no_email']++;
    }

    if ($get($r, 'NIP') !== '') {
        $stats['has_nip']++;
    }
    if ($get($r, 'NUPTK') !== '') {
        $stats['has_nuptk']++;
    }

    $status = strtoupper($get($r, 'Status Kepegawaian'));
    if (str_contains($status, 'PNS') && ! str_contains($status, 'NON')) {
        $stats['pns']++;
    } else {
        $stats['non_pns']++;
    }
}

echo PHP_EOL.'Stats:'.PHP_EOL;
foreach ($stats as $k => $v) {
    echo '  '.$k.': '.$v.PHP_EOL;
}
