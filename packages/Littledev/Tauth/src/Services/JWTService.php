<?php

declare(strict_types=1);


namespace Littledev\Tauth\Services;

use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validator;
use Littledev\Tauth\Contracts\RefreshTokenInterface;
use Littledev\Tauth\Domain\JWT\JWTToken;
use Littledev\Tauth\Errors\TokenExpiredException;
use Littledev\Tauth\Support\JWTConfiguration;

final class JWTService implements JWTServiceInterface
{
    public const TOKEN_CLAIM = 'tok';

    public const SUBJECT_CLAIM = 'sub';

    private JWTConfiguration $configuration;

    private Parser $parser;

    private Validator $validator;

    private array $constraints;

    public function __construct(JWTConfiguration $configuration)
    {
        $this->setConfiguration($configuration);
    }

    public function setConfiguration(JWTConfiguration $configuration): void
    {
        $this->configuration = $configuration;

        $config = Configuration::forSymmetricSigner(
            $this->configuration->getSigner(),
            $this->configuration->getKey(),
        );
        $this->parser = $config->parser();
        $this->validator = $config->validator();

        $this->constraints = [
             new SignedWith($this->configuration->getSigner(), $this->configuration->getKey()),
             new PermittedFor($this->configuration->getAudience()),
             new IssuedBy($this->configuration->getIssuer()),
             new LooseValidAt(SystemClock::fromUTC()),
        ];
    }

    public function createTokenFor(RefreshTokenInterface $refreshToken): Token
    {
        if ($refreshToken->isExpired()) {
            throw new TokenExpiredException('Refresh token expired');
        }

        $builder = (new JWTToken($this->configuration->getKey(), $this->configuration->getSigner()))
            ->setIssuer($this->configuration->getIssuer())
            ->setAudience($this->configuration->getAudience())
            ->setExpiredAfter($this->configuration->getValidFor())
            ->setSubject($refreshToken->getJWTSubject())
            ->setClaim(self::TOKEN_CLAIM, $refreshToken->getToken());

        foreach ($refreshToken->getJWTExtraClaims() as $key => $value) {
            $builder->setClaim($key, $value);
        }

        return $builder->toToken();
    }

    public function isValid(Token $token): bool
    {
        return $this->validator->validate($token, ...$this->constraints);
    }

    public function parse(string $jwt): UnencryptedToken
    {
        return $this->parser->parse($jwt);
    }

    public function getRefreshToken(UnencryptedToken $token): string
    {
        return $token->claims()->get(self::TOKEN_CLAIM);
    }

    public function getSubjectKey(Token $token)
    {
        return $token->claims()->get(self::SUBJECT_CLAIM);
    }

    public function isJWT(string $jwt): bool
    {
        try {
            $this->parser->parse($jwt);
        } catch (\Throwable $err) {
            return false;
        }

        return true;
    }
}
