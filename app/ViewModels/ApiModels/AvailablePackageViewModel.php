<?php

namespace App\ViewModels\ApiModels;

use App\Http\Controllers\Api\UploaderPackageController;
use App\ValueObjects\AvailableVersionValue;
use App\ViewModels\ViewModel;

class AvailablePackageViewModel extends ViewModel
{
    protected array $visible = ['version', 'platforms', 'downloads'];
    private AvailableVersionValue $package;

    public static function fromArray(array $arr)
    {
        return array_map(fn ($item) => new self($item), $arr);
    }

    public function __construct(AvailableVersionValue $package)
    {
        $this->package = $package;
    }

    public function getVersion(): string
    {
        return (string) $this->package->getVersion();
    }

    public function getPlatforms(): array
    {
        return array_map(fn ($platform) => (string) $platform, $this->package->getPlatforms());
    }

    public function getDownloads(): array
    {
        return array_map(
            fn ($platform) => [
                'platform' => (string) $platform,
                'downloadLink' => action(
                    [UploaderPackageController::class, 'download'],
                    [(string) $this->package->getVersion(), (string) $platform]
                ),
            ],
            $this->package->getPlatforms()
        );
    }
}
