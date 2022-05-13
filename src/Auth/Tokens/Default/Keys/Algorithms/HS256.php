<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms;

class HS256 extends Key
{
    public function getForEncoding(): string
    {
        return $this->config['token_key'];
    }

    public function getForDecoding(): string
    {
        return $this->config['token_key'];
    }
}
