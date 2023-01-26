<?php

declare(strict_types=1);

namespace App\Domain\Support;

use DateTimeImmutable;
use DateTimeInterface;

final class Date
{
    public static function fromString(string $format): DateTimeInterface
    {
        return new DateTimeImmutable($format);
    }

    public static function fromNullableString(?string $format): ?DateTimeInterface
    {
        if ($format === null) {
            return null;
        }

        return self::fromString($format);
    }
}
