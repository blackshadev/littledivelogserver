<?php

return [
    'secret' => env('TAUTH_SECRET'),
    'issuer' => env('TAUTH_ISSUER'),
    'audience' => env('TAUTH_AUDIENCE'),
    'lifetime' => env('TAUTH_LIFETIME'),
    'signer' => env('TAUTH_SIGNER', 'HS256')
];
