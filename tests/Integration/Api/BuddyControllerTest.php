<?php

namespace Tests\Integration\Api;

use App\Http\Controllers\Api\BuddyController;
use App\Models\Buddy;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\WithFakeTAuthentication;

class BuddyControllerTest extends TestCase
{
    use WithFaker;
    use WithFakeTAuthentication;
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakedTauth();
    }

    public function testItCreatesABuddy()
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $this->fakeAccessTokenFor($user);

        $buddy = Buddy::factory()->state([
            'user_id' => null,
        ])->makeOne();

        $data = [
            'text' => $buddy->name,
            'color' => $buddy->color,
        ];
        $this->post(action([BuddyController::class, 'store']), $data)
           ->assertStatus(200)
           ->assertJsonStructure(['buddy_id'])
           ->assertJsonFragment($data);

        $this->assertDatabaseHas('buddies', [
            'user_id' => $user->id,
            'name' => $buddy->name,
            'color' => $buddy->color,
        ]);
    }

    public function testItUpdatesABuddy()
    {
        /** @var User $user */
        $user = User::factory()
            ->has(Buddy::factory()->count(2))
            ->createOne();

        $this->fakeAccessTokenFor($user);

        $buddy = $user->buddies()->first();

        $newBuddy = Buddy::factory()->state([
            'user_id' => null,
        ])->makeOne();

        $data = [
            'text' => $newBuddy->name,
            'color' => $newBuddy->color,
        ];

        $this->put(action([BuddyController::class, 'update'], [$buddy->id]), $data)
           ->assertStatus(200)
           ->assertJsonStructure(['buddy_id'])
           ->assertJsonFragment($data);

        $this->assertDatabaseHas('buddies', [
            'user_id' => $user->id,
            'name' => $data['text'],
            'color' => $data['color'],
        ]);

        $this->assertDatabaseMissing('buddies', [
            'user_id' => $user->id,
            'name' => $buddy->name,
            'color' => $buddy->color,
        ]);
    }

    public function testItListsBuddies()
    {
        $buddyCount = $this->faker->numberBetween(1, 10);

        /** @var User $user */
        $user = User::factory()
            ->has(Buddy::factory()->count($buddyCount))
            ->createOne();

        $this->fakeAccessTokenFor($user);

        /** @var User $user */
        $resp = $this->get(action([BuddyController::class, 'index']))
           ->assertStatus(200);

        $data = $resp->json();
        self::assertIsArray($data);
        self::assertArrayHasKey('buddy_id', $data[0]);
        self::assertArrayHasKey('text', $data[0]);
        self::assertArrayHasKey('color', $data[0]);
    }

    public function testItShowsABuddy()
    {
        $buddyCount = $this->faker->numberBetween(1, 10);

        /** @var User $user */
        $user = User::factory()
            ->has(Buddy::factory()->count($buddyCount))
            ->createOne();
        $buddy = $user->buddies()->first();

        $this->fakeAccessTokenFor($user);

        /* @var User $user */
        $this->get(action([BuddyController::class, 'show'], $buddy->id))
           ->assertStatus(200)
           ->assertJsonStructure(['buddy_id', 'text', 'color']);
    }
}
