<?php

declare(strict_types=1);

namespace App\CommandObjects;

class FindPlaceCommand
{
    private ?string $keywords;

    private ?string $country;

    public static function fromArray(array $data): self
    {
        $command = new self();
        $command->setCountry($data['country'] ?? null);
        $command->setKeywords($data['keywords'] ?? null);
        return $command;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }
}
