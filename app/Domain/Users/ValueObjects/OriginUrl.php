<?php

declare(strict_types=1);

namespace App\Domain\Users\ValueObjects;

final class OriginUrl
{
    private function __construct(private string $url, private ?string $message = null)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Invalid origin url given: It is not a valid URL.");
        }
        if (str_contains($url, '?')) {
            throw new \InvalidArgumentException("Invalid origin url given: It should not include a query string");
        }
    }

    public static function fromString(string $url): self
    {
        return new self($url);
    }

    public function withMessage(string $message): self
    {
        $self = clone $this;

        $self->message = $message;

        return $self;
    }

    public function toString(): string
    {
        return $this->url . ($this->message !== null ? '?message=' . $this->message : '');
    }
}
