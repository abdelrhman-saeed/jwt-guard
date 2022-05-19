<?php

use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms;


return [

    'supported_algs' => [
        'HS256' => Algorithms\HS256::class,
        'RS256' => Algorithms\RS256::class,
    ],

    'token_alg' => env('TOKEN_ALG', 'HS256'),
    'token_key' => env('TOKEN_KEY'),
    'passphrase' => env('TOKEN_KEY_PASSPHRASE'),

    'payload' => [
        'iss' => env('APP_URL'),
        'aud' => env('TOKEN_AUD'),
        'iat' => time(),
        'ntb' => env('TOKEN_PAYLOAD_NTB', time()),
        'exp' => env('TOKEN_PAYLOAD_EXP', time() + (60 * 15))
    ],

    'refresh_token' => [
        'expiration' => now()->addHours(3),
        'long_expiration' => now()->months(3),
    ]
];