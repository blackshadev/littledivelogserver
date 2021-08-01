<?php

declare(strict_types=1);

namespace Tests\Integration\Api;

use App\Http\Controllers\Api\TagController;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\WithFakeTAuthentication;

final class TagsControllerTest extends TestCase
{
    use WithFaker;
    use WithFakeTAuthentication;
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakedTauth();
    }

    public function testItCreatesATag(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $this->fakeAccessTokenFor($user);

        $tag = Tag::factory()->state([
            'user_id' => null,
        ])->makeOne();

        $data = [
            'text' => $tag->text,
            'color' => $tag->color,
        ];

        $this->post(action([TagController::class, 'store']), $data)
           ->assertStatus(200)
           ->assertJsonStructure(['tag_id'])
           ->assertJsonFragment($data);

        $this->assertDatabaseHas('tags', [
            'user_id' => $user->id,
            'text' => $tag->text,
            'color' => $tag->color,
        ]);
    }

    public function testItUpdatesATag(): void
    {
        /** @var User $user */
        $user = User::factory()
            ->has(Tag::factory()->count(2))
            ->createOne();
        $this->fakeAccessTokenFor($user);

        $tag = $user->tags()->first();

        $newTag = Tag::factory()->state([
            'user_id' => null,
        ])->makeOne();

        $data = [
            'text' => $newTag->text,
            'color' => $newTag->color,
        ];

        $this->put(action([TagController::class, 'update'], [$tag->id]), $data)
           ->assertStatus(200)
           ->assertJsonStructure(['tag_id'])
           ->assertJsonFragment($data);

        $this->assertDatabaseHas('tags', [
            'user_id' => $user->id,
            'text' => $data['text'],
            'color' => $data['color'],
        ]);
        $this->assertDatabaseMissing('tags', [
            'user_id' => $user->id,
            'text' => $tag->text,
            'color' => $tag->color,
        ]);
    }

    public function testItListsTags(): void
    {
        $tagCount = $this->faker->numberBetween(1, 10);

        /** @var User $user */
        $user = User::factory()
            ->has(Tag::factory()->count($tagCount))
            ->createOne();

        $this->fakeAccessTokenFor($user);

        /** @var User $user */
        $resp = $this->get(action([TagController::class, 'index']))
           ->assertStatus(200);

        $data = $resp->json();
        self::assertIsArray($data);
        self::assertArrayHasKey('tag_id', $data[0]);
        self::assertArrayHasKey('text', $data[0]);
        self::assertArrayHasKey('color', $data[0]);
    }

    public function testItShowsATag(): void
    {
        $tagCount = $this->faker->numberBetween(1, 10);

        /** @var User $user */
        $user = User::factory()
            ->has(Tag::factory()->count($tagCount))
            ->createOne();
        $tag = $user->tags()->first();

        $this->fakeAccessTokenFor($user);

        /** @var User $user */
        $this->get(action([TagController::class, 'show'], $tag->id))
           ->assertStatus(200)
           ->assertJsonStructure(['tag_id', 'text', 'color']);
    }
}
