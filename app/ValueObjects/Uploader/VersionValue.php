<?php

namespace App\ValueObjects\Uploader;

use App\Helpers\Equality\Equality;

class VersionValue implements Equality
{
    private int $major;
    private int $minor;
    private int $patch;

    public function __construct(int $major, int $minor, int $patch)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
    }

    public static function fromString(string $value): self
    {
        preg_match('/v?(\d+)\.(\d+)\.(\d+)/', $value, $matches);

        return new self((int) $matches[1], (int) $matches[2], (int) $matches[3]);
    }

    public function getMajor(): int
    {
        return $this->major;
    }

    public function getMinor(): int
    {
        return $this->minor;
    }

    public function getPatch(): int
    {
        return $this->patch;
    }

    public function __toString()
    {
        return 'v'.$this->major.'.'.$this->minor.'.'.$this->patch;
    }

    public function compare(self $value): int
    {
        if ($this->major !== $value->major) {
            return $this->major - $value->major;
        }
        if ($this->minor !== $value->minor) {
            return $this->minor - $value->minor;
        }

        return $this->patch - $value->patch;
    }

    public function isEqualTo($other): bool
    {
        return $other instanceof self &&
            $this->major === $other->major &&
            $this->minor === $other->minor &&
            $this->patch === $other->patch;
    }
}
