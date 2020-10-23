<?php


namespace App\ValueObjects\Uploader;

use App\Helpers\Equality\Equality;

class PlatformValue implements Equality
{
    private const WIN32 = "win32";
    private const UNIX = "unix";
    private const UNKNOWN = "unknown";

    private string $value;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public static function unix(): self
    {
        return new self(self::UNIX);
    }

    public static function win32(): self
    {
        return new self(self::WIN32);
    }

    public static function unknown(): self
    {
        return new self(self::UNKNOWN);
    }

    public static function fromString(string $value): self
    {
        switch(strtolower($value)) {
            case 'win32': return self::win32();
            case 'unix': return self::unix();
            default: return self::unknown();
        }
    }

    /**
     * Check if two platform values are equal.
     * With exception of the unknown, which always return false to equality
     */
    public function isEqualTo($other): bool
    {
        return $other instanceof self &&
            !$this->isUnknown() &&
            $this->value === $other->value;
    }

    public function isUnknown(): bool
    {
        return $this->value === self::UNKNOWN;
    }

    public function __toString()
    {
        return $this->value;
    }
}
