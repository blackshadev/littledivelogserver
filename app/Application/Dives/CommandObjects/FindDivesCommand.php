<?php

declare(strict_types=1);

namespace App\Application\Dives\CommandObjects;

use App\Domain\Support\Date;
use DateTimeInterface;
use Webmozart\Assert\Assert;

final class FindDivesCommand
{
    /**
     * @param int[] $buddies
     * @param int[] $tags
     */
    private function __construct(
        public readonly int $userId,
        public readonly string $keywords,
        public readonly ?DateTimeInterface $before,
        public readonly ?DateTimeInterface $after,
        public readonly ?array $buddies,
        public readonly ?array $tags,
        public readonly ?int $placeId,
    ) {
        if ($buddies !== null) {
            Assert::allInteger($buddies);
        }
        if ($tags !== null) {
            Assert::allInteger($tags);
        }
    }

    public static function forUser(int $userId, array $data = []): self
    {
        return new self(
            userId: $userId,
            keywords: $data['keywords'] ?? '',
            before: Date::fromNullableString($data['date_before'] ?? null),
            after: Date::fromNullableString($data['date_after'] ?? null),
            buddies: $data['buddies'] ?? null,
            tags: $data['tags'] ?? null,
            placeId: $data['place'] ?? null,
        );
    }
}
