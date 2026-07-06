<?php

namespace App\Exports;

use App\Models\Siswa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class SiswaExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithCustomValueBinder
{
    private int $rowNumber = 0;

    public function collection(): Collection
    {
        return Siswa::query()
            ->with('kelas:id,tingkat,nama,tahun_ajaran')
            ->orderBy('nama')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'NIS',
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

    public function map($siswa): array
    {
        $this->rowNumber++;

        $kelas = $siswa->kelas;
        $kelasLabel = $kelas ? trim("Kelas {$kelas->tingkat} - {$kelas->nama}") : ($siswa->tingkat_rombel ?? '');

        return [
            $this->rowNumber,
            $siswa->nama,
            $siswa->nis,
            $siswa->nisn ?? '',
            $siswa->nik ?? '',
            $siswa->tempat_lahir ?? '',
            $siswa->tanggal_lahir?->format('Y-m-d') ?? '',
            $kelasLabel,
            $siswa->umur ?? '',
            $siswa->status ?? '',
            $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin === 'P' ? 'Perempuan' : ''),
            $siswa->alamat ?? '',
            $siswa->no_telepon ?? '',
            $siswa->kebutuhan_khusus ?? '',
            $siswa->disabilitas ?? '',
            $siswa->nomor_kip_pip ?? '',
            $siswa->nama_ayah_kandung ?? '',
            $siswa->nama_ibu_kandung ?? '',
            $siswa->nama_wali ?? '',
        ];
    }
}

