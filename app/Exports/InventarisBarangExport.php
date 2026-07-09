<?php

namespace App\Exports;

use App\Models\InventarisBarang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventarisBarangExport implements FromCollection, WithHeadings, WithMapping
{
    private int $row = 0;

    public function collection(): Collection
    {
        return InventarisBarang::query()
            ->with('kategori:id,nama')
            ->orderBy('nama')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Stok Awal',
            'Stok Akhir',
            'Stok Minimum',
            'Kondisi',
            'Status',
            'Catatan',
        ];
    }

    public function map($barang): array
    {
        $this->row++;

        return [
            $this->row,
            $barang->kode ?? '',
            $barang->nama,
            $barang->kategori?->nama ?? '',
            $barang->satuan,
            $barang->stok_awal,
            $barang->stok_akhir,
            $barang->stok_minimum,
            InventarisBarang::kondisiLabel($barang->kondisi),
            $barang->is_active ? 'Aktif' : 'Nonaktif',
            $barang->catatan ?? '',
        ];
    }
}
