<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cabang default untuk pendaftaran lembaga publik
    |--------------------------------------------------------------------------
    |
    | Permohonan baru diarahkan ke cabang ini hingga alur konfigurasi wilayah
    | ditambahkan pada formulir publik.
    |
    */
    'default_cabang_id' => (int) env('LEMBAGA_DEFAULT_CABANG_ID', 1),
];
