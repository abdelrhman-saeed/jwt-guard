<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms;

class RS256 extends Key
{
    public function getForEncoding()
    {
        set_error_handler(function () {
            throw new TokenKException('Your Key\'s Passphrase is wrong');
        }, E_WARNING);

        $key = openssl_pkey_get_private( file_get_contents( $this->config['token_key'] ), $this->configg['passphrase'] );
        restore_error_handler();

        return $key;
    }

    public function getForDecoding()
    {
        return openssl_pkey_get_details($this->getForEncoding())['key'];
    }
}
