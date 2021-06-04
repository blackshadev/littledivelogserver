<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Uploader;

class AvailablePackageVersionValue
{
    private VersionValue $version;

    /** @var PlatformValue[] */
    private array $platforms;

    /**
     * @param PlatformValue[] $platforms
     */
    public function __construct(VersionValue $version, array $platforms = [])
    {
        $this->version = $version;
        $this->platforms = $platforms;
    }

    public function getVersion(): VersionValue
    {
        return $this->version;
    }

    public function getPlatforms(): array
    {
        return $this->platforms;
    }

    public function addPlatform(PlatformValue $platform)
    {
        $this->platforms[] = $platform;
    }
}
