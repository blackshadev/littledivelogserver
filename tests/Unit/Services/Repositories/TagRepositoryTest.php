<?php

namespace Tests\Unit\Services\Repositories;

use App\DataTransferObjects\TagData;
use App\Error\TagNotFound;
use App\Models\Tag;
use App\Models\User;
use App\Services\Repositories\TagRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagRepositoryTest extends TestCase
{
    use WithFaker;

    /** @var \Mockery\Mock|TagRepository  */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = \Mockery::mock(TagRepository::class)->makePartial();
        $this->repository->shouldReceive('save')->byDefault();
    }

    public function testItUpdatesTagDetails()
    {
        $tagData = new TagData();
        $tagData->setText($this->faker->word);
        $tagData->setColor($this->faker->hexColor);

        $tag = new Tag();

        $this->repository->expects('save')->with($tag);

        $this->repository->update($tag, $tagData);

        self::assertSame($tagData->getText(), $tag->text);
        self::assertSame($tagData->getColor(), $tag->color);
    }

    public function testFindOrCreateThrowsExceptionWhenTagNotFound()
    {
        $id = $this->faker->numberBetween();
        $tagData = new TagData();
        $tagData->setId($id);

        $user = new User();

        $this->repository->expects('find')
            ->with($id, $user)
            ->andReturnNull();

        $this->expectException(TagNotFound::class);

        $this->repository->findOrCreate($tagData, $user);
    }

    public function testFindOrCreateReturnsExistingTagById()
    {
        $id = $this->faker->numberBetween();
        $tagData = new TagData();
        $tagData->setId($id);

        $user = new User();
        $tag = new Tag();

        $this->repository->expects('find')
            ->with($id, $user)
            ->andReturn($tag);

        $result = $this->repository->findOrCreate($tagData, $user);

        self::assertSame($tag, $result);
    }

    public function testFindOrCreateReturnsExistingTagByText()
    {
        $word = $this->faker->word;

        $tagData = new TagData();
        $tagData->setText($word);

        $user = new User();
        $tag = new Tag();

        $this->repository->expects('findByText')
            ->with($word, $user)
            ->andReturn($tag);

        $result = $this->repository->findOrCreate($tagData, $user);

        self::assertSame($tag, $result);
    }

    public function testFindOrCreateReturnsNewTagWhenNotFound()
    {
        $text = $this->faker->word;

        $tagData = new TagData();
        $tagData->setText($text);

        $user = new User();
        $tags = new Tag();

        $this->repository->expects('findByText')
            ->with($text, $user)
            ->andReturnNull();
        $this->repository->expects('create')
            ->with($tagData, $user)
            ->andReturn($tags);

        $result = $this->repository->findOrCreate($tagData, $user);

        self::assertSame($tags, $result);
    }

    public function testItCreatesANewTag()
    {
        $uid = $this->faker->numberBetween();
        $text = $this->faker->word;
        $color = $this->faker->hexColor;


        $tagData = new TagData();
        $tagData->setText($text);
        $tagData->setColor($color);

        $user = new User();
        $user->id = $uid;
;
        $this->repository->expects('save')
            ->withArgs(function ($arg) use ($tagData, $user) {
                /** @var Tag $arg */
                self::assertInstanceOf(Tag::class, $arg);
                self::assertEquals($tagData->getText(), $arg->text);
                self::assertEquals($tagData->getColor(), $arg->color);
                self::assertEquals($user->id, $arg->user_id);
                return true;
            });

        $result = $this->repository->create($tagData, $user);

        self::assertInstanceOf(Tag::class, $result);
        self::assertEquals($tagData->getText(), $result->text);
        self::assertEquals($tagData->getColor(), $result->color);
        self::assertEquals($user->id, $result->user_id);
    }


}
