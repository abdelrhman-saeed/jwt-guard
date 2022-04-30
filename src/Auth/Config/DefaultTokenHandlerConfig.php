<?php

return [

    'supported_algs' => [ 'HS256', 'RS256'],
    'token_alg' => env('TOKEN_ALS'),
    'token_key' => env('TOKEN_KEY'),
    'passphrase' => env('TOKEN_KEY_PASSPHRASE'),

    'payload' => [
        'iss' => env('APP_URL'),
        'aud' => env('TOKEN_AUD'),
        'iat' => time(),
        'ntb' => env('TOKEN_PAYLOAD_NTB', time()),
        'exp' => env('TOKEN_PAYLOAD_EXP', time() + (60 * 15))
    ]
];