<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GtkSheetReader
{
    public static function rows(string $path, string|int|null $sheet = null, int $fallbackIndex = 0): Collection
    {
        $spreadsheet = IOFactory::load($path);
        $worksheet = self::resolveWorksheet($spreadsheet, $sheet, $fallbackIndex);

        if ($worksheet === null) {
            throw new \InvalidArgumentException(__('Sheet tidak ditemukan di file Excel.'));
        }

        $matrix = $worksheet->toArray(null, true, true, false);
        if (count($matrix) < 2) {
            return collect();
        }

        $headings = HeadingRowFormatter::format($matrix[0]);
        $rows = collect();

        for ($i = 1; $i < count($matrix); $i++) {
            $line = $matrix[$i];
            $assoc = [];
            $isEmpty = true;

            foreach ($headings as $j => $heading) {
                if ($heading === '' || $heading === null) {
                    continue;
                }

                $value = $line[$j] ?? null;
                if ($value !== null && trim((string) $value) !== '') {
                    $isEmpty = false;
                }

                $assoc[$heading] = $value;
            }

            if (! $isEmpty) {
                $rows->push($assoc);
            }
        }

        return $rows;
    }

    private static function resolveWorksheet(Spreadsheet $spreadsheet, string|int|null $sheet, int $fallbackIndex): ?Worksheet
    {
        if (is_string($sheet) && $sheet !== '') {
            $byName = self::findSheetByName($spreadsheet, $sheet);
            if ($byName !== null) {
                return $byName;
            }
        }

        if (is_int($sheet) && $spreadsheet->getSheetCount() > $sheet) {
            return $spreadsheet->getSheet($sheet);
        }

        if ($spreadsheet->getSheetCount() > $fallbackIndex) {
            return $spreadsheet->getSheet($fallbackIndex);
        }

        return null;
    }

    private static function findSheetByName(Spreadsheet $spreadsheet, string $name): ?Worksheet
    {
        $target = trim($name);

        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            if (strcasecmp(trim($worksheet->getTitle()), $target) === 0) {
                return $worksheet;
            }
        }

        return null;
    }
}
