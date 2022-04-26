<?php

namespace AbdelrhmanSaeed\JWT\Auth\TokenHandlers;

abstract class TokenHandler
{

    /**
     * Generates JWT
     */
    abstract public function generateToken(): string;
    
    /**
     * Generates refresh token
     */
    abstract public function generateRefreshToken(): string;
    
    /**
     * Debug JWT to check
     * 
     * @return null|array
     */
    abstract public function debugToken(): ?array;
    
    /**
     * Debug The Refresh Token
     */
    abstract public function debugRefreshToken(): bool;

    /**
     * Revoking the User token
     * 
     * @return bool
     */
    abstract public function revokeTokens(): bool;
}