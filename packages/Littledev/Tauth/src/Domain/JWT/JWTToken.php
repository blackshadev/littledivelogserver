<?php

declare(strict_types=1);


namespace Littledev\Tauth\Domain\JWT;

use Carbon\Carbon;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;

class JWTToken
{
    private string $subject;

    private string $issuer;

    private string $audience;

    private Carbon $expiresAt;

    private Carbon $issuedAt;

    private \stdClass $claims;

    private Key $key;

    private Signer $signer;

    private Builder $builder;

    public function __construct(Key $key, Signer $signer)
    {
        $config = Configuration::forSymmetricSigner(
            $signer,
            $key
        );
        $this->builder = $config->builder();
        $this->key = $key;
        $this->signer = $signer;
        $this->issuedAt = Carbon::now();
        $this->claims = new \stdClass();
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getIssuer(): string
    {
        return $this->issuer;
    }

    public function setIssuer(string $issuer): self
    {
        $this->issuer = $issuer;
        return $this;
    }

    public function getAudience(): string
    {
        return $this->audience;
    }

    public function setAudience(string $audience): self
    {
        $this->audience = $audience;
        return $this;
    }

    public function getExpiresAt(): Carbon
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(Carbon $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function setExpiredAfter(\DateInterval $seconds): self
    {
        $this->expiresAt = $this->issuedAt->copy()->add($seconds);
        return $this;
    }

    public function getIssuedAt(): Carbon
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(Carbon $issuedAt): self
    {
        $this->issuedAt = $issuedAt;
        return $this;
    }

    public function setClaim(string $claim, $value): self
    {
        $this->claims->$claim = $value;
        return $this;
    }

    public function getClaim(string $claim)
    {
        return $this->claims->$claim ?? null;
    }

    public function getClaims(): \stdClass
    {
        return clone $this->claims;
    }

    public function toToken(): Token
    {
        $builder = $this->builder
            ->issuedAt($this->getIssuedAt()->toDateTimeImmutable())
            ->issuedBy($this->issuer)
            ->expiresAt($this->getExpiresAt()->toDateTimeImmutable())
            ->permittedFor($this->audience)
            ->relatedTo($this->subject);

        foreach ($this->claims as $claim => $value) {
            $builder->withClaim($claim, $value);
        }

        return $builder->getToken($this->signer, $this->key);
    }
}
