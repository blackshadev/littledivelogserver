<?php

declare(strict_types=1);

namespace Tests\Integration\Services\Repositories;

use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Factories\Dives\DiveFactory;
use App\Domain\Support\Arrg;
use App\Models\Dive as DiveModel;
use App\Repositories\Dives\EloquentDiveBatchRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use Tests\TestCase;

final class EloquentDiveBatchRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private EloquentDiveBatchRepository $diveBatchRepository;

    private DiveRepository |

 Mockery\MockInterface $diveRepository;

    private DiveFactory |

 Mockery\MockInterface $diveFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->diveRepository = app()->make(DiveRepository::class);
        $this->diveFactory = Mockery::mock(DiveFactory::class);
        $this->diveBatchRepository = new EloquentDiveBatchRepository(
            $this->diveFactory,
            $this->diveRepository
        );
    }

    public function testItFindsByIds(): void
    {
        $times = 10;
        $userId = 1;
        $models = DiveModel::factory()->count($times)->create([
            'user_id' => $userId,
        ])->toArray();
        $input = Arrg::get($models, 'id');

        $this->diveFactory->expects('createFrom')
            ->times($times)
            ->withArgs(fn ($model) => in_array($model->id, $input, true))
            ->andReturnUsing(fn (DiveModel $model) => $this->createExistingDiveFromModel($model));

        $result = $this->diveBatchRepository->findByIds($input);

        self::assertCount($times, $result);
        self::assertContainsOnlyInstancesOf(Dive::class, $result);
        /** @var Dive $dive */
        foreach ($result as $dive) {
            self::assertEquals($userId, $dive->getUserId());
            self::assertContains($dive->getDiveId(), $input);
        }
    }

    public function testItFindsEmptyArray(): void
    {
        $result = $this->diveBatchRepository->findByIds([]);
        self::assertEmpty($result);
    }

    public function testItReplacesDive(): void
    {
        $times = 10;
        $dives = $this->createExistingDives($times);
        $newDive = Dive::new(
            userId: $dives[0]->getUserId(),
            date: new \DateTimeImmutable('2020-10-10 10:10:10'),
            divetime: 42,
            maxDepth: 10.5,
        );

        self::assertFalse($newDive->isExisting());

        $this->diveBatchRepository->replace($dives, $newDive);

        /** @var Dive $dive */
        foreach ($dives as $dive) {
            $this->assertDatabaseMissing('dives', ['id' => $dive->getDiveId() ]);
        }

        self::assertTrue($newDive->isExisting());
        $this->assertDatabaseHas('dives', ['id' => $newDive->getDiveId()]);
    }

    private function createExistingDives(int $times): array
    {
        $models = DiveModel::factory()->count($times)->create();

        return $models
            ->map(fn (DiveModel $model) => $this->createExistingDiveFromModel($model))
            ->toArray();
    }

    private function createExistingDiveFromModel(DiveModel $model): Dive
    {
        return Dive::existing(
            diveId: $model->id,
            userId: $model->user_id,
            date: $model->date,
        );
    }
}
