<?php

namespace AbdelrhmanSaeed\JwtGuard\Auth\TokenHandlers;

class DefaultTokenHandler extends TokenHandler
{

    /**
     * Generates JWT
     */
    public function generateToken(): string {
        return '';
    }
    
    /**
     * Generates refresh token
     */
    public function generateRefreshToken(): string {
        return '';
    }
    
    /**
     * Debug JWT to check
     * 
     * @return null|array
     */
    public function debugToken(): ?array {
        return [];
    }
    
    /**
     * Debug The Refresh Token
     */
    public function debugRefreshToken(): bool {
        return true;
    }

    /**
     * Revoking the User token
     * 
     * @return bool
     */
    public function revokeTokens(): bool {
        return true;
    }
}
