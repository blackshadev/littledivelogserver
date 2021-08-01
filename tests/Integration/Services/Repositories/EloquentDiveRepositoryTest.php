<?php

declare(strict_types=1);

namespace Tests\Integration\Services\Repositories;

use App\Domain\Buddies\Entities\Buddy;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Entities\DiveTank;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Dives\ValueObjects\GasMixture;
use App\Domain\Dives\ValueObjects\TankPressures;
use App\Domain\Places\Entities\Place;
use App\Domain\Support\Arrg;
use App\Domain\Tags\Entities\Tag;
use App\Models\Buddy as BuddyModel;
use App\Models\Computer as ComputerModel;
use App\Models\Dive as DiveModel;
use App\Models\Tag as TagModel;
use App\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\WithFakeTAuthentication;

class EloquentDiveRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use WithFakeTAuthentication;

    private DiveRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->app->make(DiveRepository::class);
    }

    public function testItFindsFullDive(): void
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

    public function testItFindsMinimalDives()
    {
        $user = UserModel::factory()->createOne();

        $diveModel = DiveModel::factory()
            ->for($user)
            ->createOne();

        $dive = $this->subject->findById($diveModel->id);

        self::assertEquals($user->id, $dive->getUserId());
        self::assertTrue($dive->isExisting());
        self::assertEquals($diveModel->id, $dive->getDiveId());
    }

    public function testItSavesDive()
    {
        $user = UserModel::factory()->createOne();

        $this->fakedTauth();
        $this->fakeAccessTokenFor($user);

        $userId = $user->id;

        $dive = Dive::new(
            userId: $userId,
            maxDepth: 42.42,
            date: new \DateTimeImmutable('2020-10-10 10:10:10'),
            divetime: 420,
            place: Place::new($userId, ':place:', 'NL'),
            samples: [
                ['Time' => 0, 'Depth' => 1],
                ['Time' => 60, 'Depth' => 6],
                ['Time' => 160, 'Depth' => 1],
            ],
            tanks: [DiveTank::new(
                diveId: null,
                volume: 12,
                pressures: new TankPressures('bar', 221, 51),
                gasMixture: new GasMixture(21)
            )],
            buddies: [Buddy::new($userId, ':buddy:', ':color:', null)],
            tags: [Tag::new($userId, ':tag:', ':color:')],
        );

        self::assertFalse($dive->isExisting());

        $this->subject->save($dive);

        self::assertTrue($dive->isExisting());
        $this->assertDatabaseHas('dives', [
            'id' => $dive->getDiveId(),
            'max_depth' => $dive->getMaxDepth(),
            'divetime' => $dive->getDivetime(),
        ]);
    }
}
