<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Authenticators\Default;

use abdelrhmanSaeed\JwtGuard\Auth\Authenticators\Authenticator;
use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\DefaultToken;
use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Token;
use abdelrhmanSaeed\JwtGuard\Exceptions\JwtAuthenticatorException;
use abdelrhmanSaeed\JwtGuard\Exceptions\TokenKException;

use Firebase\JWT\Key as JWTKey;
use Illuminate\Http\Request;

class DefaultAuthenticator extends Authenticator
{

    private Token $token;
    private \abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms\Key $key;
    private array $config;

    public function __construct()
    {


        $this->config   = (array) config('DefaultTokenConfig');
        $supported_algs = $this->config['supported_algs'];

        if ( ! isset( $supported_algs[$this->config['token_alg']] )) {
            throw new TokenKException('The Algorithm That You Defined To Encode The Token is Not Supported Yet!');
        }

        $this->key   = app( $supported_algs[$this->config['token_alg']], [ 'config' => $this->config] ); 
        $this->token = app( DefaultToken::class, [ 'config' => $this->config, 'key' => $this->key ]);
    }
    
    /** 
     * Check if the JWT is valid
     * 
     * @return null|array
     */
    public function isTokenValid(Request $request): ?array
    {
        return $this->token->debugToken(
                $request->bearerToken() , (new JwtKey( $this->key->getForDecoding(), $this->config['token_alg'] ))
            );
    }

    /**
     * check if the refresh token is valid
     * 
     * @return bool
     */
    public function isRefreshTokenValid(Request $request): ?array {
        return $this->token->debugRefreshToken($request->cookie('refresh_token'));
    }

    /**
     * generates a JWT
     * 
     * @return string
     */
    public function generateToken(?array $user = null): string {

        if ($user === null) {
            throw new JwtAuthenticatorException('The User param Must Not Be Null With The Default Authenticator');
        }

        return $this->token->setPayload($user)->generateToken();
    }

    /**
     * generates a refresh token
     * 
     * @param bool $longLives - determines if the token will have a long expiration time or not
     * 
     * @return string
     */
    public function generateRefreshToken(bool $longLives, ?int $userID = null): string
    {
        return $this->token->setPayload(['id' => $userID])->generateRefreshToken($longLives);
    }

    /**
     * loging the user out by revoking the user tokens
     * 
     * @return bool
     */
    public function revokeRefreshToken(Request $request): bool {
        return $this->token->revokeRefreshToken($request->cookie('refresh_token'));
    }
}