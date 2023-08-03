<?php

declare(strict_types=1);

namespace Tests\Unit\Tauth;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Token\Signature;
use Littledev\Tauth\Errors\InvalidJWTException;
use Littledev\Tauth\Errors\NoSuchUserException;
use Littledev\Tauth\Services\JWTServiceInterface;
use Littledev\Tauth\Services\TauthRepositoryInterface;
use Littledev\Tauth\Services\TauthService;
use Littledev\Tauth\Services\TauthServiceInterface;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

final class TauthServiceTest extends TestCase
{
    /** @var TauthRepositoryInterface|MockInterface  */
    private TauthRepositoryInterface $repository;

    /** @var JWTServiceInterface|MockInterface  */
    private JWTServiceInterface $jwtService;

    private TauthServiceInterface $tauthService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(TauthRepositoryInterface::class);
        $this->jwtService = Mockery::mock(JWTServiceInterface::class);
        $this->tauthService = new TauthService($this->jwtService, $this->repository);
    }

    public function testNoUser(): void
    {
        self::assertNull($this->tauthService->getUser());
        self::assertFalse($this->tauthService->isAuthenticated());
    }

    public function testCreateRefreshToken(): void
    {
        $user = new User();
        $refresh = new RefreshToken();

        $this->repository->shouldReceive('createRefreshToken')->with($user)->andReturn($refresh);
        $result = $this->tauthService->createRefreshToken($user);

        self::assertSame($refresh, $result);
    }

    public function testCreateAccessToken(): void
    {
        $token = $this->createToken();
        $refresh = new RefreshToken();

        $this->jwtService->shouldReceive('createTokenFor')->with($refresh)->andReturn($token);
        $result = $this->tauthService->createAccessToken($refresh);

        self::assertSame($token, $result);
    }

    public function testRefreshToken(): void
    {
        $strToken = '';
        $refresh = new RefreshToken();

        $this->repository->shouldReceive('isRefreshTokenId')
            ->with($strToken)
            ->andReturnTrue();
        $this->repository->shouldReceive('findValidRefreshToken')
            ->with($strToken)
            ->andReturn($refresh);

        $result = $this->tauthService->validateRefreshToken($strToken);

        self::assertTrue($result);
        self::assertSame($refresh, $this->tauthService->getRefreshToken());
        self::assertFalse($this->tauthService->isAuthenticated());
    }

    public function testInvalidRefreshToken(): void
    {
        $strToken = '';

        $this->repository->shouldReceive('isRefreshTokenId')
            ->with($strToken)
            ->andReturnFalse();

        $result = $this->tauthService->validateRefreshToken($strToken);

        self::assertFalse($result);
        self::assertFalse($this->tauthService->isAuthenticated());
        self::assertNull($this->tauthService->getRefreshToken());
    }

    public function testNonExistingRefreshToken(): void
    {
        $strToken = '';

        $this->repository->shouldReceive('isRefreshTokenId')
            ->with($strToken)
            ->andReturnTrue();
        $this->repository->shouldReceive('findValidRefreshToken')
            ->with($strToken)
            ->andReturnNull();

        $result = $this->tauthService->validateRefreshToken($strToken);

        self::assertFalse($result);
        self::assertFalse($this->tauthService->isAuthenticated());
        self::assertNull($this->tauthService->getRefreshToken());
    }

    public function testValidateValidAccessToken(): void
    {
        $accessToken = '';
        $userKey = '';
        $token = $this->createToken();
        $user = new User();

        $this->repository->shouldReceive('isAccessToken')
            ->with($accessToken)
            ->andReturnTrue();

        $this->jwtService->shouldReceive('parse')
            ->with($accessToken)
            ->andReturn($token);

        $this->jwtService->shouldReceive('isValid')
            ->with($token)
            ->andReturnTrue();

        $this->jwtService->shouldReceive('getSubjectKey')
            ->with($token)
            ->andReturn($userKey);

        $this->repository->shouldReceive('findUserByKey')
            ->with($userKey)
            ->andReturn($user);
        Auth::shouldReceive('setUser')->with($user);

        $result = $this->tauthService->validateAccessToken($accessToken);

        self::assertTrue($result);
        self::assertSame($token, $this->tauthService->getAccessToken());
        self::assertSame($user, $this->tauthService->getUser());
        self::assertTrue($this->tauthService->isAuthenticated());
    }

    public function testValidateBrokenAccessToken(): void
    {
        $accessToken = '';

        $this->repository->shouldReceive('isAccessToken')
            ->with($accessToken)
            ->andReturnFalse();

        $this->expectException(InvalidJWTException::class);
        $result = $this->tauthService->validateAccessToken($accessToken);

        self::assertFalse($result);
        self::assertFalse($this->tauthService->isAuthenticated());
        self::assertNull($this->tauthService->getUser());
        self::assertNull($this->tauthService->getAccessToken());
    }

    public function testValidateInvalidAccessToken(): void
    {
        $accessToken = '';
        $token = $this->createToken();

        $this->repository->shouldReceive('isAccessToken')
            ->with($accessToken)
            ->andReturnTrue();

        $this->jwtService->shouldReceive('parse')
            ->with($accessToken)
            ->andReturn($token);

        $this->jwtService->shouldReceive('isValid')
            ->with($token)
            ->andReturnFalse();

        $this->expectException(InvalidJWTException::class);
        $this->tauthService->validateAccessToken($accessToken);

        self::assertFalse($this->tauthService->isAuthenticated());
        self::assertNull($this->tauthService->getUser());
        self::assertNull($this->tauthService->getAccessToken());
    }

    public function testBreakWhenUserNotFound(): void
    {
        $accessToken = '';
        $userKey = '';
        $token = $this->createToken();

        $this->jwtService->shouldReceive('parse')
            ->with($accessToken)
            ->andReturn($token);

        $this->jwtService->shouldReceive('isValid')
            ->with($token)
            ->andReturnTrue();

        $this->jwtService->shouldReceive('getSubjectKey')
            ->with($token)
            ->andReturn($userKey);

        $this->repository->shouldReceive('isAccessToken')
            ->with($accessToken)
            ->andReturnTrue();

        $this->repository->shouldReceive('findUserByKey')
            ->with($userKey)
            ->andReturnNull();

        $this->expectException(NoSuchUserException::class);
        $this->tauthService->validateAccessToken($accessToken);
    }

    private function createToken(): Token
    {
        return new Plain(
            new DataSet([], 'headers'),
            new DataSet([], 'claims'),
            new Signature('hash', 'signature'),
        );
    }
}
