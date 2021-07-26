<?php

declare(strict_types=1);

namespace App\Domain\Places\Entities;

final class Place
{
    public function __construct(
        private ?int $id,
        private ?int $createdBy,
        private string $name,
        private string $countryCode,
    ) {
    }

    public static function new(
        int $createdBy,
        string $name,
        string $countryCode
    ) {
        return new self(
            id: null,
            createdBy: $createdBy,
            name: $name,
            countryCode: $countryCode,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function isExisting(): bool
    {
        return $this->id !== null;
    }
}
