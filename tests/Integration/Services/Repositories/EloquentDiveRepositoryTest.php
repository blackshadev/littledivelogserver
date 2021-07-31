<?php

declare(strict_types=1);

namespace Tests\Integration\Services\Repositories;

use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Support\Arrg;
use App\Models\Buddy as BuddyModel;
use App\Models\Computer as ComputerModel;
use App\Models\Dive as DiveModel;
use App\Models\Tag as TagModel;
use App\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EloquentDiveRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private DiveRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->app->make(DiveRepository::class);
    }

    public function testItFindsDive(): void
    {
        $user = UserModel::factory()
            ->has(TagModel::factory()->count(10))
            ->has(BuddyModel::factory()->count(10))
            ->has(ComputerModel::factory()->count(10))
            ->create();

        /** @var DiveModel $diveModel */
        $diveModel = DiveModel::factory()
            ->for($user)
            ->filled()
            ->withComputer()
            ->createOne();

        $dive = $this->subject->findById($diveModel->id);

        self::assertEquals($user->id, $dive->getUserId());
        self::assertTrue($dive->isExisting());
        self::assertEquals($diveModel->id, $dive->getDiveId());
        self::assertEquals($diveModel->max_depth, $dive->getMaxDepth());
        self::assertEquals($diveModel->date, $dive->getDate());
        self::assertEquals($diveModel->place_id, $dive->getPlace()->getId());
        self::assertEquals(Arrg::get($diveModel->tags->toArray(), 'id'), Arrg::call($dive->getTags(), 'getId'));
        self::assertEquals(Arrg::get($diveModel->buddies->toArray(), 'id'), Arrg::call($dive->getBuddies(), 'getId'));
        self::assertEquals($diveModel->computer->id, $dive->getComputer()->getId());

        self::assertEquals($diveModel->tanks->first()->volume, $dive->getTanks()[0]->getVolume());
        self::assertEquals($diveModel->tanks->first()->oxygen, $dive->getTanks()[0]->getGasMixture()->getOxygen());
        self::assertEquals($diveModel->tanks->first()->pressure_begin, $dive->getTanks()[0]->getPressures()->getBegin());
        self::assertEquals($diveModel->tanks->first()->pressure_end, $dive->getTanks()[0]->getPressures()->getEnd());
        self::assertEquals($diveModel->tanks->first()->pressure_type, $dive->getTanks()[0]->getPressures()->getType());
    }

    public function testItSavesDive()
    {
    }
}
