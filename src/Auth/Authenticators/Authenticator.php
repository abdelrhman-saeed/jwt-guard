<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Authenticators;

use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Token;

abstract class Authenticator
{

    /**
     * @property Token $token
     */
    private Token $token;

    /**
     * check if the JWT is valid
     * 
     * @return null|array
     */
    abstract public function isTokenValid(): ?array;


    /**
     * check if the Refresh Token is valid
     * 
     * @return bool
     */
    abstract public function isRefreshTokenValid(): ?array;

    /**
     * generates a JWT
     * 
     * @param null|array $user
     * 
     * @return string
     */
    abstract public function generateToken(?array $user = null): string;

    /**
     * generates a refresh token
     * 
     * @param bool $longLives - determines if the token will have a long expiration time or not
     * 
     * @return string
     */
    abstract public function generateRefreshToken(bool $longLives, ?int $userID = null): string;

    /**
     * revokes the refresh token
     * 
     * @return bool
     */
    abstract public function revokeRefreshToken(): bool;
}
