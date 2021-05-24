<?php

return [
    'test' => env('ESIA_TEST', true),
    'clientId' => env('ESIA_CLIENT_ID'),
    'redirectUrl' => config('app.url') . '/esia/login/callback',
    'scope' => [],
    'signer' => Esia\Signer\CliSignerPKCS7::class,
    'certPath' => env('ESIA_CERT_PATH'),
    'privateKeyPath' => env('ESIA_PRIVATE_KEY_PATH'),
    'privateKeyPassword' => env('ESIA_PRIVATE_KEY_PASSWORD'),
    'tmpPath' => storage_path('/tmp'),
    'default_role' => env('ESIA_DEFAULT_ROLE')
];
