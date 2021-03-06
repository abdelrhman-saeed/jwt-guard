<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Authenticators;

use Illuminate\Http\Request;

abstract class Authenticator
{
    /**
     * check if the JWT is valid
     * 
     * @return null|array
     */
    abstract public function isTokenValid(Request $request): ?array;


    /**
     * check if the Refresh Token is valid
     * 
     * @return null|array
     */
    abstract public function isRefreshTokenValid(Request $request): ?array;

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
    abstract public function revokeRefreshToken(Request $request): bool;
}
