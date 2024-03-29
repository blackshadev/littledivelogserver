<?php

declare(strict_types=1);

namespace Tests\Integration\Api;

use App\Http\Controllers\Api\UserProfileController;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\WithFakeTAuthentication;

final class UserControllerTest extends TestCase
{
    use WithFaker;
    use WithFakeTAuthentication;
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakedTauth();
    }

    public function testItReturnsEmptyUserProfile(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $this->fakeAccessTokenFor($user);

        $this->get(action([UserProfileController::class, 'show']))
            ->assertStatus(200)
            ->assertJson([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'inserted' => $user->created_at->toISOString(),
                'dive_count' => 0,
                'tag_count' => 0,
                'computer_count' => 0,
                'buddy_count' => 0,
            ]);
    }

    public function testItReturnsFilledUserProfile(): void
    {
        /** @var User $user */
        $user = User::factory()
            ->filled()
            ->createOne();
        $this->fakeAccessTokenFor($user);

        $this->get(action([UserProfileController::class, 'show']))
            ->assertStatus(200)
            ->assertJson([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'inserted' => $user->created_at->toISOString(),
                'dive_count' => $user->dives()->count(),
                'tag_count' => $user->tags()->count(),
                'computer_count' => $user->computers()->count(),
                'buddy_count' => $user->buddies()->count(),
            ]);
    }

    public function testItErrorsOnNonUser(): void
    {
        $this->get(action([UserProfileController::class, 'show']))
            ->assertStatus(403);
    }

    public function testItGetsUsersEquipment(): void
    {
        /** @var Equipment $equipment */
        $equipment = Equipment::factory()
            ->forUser()
            ->filled()
            ->createOne();
        /** @var $user */
        $user = $equipment->user;
        $this->fakeAccessTokenFor($user);

        $tank = $user->equipment->tanks[0];

        $this->get(action([UserProfileController::class, 'equipment']))
            ->assertStatus(200)
            ->assertJson([
                'tanks' => [[
                    'volume' => $tank->volume,
                    'oxygen' => $tank->oxygen,
                    'pressure' => [
                        'begin' => $tank->pressure_begin,
                        'end' => $tank->pressure_end,
                        'type' => $tank->pressure_type
                    ]
                ]]
            ]);
    }
}
