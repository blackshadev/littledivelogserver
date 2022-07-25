<?php

declare(strict_types=1);

namespace App\Domain\DiveSamples\Entities;

use Webmozart\Assert\Assert;

final class DiveSampleAccessor
{
    private function __construct(
        private array &$raw
    ) {
        Assert::keyExists($raw, Field::Time->value);
    }

    public static function fromArray(array &$data): self
    {
        return new self($data);
    }

    public function has(Field $field): bool
    {
        return isset($this->raw[$field->value]);
    }

    public function get(Field $field): mixed
    {
        return $this->raw[$field->value];
    }

    /**
     * @return iterable<DiveSamplePressureAccessor>
     */
    public function pressures(): iterable
    {
        $pressureList = $this->raw[Field::Pressure->value];
        if (!isset($pressureList[0])) {
            $pressureList = [$pressureList];
        }

        foreach ($pressureList as &$pressure) {
            yield DiveSamplePressureAccessor::fromArray($pressure);
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [...$this->raw];
    }

    /** @return array<string, mixed> */
    public function with(array $extraData): array
    {
        return [...$this->raw, ...$extraData];
    }
}
