<?php

declare(strict_types=1);

namespace Tests\Integration\Services\Repositories;

use App\Domain\Buddies\Entities\Buddy;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Entities\DiveTank;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Dives\ValueObjects\DiveId;
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
use DateTimeImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\WithFakeTAuthentication;

final class EloquentDiveRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use WithFakeTAuthentication;

    private DiveRepository $subject;

    private UserModel $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->app->make(DiveRepository::class);

        $this->user = UserModel::factory()->createOne();

        $this->fakedTauth();
        $this->fakeAccessTokenFor($this->user);
    }

    public function testItFindsFullDive(): void
    {
        TagModel::factory()->count(10)->for($this->user)->create();
        BuddyModel::factory()->count(10)->for($this->user)->create();
        ComputerModel::factory()->count(5)->for($this->user)->create();

        /** @var DiveModel $diveModel */
        $diveModel = DiveModel::factory()
            ->for($this->user)
            ->filled()
            ->withComputer()
            ->createOne();

        $dive = $this->subject->findById(DiveId::existing($diveModel->id));

        self::assertEquals($this->user->id, $dive->getUserId());
        self::assertTrue($dive->isExisting());
        self::assertEquals($diveModel->id, $dive->getDiveId()->value());
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

    public function testItFindsMinimalDives(): void
    {
        $diveModel = DiveModel::factory()
            ->for($this->user)
            ->createOne();

        $dive = $this->subject->findById(DiveId::existing($diveModel->id));

        self::assertEquals($this->user->id, $dive->getUserId());
        self::assertTrue($dive->isExisting());
        self::assertEquals($diveModel->id, $dive->getDiveId()->value());
    }

    public function testItSavesDive(): void
    {
        $this->fakedTauth();
        $this->fakeAccessTokenFor($this->user);

        $dive = Dive::new(
            userId: $this->user->id,
            maxDepth: 42.42,
            date: new DateTimeImmutable('2020-10-10 10:10:10'),
            divetime: 420,
            place: Place::new($this->user->id, ':place:', 'NL'),
            tanks: [DiveTank::new(
                diveId: null,
                volume: 12,
                pressures: new TankPressures('bar', 221, 51),
                gasMixture: new GasMixture(21)
            )],
            buddies: [Buddy::new($this->user->id, ':buddy:', ':color:', null)],
            tags: [Tag::new($this->user->id, ':tag:', ':color:')],
        );

        self::assertFalse($dive->isExisting());

        $this->subject->save($dive);

        self::assertTrue($dive->isExisting());
        $this->assertDatabaseHas('dives', [
            'id' => $dive->getDiveId()->value(),
            'max_depth' => $dive->getMaxDepth(),
            'divetime' => $dive->getDivetime(),
        ]);
    }

    public function testItRemovesDive(): void
    {
        $model = DiveModel::factory()->for($this->user)->createOne();

        $dive = Dive::existing(
            DiveId::existing($model->id),
            $model->user->id,
            $model->updated_at->toDateTimeImmutable(),
            $model->date->toDateTimeImmutable()
        );

        $this->subject->remove($dive);

        $this->assertDatabaseMissing('dives', ['id' => $dive->getDiveId()->value()]);
    }
}
