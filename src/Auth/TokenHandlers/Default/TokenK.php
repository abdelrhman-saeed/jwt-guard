<?php

namespace AbdelrhmanSaeed\JwtGuard\Auth\TokenHandlers\Default;

use AbdelrhmanSaeed\JwtGuard\Auth\Exceptions\TokenKException;

class TokenK
{
    /**
     * generate a token key that is used to decode and encode the JWT.
     * The Key's Generation is Based on The Supported Algorithms By The Package And
     * The Algorithm Type That is Defined by the Package User in the
     * Package Config file or in The env File of The Project
     * 
     * @return String
     */
    public static function generate(): string
    {

        switch (config('alg')) {
            case 'RS256':
                set_error_handler(function () {
                    throw new TokenKException('Your Key\'s Passphrase is wrong');
                }, E_WARNING);
    
                $key = openssl_pkey_get_private( file_get_contents( config('token_alg') ), config('passphrase') );
                restore_error_handler();
                break;

            case 'HS256':
                $key = config('token_key');
                break;
        }

        return $key;
    }
}