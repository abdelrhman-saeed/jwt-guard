<?php

namespace AbdelrhmanSaeed\JwtGuard\Auth\Tokens;

use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms\Key;
use Firebase\JWT\Key as JWTKey;

abstract class Token
{

    /**
     * Generates JWT
     * 
     * @return string
     */
    abstract public function generateToken(): string;
    
    /**
     * Generates refresh token
     *
     * @return string
     */
    abstract public function generateRefreshToken(): string;
    
    /**
     * Debug JWT to check
     * 
     * @param string $token - the JWT
     * @return null|array
     */
    abstract public function debugToken(string $token, JWTKey $key): ?array;
    
    /**
     * Debug The Refresh Token
     * 
     * @param string $refreshToken
     * @return bool
     */
    abstract public function debugRefreshToken(string $refresh_token): bool;

    /**
     * Revoking Refresh Token And Access Token
     * 
     * @param string $refresh_token
     * @return bool
     */
    abstract public function revokeRefreshToken(string $refresh_token): bool;

    /**
     * @return abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms\Key
     */
    abstract protected function getKey(): Key;
}