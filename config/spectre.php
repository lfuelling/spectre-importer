<?php
declare(strict_types=1);


return [
    'version'         => '1.0.0-alpha.1',
    'access_token'    => env('FIREFLY_III_ACCESS_TOKEN'),
    'uri'             => env('FIREFLY_III_URI'),
    'upload_path'     => storage_path('uploads'),
    'minimum_version' => '5.2.5',
    'spectre_app_id'  => env('SPECTRE_APP_ID', ''),
    'spectre_secret'  => env('SPECTRE_SECRET', ''),
    'spectre_uri'     => 'https://www.saltedge.com/api/v5',
];
