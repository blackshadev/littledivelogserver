<?php

namespace Tests\Feature\Api;

use App\Models\Buddy;
use App\Models\Dive;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Littledev\Tauth\Services\TauthServiceInterface;
use Tests\TestCase;
use Tests\WithFakeTAuthentication;

class DiveListingTest extends TestCase
{
    use WithFaker;
    use WithFakeTAuthentication;


    protected function setUp(): void
    {
        parent::setUp();
        $this->fakedTauth();
    }

    public function testDiveListForAuthenticatedUser()
    {
        $diveCount = $this->faker->numberBetween(1, 10);

        /** @var User $user */
        $user = User::factory()
            ->has(Dive::factory()->count($diveCount))
            ->makeOne();

        $this->fakeAccessTokenFor($user);

        /** @var Dive $dive */
        foreach ($user->dives as $dive) {
            $tags = Tag::factory()->count($this->faker->numberBetween(0, 3))->make();
            $buddies = Buddy::factory()->count($this->faker->numberBetween(0, 3))->make();
            $dive->tags()->attach($tags);
            $dive->buddies()->attach($buddies);
        }

        $response = $this->get('/api/dives/', ['Authorization' => 'Bearer aa.test.aa']);
        $response->assertStatus(200);

    }

}
