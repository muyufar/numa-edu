<?php

namespace App\Support;

final class SchoolOperator
{
    /**
     * Email login sintetis: bagian lokal dari NPSN + domain konfigurasi.
     */
    public static function emailFromNpsn(string $npsn): string
    {
        $local = preg_replace('/\D/', '', $npsn);
        if ($local === '') {
            $local = strtolower(preg_replace('/[^a-zA-Z0-9._-]/', '', $npsn));
        }

        $domain = config('tenancy.operator_email_domain', 'numa.com');

        return $local.'@'.$domain;
    }
}
