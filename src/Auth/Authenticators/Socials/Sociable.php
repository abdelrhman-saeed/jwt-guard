<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Authenticators\Socials;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use ReflectionClass;

trait Sociable
{
    /**
     * @var array $authenticatorConfig
     */
    private array $authenticatorConfig = [];

    /**
     * @var null|string $redirectURI
     */
    private ?string $redirectURI = null;


    public function __construct() {
        $this->setOpenIDConfigurations();
    }

    /**
     * @param string $redirectURI
     * @return void
     */
    public function setRedirectURI(string $redirectURI): void {
        $this->redirectURI = $redirectURI;
    }

    /**
     * @return void
     */
    protected function setOpenIDConfigurations(): void {

        $authenticatorConfig = Config::get(
            $cache_key = 'openid_configurations.' . (new ReflectionClass($this))->getShortName()
        );

        if (( $this->openIDConfigurations = Cache::get($cache_key) ) === null) {
            Cache::set(
                $cache_key,
                $this->openIDConfigurations = Http::get($authenticatorConfig['openid_configurations_url'])->json()
            );
        }
    }

    /**
     * call the google endpoint that is defined in the AuthenticatorsConfig File
     * to for obtaining the authorization code
     * 
     * @return RedirectResponse
     */
    protected function obtainAuthorizationCode(): RedirectResponse
    {
        if ($this->redirectURI === null) {
            throw new JwtAuthenticatorException('The redirect uri is not set!');
        }

        $obtainsAuthorizationCodeURI = rtrim(
            $this->authenticatorConfig['obtain_authorization_code']['uri'], '?') . '?';

        return redirect(
            $obtainsAuthorizationCodeURI
            . http_build_query($this->authenticatorConfig['obtain_authorization_code']['params'])
        );
    }


    /**
     * call google endpoint that is defined in the AuthenticatorsConfig File to
     * obtain the User Tokens ( access, refresh, openid ) Tokens
     * 
     * @param Request $request
     * @return array
     */
    protected function obtainUserTokens(Request $request): array
    {
        $tokensUriparams = $this->authenticatorConfig['exchange_authorization_code']['params'];

        $tokensUriparams['redirect_uri'] = $this->redirectURI;
        $tokensUriparams['code'] = $request->code;

        return Http::post(
                $this->authenticatorConfig['exchange_authorization_code']['uri'],
                $tokensUriparams
            )->json();
    }

    /**
     * gets the JWTs Keys From Google endpoint that is defined in the AuthenticatorsConfig File
     * for decoding the Jwts.
     * 
     * then caches the Keys and decode the tokens locally for subsequent requests.
     * 
     * @param string $jwt - the JSON WEB TOKEN
     * @return array
     */
    protected function decodeJwt(string $jwt): array {

        $cache_key  = 'openid_configurations.' . $class = (new ReflectionClass($this))->getShortName() . '.jwks_uri';
        $jwks       = Cache::get($cache_key);

        if ($jwks  === null) {
            Cache::set( $cache_key, Http::get( Cache::get("openid_configurations.$class")['jwks_uri'] )->json());
        }

        return (array) JWT::decode($jwt, JWK::parseKeySet($jwks));
    }

}


