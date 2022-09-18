<?php

declare(strict_types=1);

namespace Tests\Integration\Services\Repositories;

use App\Domain\Computers\Entities\Computer;
use App\Domain\Users\Entities\User;
use App\Domain\Users\ValueObjects\OriginUrl;
use App\Models\Computer as ComputerModel;
use App\Models\User as UserModel;
use App\Repositories\Computers\EloquentComputerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

final class EloquentComputerRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private EloquentComputerRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new EloquentComputerRepository();
    }

    public function testItFindsComputerById(): void
    {
        $user = UserModel::factory()->createOne();
        $model = ComputerModel::factory()->for($user)->createOne();

        $computer = $this->subject->findById($model->id);

        self::assertEquals($model->id, $computer->getId());
        self::assertEquals($model->serial, $computer->getSerial());
        self::assertEquals($model->vendor, $computer->getVendor());
        self::assertEquals($model->model, $computer->getModel());
        self::assertEquals($model->type, $computer->getType());
        self::assertEquals($model->name, $computer->getName());
        self::assertEquals($model->last_fingerprint, $computer->getFingerprint());
        self::assertEquals($model->last_read, $computer->getLastRead());
    }

    public function testItFindsBySerial(): void
    {
        $userModel = UserModel::factory()->createOne();
        $model = ComputerModel::factory()->for($userModel)->createOne();
        $user = new User($userModel->id, $userModel->name, $userModel->email, OriginUrl::fromString($userModel->origin));

        $computer = $this->subject->findBySerial($user, $model->serial);

        self::assertEquals($model->id, $computer->getId());
    }

    public function testItSavesComputer(): void
    {
        $user = UserModel::factory()->createOne();
        $computer = Computer::new(
            userId: $user->id,
            name: ':name:',
            type: -1,
            vendor: ':vendor:',
            serial: 1234567890,
            model: 5,
        );

        self::assertFalse($computer->isExisting());

        $this->subject->save($computer);

        self::assertTrue($computer->isExisting());
        $this->assertDatabaseHas('computers', [
            'id' => $computer->getId(),
            'model' => $computer->getModel(),
            'serial' => $computer->getSerial(),
            'type' => $computer->getType(),
            'vendor' => $computer->getVendor(),
            'name' => $computer->getName(),
        ]);
    }
}
