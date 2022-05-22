<?php

namespace abdelrhmanSaeed\JwtGuard\Tests\Feature\Tokens\Default\Keys;

use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms\Key;
use abdelrhmanSaeed\JwtGuard\Auth\Tokens\Default\Keys\Algorithms\RS256;
use abdelrhmanSaeed\JwtGuard\Exceptions\TokenKException;
use OpenSSLAsymmetricKey;
use PHPUnit\Framework\TestCase;

/**
 * RS256Test
 * @group group
 */
class RS256Test extends TestCase
{

    private array $config;
    private Key $key;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'token_key' => '/home/abdelrhman/.ssh/id_rsa',
            'passphrase' => 'abdelrhman'
        ];

        $this->key = new RS256($this->config);
    }

    /** @test */
    public function test_get_for_Encoding(): void
    {
        $this->assertInstanceOf(OpenSSLAsymmetricKey::class, $this->key->getForEncoding());
    }

    public function test_get_for_decoding(): void
    {
        $this->assertStringStartsWith('-----BEGIN PUBLIC KEY-----', $this->key->getForDecoding());
    }

    public function test_get_for_Encoding_with_wrongg_passphrase(): void
    {
        $this->config['passphrase'] = '';
        $this->key = new RS256($this->config);

        $this->expectException(TokenKException::class);
        $this->key->getForEncoding();
    }
}
