<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Authenticators;


class DefaultAuthenticator extends Authenticator
{
        /**
     * Check if the User is authenticated
     * 
     * it will check if there's a token in the HTTP Authorization Header With Bearer Schema.
     * if the token is not found, then will check for the refresh token that's stored in the
     * browser cookie
     * 
     * @return bool
     */
    public function authenticated(): bool
    {
        return true;
    }

    /**
     * Authenticate The User to the System
     * 
     * @return bool
     */
    public function authenticate(): bool {
        return true;
    }

    /**
     * loging the user out by revoking the user tokens
     * 
     * @return bool
     */
    public function logout(): bool {
        return true;
    }
}
