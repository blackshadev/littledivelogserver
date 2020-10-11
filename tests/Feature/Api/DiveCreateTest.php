<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\DiveController;
use App\Models\Dive;
use App\Models\User;
use Tests\TestCase;
use Tests\WithFakeTAuthentication;

class DiveCreateTest extends TestCase
{
    use WithFakeTAuthentication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakedTauth();
    }

    public function testItWorksWithMinimalData()
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $this->fakeAccessTokenFor($user);

        $dive = Dive::factory()->state([
            'user_id' => null,
        ])->makeOne();

        $data = [
            'date' => $dive->date,
            'divetime' => $dive->divetime,
            'max_depth' => $dive->max_depth,
        ];

        $this->post(action([DiveController::class, 'store']), $data)
            ->assertStatus(200)
            ->assertJsonStructure(['dive_id'])
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('dives', array_merge($data, ['user_id' => $user->id]));
    }

    public function testItRequiresToBeLoggedIn()
    {
        $user = User::factory()->createOne();
        $dive = Dive::factory()->state([
            'user_id' => null,
        ])->makeOne();

        $data = [
            'date' => $dive->date,
            'divetime' => $dive->divetime,
            'max_depth' => $dive->max_depth,
        ];

        $this->post(action([DiveController::class, 'store']), $data)
            ->assertStatus(403);

        $this->assertDatabaseMissing('dives', array_merge($data, ['user_id' => $user->id]));
    }

    public function testFullData()
    {
    }
}
