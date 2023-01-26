<?php

declare(strict_types=1);

namespace App\Application\Places\CommandObjects;

final class FindPlaceCommand
{
    private function __construct(
        public readonly string $keywords,
        public readonly ?string $country,
        public readonly ?int $userId,
    ) {
    }

    public static function forUser(int $userId, array $data): self
    {
        return new self(
            keywords: $data['keywords'] ?? '',
            country: $data['country'] ?? null,
            userId: $userId,
        );
    }
}
