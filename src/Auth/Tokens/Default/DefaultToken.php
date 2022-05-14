<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default;

use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms\Key;
use abdelrhmanSaeed\JwtGuard\Exceptions\InvalidTokenConfigException;
use Facades\abdelrhmanSaeed\JwtGuard\Models\RefreshToken;
use Facades\Firebase\JWT\JWT;
use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Token;
use Firebase\JWT\Key as JWTKey;
use Illuminate\Support\Facades\Log;

class DefaultToken extends Token
{
    /**
     * @param array $payload - user's payload
     * @param array $config - token's configurations
     * @param bool $longlives - a boolean refers to if the token will have aa long time xpiration or not
     * @param Key $key
     */
    public function __construct(
        private array $payload, private array $config, private bool $longLives, private Key $key)
    {    
    }
    
    /**
     * retreving a key instance by the defined algorithm name from the supported keys in the conig file
     * 
     * @return Key
     */
    protected function getKey(): Key {
        return $this->key;
    }

    /**
     * Generates JWT
     */
    public function generateToken(): string
    {
        /**
         * merging the user's payload and the token's payload
         * 
         * retreving a key instance by the defined algorithm name
         * from the supported keys in the conig file
         */
        try
        {
            return JWT::encode(
                array_merge($this->payload, $this->config['payload']),
                $this->getKey()->getForEncoding(),
                $this->config['token_alg']
            );
        }
        catch (\Throwable $th)
        {
            throw new InvalidTokenConfigException($th->getMessage());
        }
    }

    /**
     * Generates refresh token
     */
    public function generateRefreshToken(): string
    {
        /**
         * Storing The Refresh Token In The Database And Sending its UUID as an actuall Token
         */

        $expiration = $this->longLives ? $this->config['refresh_token']['long_expiration']
                                                : $this->config['refresh_token']['expiration'];

        return RefreshToken::create([
            'uuid'      => \Illuminate\Support\Str::uuid(),
            'user_id'   => $this->payload['id'],
            'expire_at' => $expiration
        ])->uuid;
    }

    /**
     * Debug JWT to check
     * 
     * @param string $token - the JWT
     * @return null|array
     */
    public function debugToken(string $token, JWTKey $key): ?array
    {
        return (array) JWT::decode($token, $key);
    }

    /**
     * Debug The Refresh Token
     */
    public function debugRefreshToken(string $refresh_token): bool
    {
        return now()->lt( RefreshToken::firstWhere('uuid', $refresh_token)->expire_at );
    }

    /**
     * Revoking Refresh Token And Access Token
     * 
     * @param string $refresh_token - the refresh token that will be revoked
     * @return bool
     */
    public function revokeRefreshToken(string $refresh_token): bool
    {
        return RefreshToken::firstWhere('uuid', $refresh_token)->update(['expire_at' => now()]);
    }
}
