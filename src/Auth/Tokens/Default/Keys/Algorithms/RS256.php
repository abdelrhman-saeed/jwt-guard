<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms;

use abdelrhmanSaeed\JwtGuard\Exceptions\TokenKException;

class RS256 extends Key
{
    public function getForEncoding()
    {
        $key = openssl_pkey_get_private( file_get_contents( $this->config['token_key'] ), $this->config['passphrase'] );

        if ( ! $key ) {
            throw new TokenKException('Your Key\'s Passphrase is wrong');
        }

        return $key;
    }

    public function getForDecoding()
    {
        return openssl_pkey_get_details($this->getForEncoding())['key'];
    }
}
