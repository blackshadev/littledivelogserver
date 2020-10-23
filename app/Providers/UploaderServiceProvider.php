<?php

namespace App\Providers;

use App\Services\Repositories\UploaderPackageRepository;
use App\Services\Services\UploaderPackageDownloadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class UploaderServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $uploaderDiskName = env("UPLOADER_DISK_NAME", "uploader");
        $this->app->bind(UploaderPackageRepository::class, function () use ($uploaderDiskName) {
            return new UploaderPackageRepository(Storage::disk($uploaderDiskName));
        });
        $this->app->bind(UploaderPackageDownloadService::class, function () use ($uploaderDiskName) {
            return new UploaderPackageDownloadService(Storage::disk($uploaderDiskName));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
