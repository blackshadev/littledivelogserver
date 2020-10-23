<?php

declare(strict_types=1);

namespace Tests\Integration\Api;

use App\Http\Controllers\Api\UploaderPackageController;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploaderPackageControllerTest extends TestCase
{
    private $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = Storage::fake('uploader');

        $this->filesystem->makeDirectory('v0.1.0');
        $this->filesystem->makeDirectory('v1.2.3');
        $this->filesystem->put('v1.2.3/dive-uploader-installer-unix', 'test');
    }

    public function testItDownloadsLatestFile()
    {
        $resp = $this->get(action([UploaderPackageController::class, 'latest'], ['unix']))
            ->assertStatus(200)
            ->assertHeader('Content-Disposition', 'attachment; filename=dive-uploader-installer-unix');
        $content = $resp->streamedContent();
        self::assertEquals('test', $content);
    }

    public function testItDownloadsVersion()
    {
        $resp = $this->get(action([UploaderPackageController::class, 'download'], ['v1.2.3', 'unix']))
            ->assertStatus(200)
            ->assertHeader('Content-Disposition', 'attachment; filename=dive-uploader-installer-unix');
        $content = $resp->streamedContent();
        self::assertEquals('test', $content);
    }

    public function testIt404OnInvalidPlatform()
    {
        $this->get(action([UploaderPackageController::class, 'latest'], ['bla']))
            ->assertStatus(404);
    }

    public function testIt404OnUnsupportedPlatform()
    {
        $this->get(action([UploaderPackageController::class, 'latest'], ['win32']))
            ->assertStatus(404);
    }

    public function testIt404OnInvalidVersion()
    {
        $this->get(action([UploaderPackageController::class, 'download'], ['noop', 'unix']))
            ->assertStatus(404);
    }

    public function testIt404OnUnsupportedVersion()
    {
        $this->get(action([UploaderPackageController::class, 'download'], ['v0.0.0', 'unix']))
            ->assertStatus(404);
    }
}
