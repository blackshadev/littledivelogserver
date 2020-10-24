<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Repositories;

use App\DataTransferObjects\BuddyData;
use App\Error\BuddyNotFound;
use App\Models\Buddy;
use App\Models\User;
use App\Services\Repositories\BuddyRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BuddyRepositoryTest extends TestCase
{
    use WithFaker;

    /** @var \Mockery\Mock|BuddyRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = \Mockery::mock(BuddyRepository::class)->makePartial();
        $this->repository->shouldReceive('save')->byDefault();
    }

    public function testItUpdatesBuddyDetails()
    {
        $buddyData = new BuddyData();
        $buddyData->setColor($this->faker->hexColor);
        $buddyData->setName($this->faker->name);

        $buddy = new Buddy();

        $this->repository->expects('save')->with($buddy);

        $this->repository->update($buddy, $buddyData);

        self::assertSame($buddyData->getName(), $buddy->name);
        self::assertSame($buddyData->getColor(), $buddy->color);
    }

    public function testFindOrCreateThrowsExceptionWhenBuddyNotFound()
    {
        $id = $this->faker->numberBetween();
        $buddyData = new BuddyData();
        $buddyData->setId($id);

        $user = new User();

        $this->repository->expects('find')
            ->with($id, $user)
            ->andReturnNull();

        $this->expectException(BuddyNotFound::class);

        $this->repository->findOrCreate($buddyData, $user);
    }

    public function testFindOrCreateReturnsExistingBuddyById()
    {
        $id = $this->faker->numberBetween();
        $buddyData = new BuddyData();
        $buddyData->setId($id);

        $user = new User();
        $buddy = new Buddy();

        $this->repository->expects('find')
            ->with($id, $user)
            ->andReturn($buddy);

        $result = $this->repository->findOrCreate($buddyData, $user);

        self::assertEquals($buddy, $result);
    }

    public function testFindOrCreateReturnsExistingBuddyByName()
    {
        $name = $this->faker->name;

        $buddyData = new BuddyData();
        $buddyData->setName($name);

        $user = new User();
        $buddy = new Buddy();

        $this->repository->expects('findByName')
            ->with($name, $user)
            ->andReturn($buddy);

        $result = $this->repository->findOrCreate($buddyData, $user);

        self::assertEquals($buddy, $result);
    }

    public function testFindOrCreateReturnsNewBuddyWhenNotFound()
    {
        $name = $this->faker->name;

        $buddyData = new BuddyData();
        $buddyData->setName($name);

        $user = new User();
        $buddy = new Buddy();

        $this->repository->expects('findByName')
            ->with($name, $user)
            ->andReturnNull();
        $this->repository->expects('create')
            ->with($buddyData, $user)
            ->andReturn($buddy);

        $result = $this->repository->findOrCreate($buddyData, $user);

        self::assertEquals($buddy, $result);
    }

    public function testItCreatesANewBuddy()
    {
        $uid = $this->faker->numberBetween();
        $name = $this->faker->name;
        $color = $this->faker->hexColor;

        $buddyData = new BuddyData();
        $buddyData->setName($name);
        $buddyData->setColor($color);

        $user = new User();
        $user->id = $uid;

        $this->repository->expects('save')
            ->withArgs(function ($arg) use ($buddyData, $user) {
                /** @var Buddy $arg */
                self::assertInstanceOf(Buddy::class, $arg);
                self::assertEquals($buddyData->getName(), $arg->name);
                self::assertEquals($buddyData->getColor(), $arg->color);
                self::assertEquals($user->id, $arg->user_id);

                return true;
            });

        $result = $this->repository->create($buddyData, $user);

        self::assertInstanceOf(Buddy::class, $result);
        self::assertEquals($buddyData->getName(), $result->name);
        self::assertEquals($buddyData->getColor(), $result->color);
        self::assertEquals($user->id, $result->user_id);
    }
}
