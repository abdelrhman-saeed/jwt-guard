<?php

namespace abdelrhmanSaeed\JwtGuard\Tests\Feature\Tokens\Default;

use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\DefaultToken;
use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms\Key;
use abdelrhmanSaeed\JwtGuard\Exceptions\InvalidTokenConfigException;

use abdelrhmanSaeed\JwtGuard\Database\Models\RefreshToken;
use Facades\Firebase\JWT\JWT;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Firebase\JWT\Key as JWTKey;

use App\Models\User;
use Tests\TestCase;

use Exception;
use stdClass;


class DefaultTokenTest extends TestCase
{

    use RefreshDatabase;

    private array           $config = [];
    private array           $payload = [];
    private Key             $key;
    private DefaultToken    $defaultToken;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'supported_algs' => [
                'HS256' => Algorithms\HS256::class,
                'RS256' => Algorithms\RS256::class,
            ],

            'token_alg'  => 'HS256',
            'token_key'  => 'abdelrhman',
            'passphrase' => 'passphrase',

            'payload' => [
                'iss' => 'www.app.com',
                'aud' => 'www.app.com',
                'iat' => $now = time(),
                'ntb' => $now,
                'exp' => $now + (60 * 15),
            ],

            'refresh_token' => [
                'expiration' => now()->addHours(3),
                'long_expiration' => now()->addMonths(3),
            ]
        ];

        $this->key = $this->mock(Key::class);
        $this->defaultToken = new DefaultToken( $this->config, $this->key );
    }

    public function testGenerateToken(): void
    {

        $this->payload = User::factory()->create()->toArray();
        $this->defaultToken->setPayload($this->payload);

        $this->key->shouldReceive('getForEncoding')
                    ->once()
                    ->andReturn($this->config['token_key']);

        JWT::shouldReceive('encode')
                ->with(
                    array_merge($this->payload, $this->config['payload']),
                    $this->config['token_key'],
                    $this->config['token_alg']
                )->andReturn($token = 'token.token.token');

        $this->assertEquals($token, $this->defaultToken->generateToken());
    }

    public function testGenerateRefreshToken(): void
    {
        $this->defaultToken->setPayload(User::factory()->create()->toArray());

        $this->assertEquals(0, RefreshToken::count());

        $this->defaultToken->generateRefreshToken(true);

        $this->assertEquals(1, RefreshToken::count());
    }

    public function testDebugToken(): void
    {

        $token  = 'token.token.token';
        $jwtkey = $this->mock(JWTKey::class);

        $returnedVal = new stdClass;
        
        $returnedVal->name = 'abdelrhman';
        $returnedVal->otherdata = 'somedata';

        JWT::shouldReceive('decode')->with($token, $jwtkey)->andReturn($returnedVal);

        $this->assertSame((array) $returnedVal, $this->defaultToken->debugToken($token, $jwtkey));
    }

    public function testDebugRefreshToken(): void
    {


        $refresh_token = RefreshToken::factory()->create();
        $this->assertIsArray($this->defaultToken->debugRefreshToken($refresh_token->uuid));

        /**
         * testing after expiring the token's time
         */
        $refresh_token->expire_at = now();
        $refresh_token->save();

        $this->assertNull($this->defaultToken->debugRefreshToken($refresh_token->uuid));
    }

    public function testRevokeRefreshToken(): void
    {
        $refresh_token = RefreshToken::factory()->create();

        $this->assertTrue( now()->lt($refresh_token->expire_at) );
        $this->assertTrue( $this->defaultToken->revokeRefreshToken($refresh_token->uuid) );

        $refresh_token->refresh();
        $this->assertFalse( now()->lt($refresh_token->expire_at) );
    }
}
