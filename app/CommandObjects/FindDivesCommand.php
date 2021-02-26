<?php

declare(strict_types=1);

namespace App\CommandObjects;

use Carbon\Carbon;

class FindDivesCommand
{
    private int $userId;

    private int $limit = 50;

    private ?string $keywords = null;

    private ?Carbon $before = null;

    private ?Carbon  $after = null;

    /** @var int[] $buddies  */
    private ?array $buddies = null;

    /** @var int[] $tags  */
    private ?array $tags = null;

    private ?int $placeId = null;

    private function __construct()
    {
    }

    public static function forUser(int $userId, array $data = []): self
    {
        $cmd = self::fromArray($data);
        $cmd->userId = $userId;

        return $cmd;
    }

    public static function fromArray(array $data): self
    {
        $cmd = new self();

        $cmd->setKeywords($data['keywords'] ?? null);
        $cmd->setAfter($data['date_after'] ?? null);
        $cmd->setBefore($data['date_before'] ?? null);
        $cmd->setPlaceId($data['place'] ?? null);
        $cmd->setBuddies($data['buddies'] ?? null);
        $cmd->setTags($data['tags'] ?? null);

        return $cmd;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getBefore(): ?Carbon
    {
        return $this->before;
    }

    public function setBefore($before): void
    {
        if (is_string($before)) {
            $before = new Carbon($before);
        }
        $this->before = $before;
    }

    public function getAfter(): ?Carbon
    {
        return $this->after;
    }

    public function setAfter($after): void
    {
        if (is_string($after)) {
            $after = new Carbon($after);
        }
        $this->after = $after;
    }

    public function getBuddies(): ?array
    {
        return $this->buddies;
    }

    public function setBuddies(?array $buddies): void
    {
        $this->buddies = $buddies;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function getPlaceId(): ?int
    {
        return $this->placeId;
    }

    public function setPlaceId($placeId): void
    {
        if (is_string($placeId)) {
            $placeId = (int)$placeId;
        }

        $this->placeId = $placeId;
    }
}
