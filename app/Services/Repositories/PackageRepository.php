<?php


namespace App\Services\Repositories;


use App\DataTransferObjects\PackageData;
use Illuminate\Contracts\Filesystem\Filesystem;

class PackageRepository
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function getPackage(PackageData $packageData)
    {
        
    }

}
