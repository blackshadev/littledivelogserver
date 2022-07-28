<?php

declare(strict_types=1);

namespace Tests\Integration\Services\Repositories;

use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\DiveSamples\DiveSamplesRepository;
use App\Domain\DiveSamples\Entities\DiveSamples;
use App\Error\SaveOperationFailed;
use App\Models\Dive as DiveModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

final class EloquentDiveSamplesRepositoryTest extends TestCase
{
    private DiveSamplesRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->app->make(DiveSamplesRepository::class);
    }

    public function testSaveSavesExistingDiveSamples(): void
    {
        $samples = [['Time' => 1]];
        $model = DiveModel::factory()
            ->createOne();

        self::assertNotEquals($samples, $model->samples);

        $diveSamples = DiveSamples::create(DiveId::existing($model->id), $samples);

        $this->subject->save($diveSamples);

        self::assertEquals($samples, $model->refresh()->samples);
    }

    public function testSaveFailsOnUnknownDive(): void
    {
        $diveSamples = DiveSamples::create(DiveId::existing(-1), []);

        $this->expectException(SaveOperationFailed::class);
        $this->subject->save($diveSamples);

        $this->assertDatabaseMissing('dives', [ 'id' => -1 ]);
    }

    public function testFindByIdFindsSamples(): void
    {
        $samples = [['Time' => 1]];
        $model = DiveModel::factory()
            ->state([ 'samples' => $samples ])
            ->createOne();
        $diveId = DiveId::existing($model->id);

        $diveSamples = DiveSamples::create($diveId, $samples);

        $result = $this->subject->findById($diveId);

        self::assertEquals($diveSamples, $result);
    }

    public function testFindByIdThrowsOnUnknownDive(): void
    {
        $diveId = DiveId::existing(-1);

        $this->expectException(ModelNotFoundException::class);
        $this->subject->findById($diveId);
    }
}
