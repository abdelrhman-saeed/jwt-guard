<?php

namespace AbdelrhmanSaeed\JwtGuard\Auth\TokenHandlers;

use AbdelrhmanSaeed\JwtGuard\Auth\TokenHandlers\Default\TokenK;
use AbdelrhmanSaeed\JwtGuard\Exceptions\InvalidTokenConfigException;
use Exception;
use Firebase\JWT\JWT;

class DefaultTokenHandler extends TokenHandler
{

    public function __construct(private array $payload) {
    }
    /**
     * Generates JWT
     */
    public function generateToken(): string {

        if ( ! in_array(config('token_alg'), config('supported_algs')) ) {
            throw new InvalidTokenConfigException('The Algorithm That You Defined is Not Supported Yet.');
        }

        $payload = array_merge( config('payload'), $this->payload);
        return JWT::encode($payload, TokenK::generate(), config('token_alg'));
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
