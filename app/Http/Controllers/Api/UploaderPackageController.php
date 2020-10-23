<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Repositories\UploaderPackageRepository;
use App\Services\Services\UploaderPackageDownloadService;
use App\ValueObjects\Uploader\PlatformValue;
use App\ValueObjects\Uploader\UploaderPackageValue;
use App\ValueObjects\Uploader\VersionValue;
use App\ViewModels\ApiModels\AvailablePackageViewModel;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UploaderPackageController extends Controller
{
    private UploaderPackageRepository $repository;

    private UploaderPackageDownloadService $downloadService;

    public function __construct(UploaderPackageRepository $repository, UploaderPackageDownloadService $downloadService)
    {
        $this->repository = $repository;
        $this->downloadService = $downloadService;
    }

    public function index()
    {
        $all = $this->repository->listVersions();

        return AvailablePackageViewModel::fromArray($all);
    }

    public function latest(string $platformString)
    {
        $platform = PlatformValue::fromString($platformString);

        $uploader = $this->repository->getLatest($platform);

        return $this->downloadUploader($uploader);
    }

    public function download($versionString, $platformString)
    {
        $platform = PlatformValue::fromString($platformString);
        $version = VersionValue::fromString($versionString);

        $uploader = $this->repository->find($version, $platform);

        return $this->downloadUploader($uploader);
    }

    private function downloadUploader(?UploaderPackageValue $uploader)
    {
        if ($uploader === null) {
            throw new NotFoundHttpException('Unable to find package');
        }

        return $this->downloadService->getDownloadResponse($uploader);
    }
}
