<?php

declare(strict_types=1);

namespace App\Application\Places\CommandObjects;

final class FindPlaceCommand
{
    public function __construct(
        private ?string $keywords,
        private ?string $country,
        private ?int $userId,
    ) {
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
