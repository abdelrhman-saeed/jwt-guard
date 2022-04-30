<?php

namespace AbdelrhmanSaeed\JwtGuard\Auth\Authenticators;

use AbdelrhmanSaeed\JwtGuard\Auth\Authenticators\DefaultAuthenticator;


enum AuthenticatorsEnum: string
{
    case Default = DefaultAuthenticator::class;
}