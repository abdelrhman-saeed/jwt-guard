<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default;

use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms\Key;
use abdelrhmanSaeed\JwtGuard\Database\Models\RefreshToken;

use Facades\Firebase\JWT\JWT;
use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Token;
use Exception;
use Firebase\JWT\Key as JWTKey;

class DefaultToken extends Token
{

    /**
     * @property array $payload - user's payload data
     */

    private array $payload;

    /**
     * @param array $config - token's configurations
     * @param bool $longlives - a boolean refers to if the token will have aa long time xpiration or not
     * @param Key $key
     */
    public function __construct(private array $config, private Key $key) {}
    
    /**
     * @param array $payload - $payload property setter
     * @return self
     */
    public function setPayload(array $payload): self {

        $this->payload = $payload;
        return $this;
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
        return JWT::encode(
            array_merge($this->payload, $this->config['payload']),
            $this->getKey()->getForEncoding(),
            $this->config['token_alg']
        );
    }

    /**
     * Generates refresh token
     */
    public function generateRefreshToken(bool $longlives): string
    {
        /**
         * Storing The Refresh Token In The Database And Sending its UUID as an actuall Token
         */

        $expiration = $longlives ? $this->config['refresh_token']['long_expiration']
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
        try {
            return (array) JWT::decode($token, $key);
        }
        catch(Exception $e) {
            return null;
        }
    }

    /**
     * Debug The Refresh Token
     * 
     * @return null|array
     */
    public function debugRefreshToken(string $refresh_token): ?array
    {
        
        $refresh_token = RefreshToken::firstWhere('uuid', $refresh_token);

        if ( now()->lt($refresh_token->expire_at) ) {
            return $refresh_token->toArray();
        }

        return null;
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
