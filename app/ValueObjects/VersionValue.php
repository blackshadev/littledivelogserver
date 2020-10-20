<?php


namespace App\ValueObjects;


class VersionValue
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
        preg_match('/v?(\d+)\.(\d+)\.(\d+)/', $value, $matches, PREG_OFFSET_CAPTURE);

        return new self((int)$matches[1], (int)$matches[2], (int)$matches[3]);
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
        return 'v' . $this->major . '.' . $this->minor . '.' . $this->patch;
    }
}
