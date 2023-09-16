<?php

declare(strict_types=1);

namespace App\Application\Dives\DataTransferObjects;

use App\Application\Buddies\DataTransferObjects\BuddyData;
use App\Application\Equipment\DataTransferObjects\TankData;
use App\Application\Places\DataTransferObjects\PlaceData;
use App\Application\Tags\DataTransferObjects\TagData;
use Carbon\Carbon;
use Carbon\CarbonInterface;

final class DiveData
{
    /**
     * @param CarbonInterface|null $date
     * @param int|null $divetime
     * @param float|null $maxDepth
     * @param int|null $computerId
     * @param string|null $fingerprint
     * @param PlaceData $placeData
     * @param array<TankData>|null $tanks
     * @param array<TagData>|null $tags
     * @param array<BuddyData>|null $buddies
     * @param array|null $samples
     */
    public function __construct(
        public readonly ?CarbonInterface $date,
        public readonly ?int $divetime,
        public readonly ?float $maxDepth,
        public readonly ?int $computerId,
        public readonly ?string $fingerprint,
        public readonly PlaceData $placeData,
        public readonly ?array $tanks,
        public readonly ?array $tags,
        public readonly ?array $buddies,
        public readonly ?array $samples,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new static(
            date: Carbon::parse($data['date']) ?? null,
            divetime: $data['divetime'] ?? null,
            maxDepth: $data['max_depth'] ?? null,
            computerId: $data['computer_id'] ?? null,
            fingerprint: $data['fingerprint'] ?? null,
            placeData: PlaceData::fromArray($data['place'] ?? []),
            tanks: array_map(fn ($tank) => TankData::fromArray($tank), $data['tanks'] ?? []),
            tags: array_map(fn ($tagData) => TagData::fromArray($tagData), $data['tags'] ?? []),
            buddies: array_map(fn ($buddyData) => BuddyData::fromArray($buddyData), $data['buddies'] ?? []),
            samples: $data['samples'] ?? null
        );
    }
}
