<?php


namespace Littledev\Tauth\Services;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Littledev\Tauth\Domain\JWT\JWTToken;
use Littledev\Tauth\Contracts\RefreshTokenInterface;
use Littledev\Tauth\Errors\TokenExpiredException;
use Littledev\Tauth\Support\JWTConfiguration;

class JWTService implements JWTServiceInterface
{
    const TOKEN_CLAIM = 'tok';
    const SUBJECT_CLAIM = 'sub';

    private JWTConfiguration $configuration;

    public function __construct(JWTConfiguration $configuration)
    {
        $this->configuration = $configuration;
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
        return $token->validate($this->getValidator())
            && $token->verify($this->configuration->getSigner(), $this->configuration->getKey());
    }

    public function parse(string $jwt): Token
    {
        return (new Parser())->parse($jwt);
    }

    protected function getValidator(): ValidationData
    {
        $validationData = new ValidationData();
        $validationData->setAudience($this->configuration->getAudience());
        $validationData->setIssuer($this->configuration->getIssuer());
        return $validationData;
    }

    public function getRefreshToken(Token $token): string
    {
        return $token->getClaim(self::TOKEN_CLAIM);
    }

    public function getSubjectKey(Token $token)
    {
        return $token->getClaim(self::SUBJECT_CLAIM);
    }

    public function isJWT(string $jwt): bool
    {
        try {
            (new Parser())->parse($jwt);
        } catch(\Throwable $err) {
            return false;
        }

        return true;
    }
}
