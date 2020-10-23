<?php

declare(strict_types=1);

namespace Tests\Unit\Tauth;

use App\Models\RefreshToken;
use Illuminate\Foundation\Testing\WithFaker;
use Littledev\Tauth\Errors\TokenExpiredException;
use Littledev\Tauth\Services\JWTService;
use Littledev\Tauth\Services\JWTServiceInterface;
use Littledev\Tauth\Support\JWTConfiguration;
use Mockery\Mock;
use Tests\TestCase;

class TauthJWTServiceTest extends TestCase
{
    use WithFaker;

    private JWTServiceInterface $jwtService;

    /** @var Mock|JWTConfiguration */
    private $configuration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configuration = \Mockery::mock(JWTConfiguration::class);
        $this->configuration->makePartial();

        $this->configuration->setSigner('hs256');
        $this->configuration->setKey($this->faker->password);
        $this->configuration->setAudience($this->faker->url);
        $this->configuration->setIssuer($this->faker->url);
        $this->configuration->setLifetime('PT5M');

        $this->jwtService = new JWTService($this->configuration);
    }

    public function testInvalidSignerInConfig()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->configuration->setSigner('invalid');
    }

    public function testCreateValidJwt()
    {
        /** @var RefreshToken $refresh */
        $refresh = RefreshToken::factory()->makeOne();

        $token = $this->jwtService->createTokenFor($refresh);

        self::assertEquals($refresh->id, $this->jwtService->getRefreshToken($token));
        self::assertEquals($refresh->user_id, $this->jwtService->getSubjectKey($token));
        self::assertTrue($this->jwtService->isValid($token));
    }

    public function testInvalidSignerJwt()
    {
        /** @var RefreshToken $refresh */
        $refresh = RefreshToken::factory()->makeOne();

        $token = $this->jwtService->createTokenFor($refresh);

        $this->configuration->setKey($this->faker->password);
        self::assertFalse($this->jwtService->isValid($token));
    }

    public function testExpiredJwt()
    {
        /** @var RefreshToken $refresh */
        $refresh = RefreshToken::factory()->makeOne();

        $this->configuration->setLifetime('PT5M', true);
        $token = $this->jwtService->createTokenFor($refresh);

        self::assertFalse($this->jwtService->isValid($token));
    }

    public function testExpiredRefreshToken()
    {
        /** @var RefreshToken $refresh */
        $refresh = RefreshToken::factory()->expired()->makeOne();

        $this->expectException(TokenExpiredException::class);
        $this->jwtService->createTokenFor($refresh);
    }
}
