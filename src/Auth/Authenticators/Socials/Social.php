<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Authenticators\Socials;

use abdelrhmanSaeed\JwtGuard\Authenticator;

interface Social extends Authenticator {   

    public function setRedirectURI(string $redirectURI): void;
    
}