<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Repositories;

use App\Helpers\Equality\Equality;
use App\Services\Repositories\UploaderPackageRepository;
use App\ValueObjects\Uploader\PlatformValue;
use App\ValueObjects\Uploader\VersionValue;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class UploaderPackageRepositoryTest extends TestCase
{
    use WithFaker;

    public const AVAILABLE_VERSIONS = [
        'v0.4.1' => ['dive-uploader-installer-unix', 'dive-uploader-installer-win32.exe'],
        'v0.0.2' => ['dive-uploader-installer-win32.exe', 'nop.exe'],
        'v1.2.3' => ['dive-uploader-installer-unix'],
        'v0.1.0' => ['.nop'],
    ];

    /** @var MockInterface|Filesystem */
    private $filesystem;

    private UploaderPackageRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = Mockery::mock(Filesystem::class);
        $this->subject = new UploaderPackageRepository($this->filesystem);
    }

    public function testItListsAvailablePackageVersions()
    {
        $this->expectListing();

        foreach (self::AVAILABLE_VERSIONS as $dir => $fileFiles) {
            $this->filesystem->expects('files')
                ->with((string) $dir)
                ->andReturn($fileFiles);
        }

        $availableVersions = $this->subject->listVersions();
        $versionOrder = array_map(fn ($v) => (string) $v->getVersion(), $availableVersions);

        self::assertSame(['v1.2.3', 'v0.4.1', 'v0.0.2'], $versionOrder);
        self::assertArrayEquality([PlatformValue::unix()], $availableVersions[0]->getPlatforms());
        self::assertArrayEquality([PlatformValue::unix(), PlatformValue::win32()], $availableVersions[1]->getPlatforms());
        self::assertArrayEquality([PlatformValue::win32()], $availableVersions[2]->getPlatforms());
    }

    public function testItGetsLatest()
    {
        $this->expectListing();
        $this->filesystem->expects('files')
                ->with('v1.2.3')
                ->andReturn(self::AVAILABLE_VERSIONS['v1.2.3']);

        $latest = $this->subject->getLatest(PlatformValue::unix());

        self::assertEquality(VersionValue::fromString('v1.2.3'), $latest->getVersion());
        self::assertEquality(PlatformValue::unix(), $latest->getPlatform());
    }

    public function testItReturnsNullOnUnsupportedPlatform()
    {
        $this->expectListing();
        $this->filesystem->expects('files')
                ->with('v1.2.3')
                ->andReturn(self::AVAILABLE_VERSIONS['v1.2.3']);

        $latest = $this->subject->getLatest(PlatformValue::win32());

        self::assertNull($latest);
    }

    public function testItFindsVersion()
    {
        $this->filesystem->expects('files')
            ->with('v1.2.3')
            ->andReturn(['dive-uploader-installer-unix']);

        $uploader = $this->subject->find(
            VersionValue::fromString('v1.2.3'),
            PlatformValue::fromString('unix')
        );

        self::assertEquality(PlatformValue::unix(), $uploader->getPlatform());
        self::assertEquality(VersionValue::fromString('v1.2.3'), $uploader->getVersion());
    }

    public function testItFindReturnNullOnInvalidVersion()
    {
        $this->filesystem->expects('files')
            ->with('v10.2.3')
            ->andReturn([]);

        $uploader = $this->subject->find(
            VersionValue::fromString('v10.2.3'),
            PlatformValue::fromString('unix')
        );

        self::assertNull($uploader);
    }

    private function expectListing()
    {
        $this->filesystem->expects('directories')
            ->withNoArgs()
            ->andReturn(array_keys(self::AVAILABLE_VERSIONS));
    }

    /**
     * @param Equality[] $expected
     * @param Equality[] $actual
     */
    private static function assertArrayEquality(array $expected, array $actual)
    {
        for ($iX = 0, $iXMax = count($expected); $iX < $iXMax; $iX++) {
            self::assertEquality($expected[$iX], $actual[$iX]);
        }
    }

    private static function assertEquality(Equality $expected, Equality $actual)
    {
        self::assertTrue(
            $expected->isEqualTo($actual),
            ((string) $expected) . ' does not equal to ' . ((string) $actual)
        );
    }
}
