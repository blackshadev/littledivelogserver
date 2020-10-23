<?php

declare(strict_types=1);

namespace App\ValueObjects\Uploader;

class UploaderPackageValue
{
    public const BASE_FILENAME = 'dive-uploader-installer';

    public const SUFFIX_UNIX = '-unix';

    public const SUFFIX_WIN32 = '-win32.exe';

    private VersionValue $version;

    private PlatformValue $platform;

    private string $sourcePath;

    public function __construct(VersionValue $version, PlatformValue $platform, string $sourcePath)
    {
        $this->version = $version;
        $this->platform = $platform;
        $this->sourcePath = $sourcePath;
    }

    public static function getPlatformFileSuffix(PlatformValue $platformValue)
    {
        switch ((string) $platformValue) {
            case (string) PlatformValue::unix(): return self::SUFFIX_UNIX;
            case (string) PlatformValue::win32(): return self::SUFFIX_WIN32;
        }

        throw new \UnexpectedValueException('Unexpected platform value ' . $platformValue);
    }

    public function getVersion(): VersionValue
    {
        return $this->version;
    }

    public function getPlatform(): PlatformValue
    {
        return $this->platform;
    }

    public function getExecutableFileName(): string
    {
        return self::BASE_FILENAME . self::getPlatformFileSuffix($this->platform);
    }

    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }
}
