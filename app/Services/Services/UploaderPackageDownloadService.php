<?php

declare(strict_types=1);

namespace App\Services\Services;

use App\ValueObjects\Uploader\UploaderPackageValue;
use Illuminate\Contracts\Filesystem\Filesystem;

class UploaderPackageDownloadService
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function getDownloadResponse(UploaderPackageValue $package)
    {
        return $this->filesystem->download($package->getSourcePath(), $package->getExecutableFileName());
    }
}
