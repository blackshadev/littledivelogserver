<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Repositories;

use App\DataTransferObjects\ComputerData;
use App\Error\ComputerAlreadyExists;
use App\Models\Computer;
use App\Models\User;
use App\Services\Repositories\ComputerRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Tests\TestCase;

class ComputerRepositoryTest extends TestCase
{
    use WithFaker;

    /** @var ComputerRepository|MockInterface */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = \Mockery::mock(ComputerRepository::class)
            ->makePartial();

        $this->repository->shouldReceive('save')->byDefault();
    }

    public function testItCreatesNewComputer()
    {
        $serial = $this->faker->numberBetween();

        $computer = new Computer();
        $user = new User();

        $computerData = new ComputerData();
        $computerData->setSerial($serial);

        $this->repository->expects('findBySerial')
            ->with($serial, $user)
            ->andReturnNull();

        $this->repository->expects('make')
            ->with($computerData, $user)
            ->andReturn($computer);

        $result = $this->repository->create($computerData, $user);

        self::assertSame($computer, $result);
    }

    public function testItThrowsErrorOnNonExisting()
    {
        $serial = $this->faker->numberBetween();

        $computer = new Computer();
        $user = new User();

        $computerData = new ComputerData();
        $computerData->setSerial($serial);

        $this->repository->expects('findBySerial')
            ->with($serial, $user)
            ->andReturn($computer);

        $this->expectException(ComputerAlreadyExists::class);

        $this->repository->expects('save')->never();
        $this->repository->create($computerData, $user);
    }

    public function testItUpdatesLastReadWithNewerDate()
    {
        $date = new Carbon($this->faker->dateTimeThisYear());
        $fingerprint = $this->faker->word;

        $computer = new Computer();
        $computer->last_read = $date->clone()->subDays($this->faker->numberBetween(1, 300));
        $computer->last_fingerprint = 'xx';

        $this->repository->expects('save')
            ->withArgs(function ($savedComp) use ($date, $fingerprint, &$computer) {
                self::assertSame($computer, $savedComp);
                self::assertEquals($date, $savedComp->last_read);
                self::assertEquals($fingerprint, $savedComp->last_fingerprint);

                return true;
            });

        $this->repository->updateLastRead($computer, $date, $fingerprint);
        self::assertEquals($date, $computer->last_read);
        self::assertEquals($fingerprint, $computer->last_fingerprint);
    }

    public function testItDoesNotUpdateLastReadWithOlderDate()
    {
        $date = new Carbon($this->faker->dateTimeThisYear);
        $fingerprint = $this->faker->word;

        $computer = new Computer();
        $computer->last_read = $date->clone()->addDays($this->faker->numberBetween(1, 300));
        $computer->last_fingerprint = 'xx';

        $this->repository->expects('save')->never();

        $this->repository->updateLastRead($computer, $date, $fingerprint);

        self::assertNotEquals($date, $computer->last_read);
        self::assertNotEquals($fingerprint, $computer->last_fingerprint);
    }
}
