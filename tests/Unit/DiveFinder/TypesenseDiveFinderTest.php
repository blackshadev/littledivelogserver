<?php

declare(strict_types=1);

namespace Tests\Unit\DiveFinder;

use App\Application\Dives\CommandObjects\FindDivesCommand;
use App\Domain\Dives\Entities\DiveSummary;
use App\Domain\Dives\Repositories\DiveSummaryRepository;
use App\Domain\Support\Arrg;
use App\Models\Dive;
use App\Services\Dives\TypesenseDiveFinder;
use DateTimeImmutable;
use Generator;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Typesense\Collection;
use Typesense\Documents;
use Typesense\LaravelTypesense\Typesense;

final class TypesenseDiveFinderTest extends TestCase
{
    private TypesenseDiveFinder $subject;

    private DiveSummaryRepository|MockInterface $repository;

    private Typesense|MockInterface $typesense;

    public function setUp(): void
    {
        parent::setUp();

        $this->typesense = Mockery::mock(Typesense::class);
        $this->repository = Mockery::mock(DiveSummaryRepository::class);
        $this->subject = new TypesenseDiveFinder($this->typesense, $this->repository);
    }

    public function testItFiltersUserId(): void
    {
        $cmd = FindDivesCommand::forUser(42, []);

        $this->expectsTypesenseDocumentsForModel(Dive::class)
            ->expects('search')
            ->withArgs(function (array $options) {
                Assert::assertStringContainsString('42', $options['filter_by']);
                return true;
            })
            ->andReturn(
                $this->typesenseResults([])
            );

        $this->repository->expects('findByIds')
            ->andReturn([]);

        $this->subject->search($cmd);
    }

    public function testItUsesKeywordAsQuery(): void
    {
        $keywords = ':keywords:';
        $cmd = FindDivesCommand::forUser(42, [ 'keywords' => ':keywords:']);

        $this->expectsTypesenseDocumentsForModel(Dive::class)
            ->expects('search')
            ->withArgs(function (array $options) use ($keywords) {
                Assert::assertSame($keywords, $options['q']);
                return true;
            })
            ->andReturn(
                $this->typesenseResults([])
            );

        $this->repository->expects('findByIds')
            ->andReturn([]);

        $this->subject->search($cmd);
    }

    public function testItCanUseEmptyAsQuery(): void
    {
        $cmd = FindDivesCommand::forUser(42, []);

        $this->expectsTypesenseDocumentsForModel(Dive::class)
            ->expects('search')
            ->withArgs(function (array $options) {
                Assert::assertSame('', $options['q']);
                return true;
            })
            ->andReturn(
                $this->typesenseResults([])
            );

        $this->repository->expects('findByIds')
            ->andReturn([]);

        $this->subject->search($cmd);
    }

    #[DataProvider('provideFilters')]
    public function testItBuildsFilterQueries(array $input, string $expectedFilter): void
    {
        $cmd = FindDivesCommand::forUser(0, $input);
        if (!empty($expectedFilter)) {
            $expectedFilter = 'user_id:=0 && ' . $expectedFilter;
        } else {
            $expectedFilter = 'user_id:=0';
        }

        $this->expectsTypesenseDocumentsForModel(Dive::class)
            ->expects('search')
            ->withArgs(function (array $options) use ($expectedFilter) {
                Assert::assertSame($expectedFilter, $options['filter_by']);
                return true;
            })
            ->andReturn(
                $this->typesenseResults([])
            );

        $this->repository->expects('findByIds')
            ->andReturn([]);

        $this->subject->search($cmd);
    }

    public function testItReturnsResults(): void
    {
        $dives = $this->buildDives([1, 2, 3]);
        $cmd = FindDivesCommand::forUser(0, []);

        $this->expectsTypesenseDocumentsForModel(Dive::class)
            ->expects('search')
            ->andReturn(
                $this->typesenseResults([
                    [ 'id' => 1 ],
                    [ 'id' => 2 ],
                    [ 'id' => 3 ],
                ])
            );

        $this->repository->expects('findByIds')
            ->with([1, 2, 3])
            ->andReturn($dives);

        $results = $this->subject->search($cmd);

        self::assertEquals($dives, $results);
    }

    public static function provideFilters(): Generator
    {
        $date = new DateTimeImmutable('2020-10-11 15:12:15');
        yield 'empty' => [
            [],
            ''
        ];

        yield 'after' => [
            [
                'date_after' => $date->format('Y-m-d H:i:s'),
            ],
            'date:>' . $date->getTimestamp(),
        ];


        yield 'before' => [
            [
                'date_before' => $date->format('Y-m-d H:i:s'),
            ],
            'date:<' . $date->getTimestamp(),
        ];

        yield 'buddies' => [
            [
                'buddies' => [2, 3]
            ],
            'buddies.id:=[2,3]'
        ];

        yield 'tags' => [
            [
                'tags' => [2, 3]
            ],
            'tags.id:=[2,3]'
        ];

        yield 'place' => [
            [
                'place' => 5
            ],
            'place.id:=5'
        ];
    }

    private function typesenseResults(array $data): array
    {
        return [
            'total' => count($data),
            'hits' => Arrg::map($data, fn ($item) => ['document' => $item])
        ];
    }

    /**
     * @param class-string $modelClass
     */
    private function expectsTypesenseDocumentsForModel(string $modelClass): MockInterface|Documents
    {
        $collection = Mockery::mock(Collection::class);
        $documents = Mockery::mock(Documents::class);

        $this->typesense->expects('getCollectionIndex')
            ->withArgs(static fn ($model) => $model instanceof $modelClass)
            ->andReturn($collection);

        $collection->expects('getDocuments')
            ->andReturn($documents);

        return $documents;
    }

    /**
     * @param int[] $ids
     * @return DiveSummary[]
     */
    private function buildDives(array $ids): array
    {
        return array_map(
            static fn (int $id) => new DiveSummary(
                diveId: $id,
                divetime: 5,
                date: new DateTimeImmutable(),
                tags: [],
                place: null,
            ),
            $ids,
        );
    }
}
