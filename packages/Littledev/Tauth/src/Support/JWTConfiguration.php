<?php

declare(strict_types=1);


namespace Littledev\Tauth\Support;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;

class JWTConfiguration
{
    private Key $key;

    private Signer $signer;

    private string $issuer;

    private \DateInterval $validFor;

    private string $audience;

    public static function createSigner(string $signer): Signer
    {
        switch (mb_strtolower($signer)) {
            case 'hs256':
            case 'hmac-sha256':
                return new \Lcobucci\JWT\Signer\Hmac\Sha256();
            default:
                throw new \InvalidArgumentException("Invalid signer ");
        }
    }

    public function getSigner(): Signer
    {
        return $this->signer;
    }

    public function getKey(): Key
    {
        return $this->key;
    }

    public function getIssuer(): string
    {
        return $this->issuer;
    }

    public function getValidFor(): \DateInterval
    {
        return $this->validFor;
    }

    public function getAudience()
    {
        return $this->audience;
    }

    public function setKey(string $key): self
    {
        $this->key = InMemory::plainText($key);
        return $this;
    }

    public function setSigner(string $signer): self
    {
        $this->signer = self::createSigner($signer);
        return $this;
    }

    public function setIssuer(string $issuer): self
    {
        $this->issuer = $issuer;
        return $this;
    }

    public function setLifetime(string $validFor, bool $inverted = false): self
    {
        $this->validFor = new \DateInterval($validFor);
        $this->validFor->invert = $inverted;
        return $this;
    }

    public function setAudience(string $audience): self
    {
        $this->audience = $audience;
        return $this;
    }
}
