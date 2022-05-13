<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms;

abstract class Key
{
    /**
     * @param array $config - token config
     */
    public function __construct(protected array $config) {
    }
    /**
     * generated the token key that will be used for encoding the token
     */
    abstract public function getForEncoding();

    /**
     * generated the token key that will be used for decoding the token
     */
    abstract public function getForDecoding();
}
