<?php

declare(strict_types=1);

namespace App\Domain\Dives\ValueObjects;

use Webmozart\Assert\Assert;

final class TankPressures
{
    public const PRESSURE_TYPES = ['bar', 'psi'];

    public function __construct(
        private string $type,
        private ?int $begin,
        private ?int $end,
    ) {
        Assert::inArray($type, self::PRESSURE_TYPES);
        if ($begin !== null && $end !== null) {
            Assert::greaterThanEq($begin, $end);
        }
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getBegin(): ?int
    {
        return $this->begin;
    }

    public function getEnd(): ?int
    {
        return $this->end;
    }
}
