<?php

namespace abdelrhmanSaeed\JwtGuard\Tests\Feature\Authenticators;

use abdelrhmanSaeed\JwtGuard\Auth\Authenticators\Authenticator;
use abdelrhmanSaeed\JwtGuard\Auth\Authenticators\Default\DefaultAuthenticator;

use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\DefaultToken;

use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms\HS256;
use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms\Key;

use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Token;
use abdelrhmanSaeed\JwtGuard\Exceptions\JwtAuthenticatorException;
use abdelrhmanSaeed\JwtGuard\Models\RefreshToken;
use App\Models\User;
use Firebase\JWT\Key as JWTKey;
use Illuminate\Http\Request;
use Mockery\MockInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * DefaultAuthenticatorTest
 * @group group
 */
class DefaultAuthenticatorTest extends TestCase
{

    private Key|MockInterface $key;
    private Token|MockObject $token;
    private Authenticator|MockInterface $authenticator;
    private Request|MockInterface $request;
    private string $refresh_token = 'refresh_token';
    private string $requestBearerTokenMethodReturnValue = 'bearerToken';

    protected function setUp(): void
    {
        parent::setUp();
    
        $this->token    = $this->createMock(DefaultToken::class);
        $this->key      = $this->mock(HS256::class);
        $this->request  = $this->mock(Request::class);
        
        app()->bind(HS256::class, fn () => $this->key);
        app()->bind(DefaultToken::class, fn () => $this->token);

        $this->authenticator = new DefaultAuthenticator;

    }
    
    
    public function testIsTokenValid(): void
    {
        
        $keyObjectReturnForDecodingValue  = 'decoding';
        $tokenObjectDebugTokenReturnValue = User::factory()->create()->toArray();

        $this->request->shouldReceive('bearerToken')
                            ->andReturn($this->requestBearerTokenMethodReturnValue);
        

        $this->key->shouldReceive('getForDecoding')
                            ->andReturn($keyObjectReturnForDecodingValue);

        $this->token->expects($this->once())
                            ->method('debugToken')
                            ->with(
                                    $this->requestBearerTokenMethodReturnValue,
                                    $this->callback(fn ($attribute) => $attribute instanceof JWTKey)

                            )->willReturn($tokenObjectDebugTokenReturnValue);

        $this->assertSame(
            $tokenObjectDebugTokenReturnValue, $this->authenticator->isTokenValid($this->request)
        );

    }


    /** @test */
    public function test_is_refresh_token_valid()
    {

        $this->request->shouldReceive('cookie')
                        ->with('refresh_token')
                        ->andReturn($this->refresh_token);

                        
        $this->token->expects($this->once())
                        ->method('debugRefreshToken')
                        ->with($this->refresh_token)
                        ->willReturn($refresh_token = ((new RefreshToken())->toArray()));
                        
        $this->assertSame($refresh_token, $this->authenticator->isRefreshTokenValid($this->request));
    }

    /** @test */
    public function test_generate_token()
    {
        $this->token->expects($this->once())
                        ->method('setPayload')
                        ->with($user = [])
                        ->willReturnSelf();


        $this->token->expects($this->once())
                        ->method('generateToken')
                        ->willReturn('token');
                        
        $this->assertSame('token', $this->authenticator->generateToken($user));

    }

    /** @test */
    public function test_generate_token_if_user_param_is_null()
    {
        $this->expectException(JwtAuthenticatorException::class);
        $this->authenticator->generateToken(null);
    }
    
    /** @test */
    public function test_generate_refresh_token()
    {
        $this->token->expects($this->once())
                        ->method('setPayload')->with(['id' => $userID = 1])
                        ->willReturnSelf();

        $this->token->expects($this->once())
                        ->method('generateRefreshToken')
                        ->with($longLives = true)
                        ->willReturn($this->refresh_token);

        $this->assertSame($this->refresh_token, $this->authenticator->generateRefreshToken($longLives, $userID));
    }
    
    /** @test */
    public function test_revoke_refresh_token()
    {
        $this->request->shouldReceive('cookie')
                            ->with('refresh_token')
                            ->andReturn($this->refresh_token);

        $this->token->expects($this->once())
                            ->method('revokeRefreshToken')
                            ->with($this->refresh_token)
                            ->willReturn($revokeTokenReturnValue = true);

        $this->assertSame($revokeTokenReturnValue, $this->authenticator->revokeRefreshToken($this->request));
    }
    
    
    
    
}
