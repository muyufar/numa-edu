<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Deploy Runner (sementara)
    |--------------------------------------------------------------------------
    |
    | Aktifkan hanya saat deploy tanpa SSH. Set DEPLOY_RUNNER_ENABLED=false
    | atau hapus route setelah selesai.
    |
    */

    'enabled' => (bool) env('DEPLOY_RUNNER_ENABLED', false),

    'token' => env('DEPLOY_RUNNER_TOKEN'),

];
