<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiswaTemplateExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'NISN',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Tingkat - Rombel',
            'Umur',
            'Status',
            'Jenis Kelamin',
            'Alamat',
            'No Telepon',
            'Kebutuhan Khusus',
            'Disabilitas',
            'Nomor KIP/PIP',
            'Nama Ayah Kandung',
            'Nama Ibu Kandung',
            'Nama Wali',
        ];
    }
}

