<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\Helpers\Equality\EqualityHelpers;
use App\ValueObjects\Uploader\AvailablePackageVersionValue;
use App\ValueObjects\Uploader\PlatformValue;
use App\ValueObjects\Uploader\UploaderPackageValue;
use App\ValueObjects\Uploader\VersionValue;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Str;

class UploaderPackageRepository
{
    private const SOURCE_FILE_PREFIX = 'dive-uploader-installer';

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function getLatest(PlatformValue $platform): ?UploaderPackageValue
    {
        $versions = $this->listVersionsValues();

        $version = $versions[0];

        return $this->find($version, $platform);
    }

    /**
     * @return AvailablePackageVersionValue[]
     */
    public function listVersions(): array
    {
        $versions = $this->listVersionsValues();

        /** @var AvailablePackageVersionValue[] $packages */
        $packages = [];
        foreach ($versions as $version) {
            $platforms = $this->getAvailablePlatforms($version);
            if (count($platforms) > 0) {
                $packages[] = new AvailablePackageVersionValue($version, $platforms);
            }
        }

        return $packages;
    }

    public function find(VersionValue $version, PlatformValue $platform): ?UploaderPackageValue
    {
        $platforms = $this->getAvailablePlatforms($version);

        if (!EqualityHelpers::inArray($platforms, $platform, true)) {
            return null;
        }

        return new UploaderPackageValue($version, $platform, $this->getSourceFilePath($version, $platform));
    }

    /**
     * @return VersionValue[]
     */
    private function listVersionsValues(): array
    {
        $directories = $this->filesystem->directories();

        $versions = array_map(fn ($dir) => VersionValue::fromString($dir), $directories);

        usort($versions, fn ($a, $b) => -$a->compare($b));

        return $versions;
    }

    /**
     * @param VersionValue $version
     * @return PlatformValue[]
     */
    private function getAvailablePlatforms(VersionValue $version): array
    {
        $packagePath = $this->getPackageDirectory($version);
        $files = $this->filesystem->files($packagePath);

        $platformValues = [];
        foreach ($files as $file) {
            $platformValue = $this->getPlatformFromExecutableName($file);
            if ($platformValue !== null) {
                $platformValues[] = $platformValue;
            }
        }

        return $platformValues;
    }

    private function getPackageDirectory(VersionValue $version): string
    {
        return "{$version}";
    }

    private function getSourceFilePath(VersionValue $version, PlatformValue $platform): string
    {
        $dir = $this->getPackageDirectory($version);
        $exe = self::SOURCE_FILE_PREFIX . UploaderPackageValue::getPlatformFileSuffix($platform);

        return "{$dir}/{$exe}";
    }

    private function getPlatformFromExecutableName(string $executableName): ?PlatformValue
    {
        if (Str::endsWith($executableName, UploaderPackageValue::SUFFIX_UNIX)) {
            return PlatformValue::unix();
        }
        if (Str::endsWith($executableName, UploaderPackageValue::SUFFIX_WIN32)) {
            return PlatformValue::win32();
        }

        return null;
    }
}
