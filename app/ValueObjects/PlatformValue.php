<?php


namespace App\ValueObjects;


class PlatformValue
{
    private const WIN32 = "win32";
    private const UNIX = "unix";

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

    public static function fromString(string $value): self
    {
        switch(strtolower($value)) {
            case 'win32': return self::win32();
            case 'unix': return self::unix();
        }
        throw new \UnexpectedValueException("Unknown string value {$value}");
    }

    public function isEqualTo(self $other): bool
    {
        return $this->value = $other->value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
