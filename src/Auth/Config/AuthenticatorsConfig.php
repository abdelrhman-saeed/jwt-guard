<?php

use \abdelrhmanSaeed\JwtGuard\Auth\Authenticators;

return [
    'authenticators' => [
      'default'   => Authenticators\Default\DefaultAuthenticator::class,
      'google'    => Authenticators\Socials\GoogleAuthenticator::class,
      'facebook'  => Authenticators\Socials\FacebookAuthenticator::class,
    ],

    'openid_configurations' => [

      'GoogleAuthenticator' => [
        'openid_configurations_url' => 'https://accounts.google.com/.well-known/openid-configuration',

        'endpoint_config' => [
          'client_id'     => $google_client_id = env('GOOGLE_CLIENT_ID'),
          'client_secret' => $google_client_secret = env('GOOGLE_CLIENT_SECRET'),


          /**
           * endpoints query parameters
           */
          'obtain_authorization_code' => [
            'uri'     => 'https://accounts.google.com/o/oauth2/v2/auth',
            'params'  => [
              'client_id'     => $google_client_id,
              'response_type' => env('GOOGLE_RESPONSE_TYPE', 'code'),
              'scope'         => env('GOOGLE_SCOPES', 'openid profile'),
              'prompt'        => env('GOOGLE_PROMPT_SCREEN', 'consent'),
              'access_type'   => env('GOOGLE_ACCESS_TYPE', 'offline'),
              // redirect uri => ''
            ],
            'headers' => [],
          ],

          'exchange_authorization_code' => [
            'uri' => 'https://oauth2.googleapis.com/token?',
            'headers' => [],
            'params'  => [
              'client_id'     => $google_client_id,
              'client_secret' => $google_client_secret,
              'grant_type'    => env('GOOGLE_TOKEN_GRANT_TYPE', 'authorization_code')
            ]
          ],

          'decode_jwt' => [
            'jwks_uri'  => 'https://www.googleapis.com/oauth2/v3/certs',
            'headers'   => []
          ],

          // set the refresh token with the authenticator
          'revoke_refresh_access_token' => [
            'uri'           => 'https://oauth2.googleapis.com/revoke',
            'headers'       => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'refresh_token' => cookie(env('GOOGLE_REFRESH_TOKEN_NAME'), 'GOOGLE_REFRESH_TOKEN'),
          ],
          
          'regenerate_access_token' => [
            'uri'           => 'https://oauth2.googleapis.com/token',
            'headers'       => [],
            'refresh_token' => cookie(env('GOOGLE_REFRESH_TOKEN_NAME'), 'GOOGLE_REFRESH_TOKEN'),
          ]
        ]
      ]

      // 'GoogleAuthenticator' => 'https://accounts.google.com/.well-known/openid-configuration',
      // 'FacebookAuthenticator' => 'https://www.facebook.com/.well-known/openid-configuration/'
    ]
];