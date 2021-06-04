<?php

declare(strict_types=1);

namespace App\Domain\DataTransferObjects;

use App\Domain\ValueObjects\Uploader\PlatformValue;

class PackageData
{
    private ?string $version;

    private ?string $platform;

    public static function fromArray(array $data): self
    {
        $package = new self();

        $package->version = $data['version'] ?? null;
        $package->platform = isset($data['platform']) ? PlatformValue::fromString($data['platform']) : null;

        return $package;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }
}
