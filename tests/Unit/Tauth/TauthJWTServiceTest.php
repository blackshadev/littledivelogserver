<?php

declare(strict_types=1);

namespace Tests\Unit\Tauth;

use App\Models\RefreshToken;
use Illuminate\Foundation\Testing\WithFaker;
use InvalidArgumentException;
use Littledev\Tauth\Errors\TokenExpiredException;
use Littledev\Tauth\Services\JWTService;
use Littledev\Tauth\Services\JWTServiceInterface;
use Littledev\Tauth\Support\JWTConfiguration;
use Mockery\Mock;
use Tests\TestCase;

final class TauthJWTServiceTest extends TestCase
{
    use WithFaker;

    private JWTServiceInterface $jwtService;

    /** @var Mock|JWTConfiguration */
    private $configuration;

    private string $key;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new JWTConfiguration();
        $this->configuration->setSigner('hs256');
        $this->configuration->setKey($this->faker->password(32, 32));
        $this->configuration->setAudience('http://not.so.localhost');
        $this->configuration->setIssuer('http://localhost/');
        $this->configuration->setLifetime('PT5M');

        $this->jwtService = new JWTService($this->configuration);
    }

    public function testInvalidSignerInConfig(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->configuration->setSigner('invalid');
    }

    public function testCreateValidJwt(): void
    {
        /** @var RefreshToken $refresh */
        $refresh = RefreshToken::factory()->makeOne();

        $token = $this->jwtService->createTokenFor($refresh);

        self::assertEquals($refresh->id, $this->jwtService->getRefreshToken($token));
        self::assertEquals($refresh->user_id, $this->jwtService->getSubjectKey($token));
        self::assertTrue($this->jwtService->isValid($token));
    }

    public function testInvalidSignerJwt(): void
    {
        /** @var RefreshToken $refresh */
        $refresh = RefreshToken::factory()->makeOne();

        $token = $this->jwtService->createTokenFor($refresh);

        $this->configuration->setKey($this->faker->password(32, 32));
        $this->jwtService->setConfiguration($this->configuration);

        self::assertFalse($this->jwtService->isValid($token));
    }

    public function testExpiredJwt(): void
    {
        /** @var RefreshToken $refresh */
        $refresh = RefreshToken::factory()->makeOne();

        $this->configuration->setLifetime('PT5M', true);
        $token = $this->jwtService->createTokenFor($refresh);

        self::assertFalse($this->jwtService->isValid($token));
    }

    public function testExpiredRefreshToken(): void
    {
        /** @var RefreshToken $refresh */
        $refresh = RefreshToken::factory()->expired()->makeOne();

        $this->expectException(TokenExpiredException::class);
        $this->jwtService->createTokenFor($refresh);
    }
}
