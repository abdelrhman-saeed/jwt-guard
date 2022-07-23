<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Guards;

use abdelrhmanSaeed\JwtGuard\Auth\Authenticators\Authenticator;
use abdelrhmanSaeed\JwtGuard\Exceptions\JwtAuthenticatorException;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class Jwt implements Guard
{

    /**
     * 
     * @property Authenticator $authenticator
     */
    private Authenticator $authenticator;

    /**
     * 
     * @property null|User $user
     */
    private ?User $user = null;


    public function __construct()
    {
        $authenticatorsConfig   = config('AuthenticatorsConfig');
        $request                = app(Request::class);

        if ( ! isset($authenticatorsConfig['authenticators'][ $request->get('authenticator') ])) {
            throw new JwtAuthenticatorException('The Authenticator You Defined is Not Registed in The AuthenticatorsConfig File');
        }

        $this->authenticator = app(
                $authenticatorsConfig['authenticators'][ $request->get('authenticator') ]
            );
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool|\Illuminate\Http\Response
     */
    public function check() {

        if ($this->user !== null) {
            return true;
        }

        $response   = app(Response::class);
        $request    = app(Request::class);

        if (is_array($tokenPayload = $this->authenticator->isTokenValid( $request ))) {
            return $response->header('Authorization', 'Bearer '. $this->authenticator->generateToken($tokenPayload) );
        }

        if (is_array( $refreshToken =  $this->authenticator->isRefreshTokenValid($request) )) {

           $this->authenticator->revokeRefreshToken($request);
           $this->authenticator->generateRefreshToken(false, $refreshToken['user_id']);

            return $response->header(
                    'Authorization',
                    'Bearer '. $this->authenticator->generateToken( User::find($refreshToken['user_id'])->toArray() )
                );
        }

       $this->user = null;
  
       return false;
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest() {
        return ! $this->check();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user() {
        return $this->user;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id() {
        return $this->user() === null ? null : $this->user()->id;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = []) {

        $password = $credentials['password'];
        unset($credentials['password']);

        $user = User::firstWhere($credentials);
        
        if ( ! Hash::check($password, $user->password)) {
            return false;
        }
        
        $this->user = $user;
        unset($user);
        
        $request    = app(Request::class);
        $response   = app(Response::class);

        $this->authenticator->generateRefreshToken(
            $request->bool('remember_me', false), $this->user->id
        );
        
        return $response->header(
            'Authorization',
            'Bearer '. $this->authenticator->generateToken( $this->user->toArray() )
        );
    }

    /**
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser() {
        return $this->user() !== null;
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function setUser(Authenticatable $user): void {
        $this->user = $user;
    }

    public function logout(): void {
        $this->authenticator->revokeRefreshToken(app(Request::class));
    }
}
