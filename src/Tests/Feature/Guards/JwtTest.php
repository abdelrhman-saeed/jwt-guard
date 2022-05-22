<?php

namespace abdelrhmanSaeed\JwtGuard\Auth\Tests\Feature\Guards;

use abdelrhmanSaeed\JwtGuard\Auth\Authenticators\Authenticator;
use Tests\TestCase;
use abdelrhmanSaeed\JwtGuard\Auth\Guards\Jwt;
use Illuminate\Support\Facades\Config;
use abdelrhmanSaeed\JwtGuard\Auth\Authenticators\Default\DefaultAuthenticator;
use abdelrhmanSaeed\JwtGuard\Exceptions\JwtAuthenticatorException;
use App\Models\User;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * JwtTest
 * @group group
 */
class JwtTest extends TestCase
{

    private Jwt $jwtGuard;
    private Authenticator|MockInterface $authenticator;
    private Request|MockInterface|MockObject $request;
    private Response|MockInterface $response;
    private string $tokenStr = 'token';
    private string $refreshTokenStr = 'refresh_token';
    private array $refreshToken = [];
    private array $authorizationHeader = [];

    protected function setUp(): void
    {
        parent::setUp();
    
        $this->request          = $this->mock(Request::class);
        $this->authenticator    = $this->mock(DefaultAuthenticator::class);
        $this->response         = $this->mock(Response::class);

        $this->app->instance( Request::class, $this->request );
        $this->app->instance( DefaultAuthenticator::class, $this->authenticator );
        $this->app->instance( Response::class, $this->response );

        $this->authorizationHeader['header'] = 'Authorization';
        $this->authorizationHeader['value']  = 'Bearer ' . $this->tokenStr;

        Config::set('AuthenticatorsConfig', [
            'authenticators' => [ 'default' => DefaultAuthenticator::class ]
        ]);

    }
    

    /** @test */
    public function test_throw_JwtAuthenticatorException_if_the_defined_authenticator_not_found()
    {

        Config::set('AuthenticatorsConfig', []);

        $this->request = $this->mock(Request::class);
        $this->request->shouldReceive('get')->once()->with('authenticator')->andReturn('default');
        
        $this->expectException(JwtAuthenticatorException::class);

        $this->jwtGuard = new Jwt;
    }

    /** @test */
    public function test_constructing_jwt_object()
    {
        $this->request->shouldReceive('get')->twice()->with('authenticator')->andReturn('default');
        $this->jwtGuard = new Jwt;
    }
    
    /** @test */
    public function test_check_if_token_is_valid()
    {
        $this->request->shouldReceive('get')->twice()->with('authenticator')->andReturn('default');

        $this->authenticator->shouldReceive('isTokenValid')->with($this->request)->andReturn([]);
        $this->authenticator->shouldReceive('generateToken')
                                ->with([])
                                ->andReturn($this->tokenStr);

        $this->response->shouldReceive('header')
                            ->with(
                                $this->authorizationHeader['header'],
                                $this->authorizationHeader['value']
                            );

        $this->jwtGuard = new Jwt;
        $this->assertNull($this->jwtGuard->check());
    }

    /** @test */
    public function test_check_if_token_is_invalid_and_refresh_token_is_valid()
    {

        $this->request->shouldReceive('get')->twice()->with('authenticator')->andReturn('default');

        $this->authenticator->shouldReceive('isTokenValid')
                                ->with($this->request)
                                ->andReturnNull();

        $this->refreshToken['user_id'] = ( $user = User::factory()->create() )->id;

        $this->authenticator->shouldReceive('isRefreshTokenValid')
                                ->with($this->request)
                                ->andReturn($this->refreshToken);

        $this->authenticator->shouldReceive('revokeRefreshToken')->with($this->request);
        $this->authenticator->shouldReceive('generateRefreshToken')
                                ->with(false, $this->refreshToken['user_id']);

        $this->authenticator->shouldReceive('generateToken')
                                ->with($user->toArray())
                                ->andReturn($this->tokenStr);

        $this->response->shouldReceive('header')
                            ->with(
                                $this->authorizationHeader['header'],
                                $this->authorizationHeader['value']
                            );

        $this->assertNotFalse((new Jwt)->check());
        
    }

    /** @test */
    public function test_check_if_both_tokens_are_invalid()
    {
        $this->request->shouldReceive('get')->twice()->with('authenticator')->andReturn('default');

        $this->authenticator->shouldReceive('isTokenValid')->with($this->request)->andReturnNull();
        $this->authenticator->shouldReceive('isRefreshTokenValid')->with($this->request)->andReturnNull();

        $this->assertFalse((new Jwt)->check());
    }
    
    /** @test */
    public function test_validate_if_user_credentials_are_invalid()
    {
        $this->request->shouldReceive('get')->twice()->with('authenticator')->andReturn('default');

        $user = User::factory()->create();
        Hash::shouldReceive('check')->with('password', $user->password)->andReturnFalse();

        $this->assertFalse(
            (new Jwt)->validate(['email' => $user->email, 'password' => 'password'])
        );

    }

    /** @test */
    public function test_validate_if_user_credentials_are_valid()
    {
        $this->request->shouldReceive('get')->twice()->with('authenticator')->andReturn('default');

        $user = User::factory()->create();

        Hash::shouldReceive('check')->with('password', $user->password)->andReturnTrue();

        $this->request->shouldReceive('bool')->with('remember_me', false)->andReturnTrue();
        $this->authenticator->shouldReceive('generateRefreshToken')->with(true, $user->id);

        $this->authenticator->shouldReceive('generateToken')
                                ->with( $user->toArray() )
                                ->andReturn($this->tokenStr);

        $this->response->shouldReceive('header')
                            ->with(
                                $this->authorizationHeader['header'],
                                $this->authorizationHeader['value']
                            );

        $this->assertNotFalse((new Jwt)->validate([
            'email' => $user->email,
            'password' => 'password'
        ]));

            
    }
    
    

}
