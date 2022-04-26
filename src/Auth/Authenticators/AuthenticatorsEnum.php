<?php

namespace AbdelrhmanSaeed\JWT\Auth\Authenticators;

use AbdelrhmanSaeed\JWT\Auth\Authenticators\DefaultAuthenticator;


enum AuthenticatorsEnum: string
{
    case Default = DefaultAuthenticator::class;
    case Facebook = FacebookAuthenticator::class;
    case Google = GoogleAuthenticator::class;
}

// print_r(Authenticators::Default);

// $authenticator = new (Authenticators::Default->value);

// try {
    
//     echo Authenticators::df->throw();
// }
// catch(Throwable $e) {

//     print_r($e);

// }