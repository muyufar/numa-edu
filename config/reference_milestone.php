<?php

/**
 * Rujukan perencanaan fitur (diselaraskan dengan milestone SIMANU.id).
 * Digunakan hanya sebagai acuan di UI; prioritas implementasi numa-edu dapat berbeda.
 *
 * Di numa-edu sudah ada inti: master data, PPDB, presensi siswa/guru/pegawai, nilai, tagihan/pembayaran,
 * BK (pelanggaran), berita publik (/informasi), perizinan siswa, ekspor CSV pelaporan (siswa, nilai, presensi siswa).
 */
return [
    'source' => 'SIMANU.id',
    'title' => 'Milestone referensi',
    'description' => 'Ringkasan target modul dari dokumen perencanaan. Bukan jadwal rilis resmi aplikasi.',
    'columns' => [
        [
            'key' => 'ready',
            'title' => 'SIAP DIGUNAKAN',
            'accent' => 'emerald',
            'features' => [
                'Website',
                'Info & Berita',
                'PPDB',
                'Data Master',
                'Presensi Guru',
                'Presensi Siswa',
                'Presensi Pegawai',
                'Sistem Perizinan',
                'Kinerja Guru & Staff',
                'Sistem Notifikasi WA',
                'Pelaporan',
            ],
        ],
        [
            'key' => 'september',
            'title' => 'SIAP SEPTEMBER',
            'accent' => 'amber',
            'features' => [
                'Sistem Pembayaran',
                'Sistem Keuangan',
                'Aplikasi Wali Murid',
                'Kurikulum',
                'Materi / Bahan Ajar',
                'Optimasi fitur sebelumnya',
                'Masukan fitur oleh para guru, kepsek, pengurus LPM',
            ],
        ],
        [
            'key' => 'december',
            'title' => 'SIAP DESEMBER',
            'accent' => 'sky',
            'features' => [
                'CBT / Test Online',
                'Sistem Notifikasi',
                'Integrasi EMIS',
                'Sistem Inventaris',
                'E-Raport',
                'Optimasi fitur sebelumnya',
                'Masukan fitur oleh para guru, kepsek, pengurus LPM',
            ],
        ],
    ],
];
