<?php

return [

    /*
    | Sekolah default untuk data yang sudah ada sebelum multi-tenant,
    | dan untuk statistik publik landing jika tidak ada konteks login.
    */
    'default_sekolah_id' => (int) env('TENANT_DEFAULT_SEKOLAH_ID', 1),

    /*
    | Domain sintetis untuk akun operator sekolah (format NPSN@...).
    */
    'operator_email_domain' => env('TENANT_OPERATOR_EMAIL_DOMAIN', 'numa.com'),

];
