<?php

declare(strict_types=1);

namespace Tests\Unit\DiveFinder;

use App\Application\Dives\CommandObjects\FindDivesCommand;
use App\Domain\Dives\Entities\DiveSummary;
use App\Domain\Dives\Repositories\DiveSummaryRepository;
use App\Domain\Support\Arrg;
use App\Models\Dive;
use App\Repositories\Dives\ExplorerDiveFinder;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\Results;
use JeroenG\Explorer\Application\SearchCommand;
use Mockery;
use Tests\TestCase;

class DiveFinderTest extends TestCase
{
    private ExplorerDiveFinder $subject;

    private DiveSummaryRepository |

Mockery\MockInterface $repository;

    private IndexAdapterInterface |

Mockery\MockInterface $indexAdapter;

    public function setUp(): void
    {
        parent::setUp();

        $this->indexAdapter = Mockery::mock(IndexAdapterInterface::class);
        $this->repository = Mockery::mock(DiveSummaryRepository::class);
        $this->subject = new ExplorerDiveFinder($this->indexAdapter, $this->repository);
    }

    public function testItFiltersUserId(): void
    {
        $cmd = FindDivesCommand::forUser(42, []);
        $this->indexAdapter->expects('search')
            ->withArgs(function (SearchCommand $cmd) {
                self::assertEquals((new Dive())->searchableAs(), $cmd->getIndex());
                self::assertEquals([
                    ['term' => [ 'user_id' => [ 'value' => 42, 'boost' => 1.0 ]] ]
                ], $cmd->buildQuery()['query']['bool']['filter']);

                return true;
            })
            ->andReturn(
                $this->explorerResults([])
            );

        $this->repository->expects('findByIds')
            ->andReturn([]);

        $this->subject->search($cmd);
    }

    /** @dataProvider mustQueryDataProvider */
    public function testItBuildsMustQuery(array $input, array $expectedMust): void
    {
        $cmd = FindDivesCommand::forUser(0, $input);

        $this->indexAdapter->expects('search')
            ->withArgs(function (SearchCommand $cmd) use ($expectedMust) {
                self::assertEquals((new Dive())->searchableAs(), $cmd->getIndex());
                self::assertEquals($expectedMust, $cmd->buildQuery()['query']['bool']['must']);

                return true;
            })
            ->andReturn(
                $this->explorerResults([])
            );

        $this->repository->expects('findByIds')
            ->andReturn([]);

        $this->subject->search($cmd);
    }

    public function testItReturnsResults(): void
    {
        $dives = [
            new DiveSummary(
                diveId: 1,
                divetime: 5,
                place: null,
                tags: [],
                date: new \DateTimeImmutable(),
            ),

            new DiveSummary(
                diveId: 2,
                divetime: 5,
                place: null,
                tags: [],
                date: new \DateTimeImmutable(),
            ),

            new DiveSummary(
                diveId: 3,
                divetime: 5,
                place: null,
                tags: [],
                date: new \DateTimeImmutable(),
            ),
        ];
        $cmd = FindDivesCommand::forUser(0, []);

        $this->indexAdapter->expects('search')
            ->andReturn(
                $this->explorerResults([
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

    public function mustQueryDataProvider(): \Generator
    {
        yield 'empty' => [
            [],
            []
        ];

        yield 'keyword' => [
            [
                'keywords' => ':test:'
            ],
            [
                [
                    'bool' => [
                        'must' => [],
                        'filter' => [],
                        'should' => [
                            [
                                'nested' => [
                                    'path' => 'place',
                                    'query' => [
                                        'match' => [
                                            'place.name' => [
                                                'query' => ':test:',
                                                'fuzziness' => 'auto',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'nested' => [
                                    'path' => 'buddies',
                                    'query' => [
                                        'match' => [
                                            'buddies.name' => [
                                                'query' => ':test:',
                                                'fuzziness' => 'auto',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'nested' => [
                                    'path' => 'tags',
                                    'query' => [
                                        'match' => [
                                            'tags.text' => [
                                                'query' => ':test:',
                                                'fuzziness' => 'auto',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];

        yield 'after' => [
            [
                'date_after' => '2020-10-11 15:12:15'
            ],
            [
                [
                    'range' => [
                        'date' => [
                            'gt' => new \DateTimeImmutable('2020-10-11 15:12:15'),
                            'boost' => 1.0,
                        ]
                    ]
                ]
            ]
        ];


        yield 'before' => [
            [
                'date_before' => '2020-10-11 15:12:15'
            ],
            [
                [
                    'range' => [
                        'date' => [
                            'lt' => new \DateTimeImmutable('2020-10-11 15:12:15'),
                            'boost' => 1.0,
                        ]
                    ]
                ]
            ]
        ];

        yield 'buddies' => [
            [
                'buddies' => [2, 3]
            ],
            [
                [
                    'nested' => [
                        'path' => 'buddies',
                        'query' => [
                            'term' => [
                                'buddies.id' => [
                                    'value' => 2,
                                    'boost' => 1.0
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'nested' => [
                        'path' => 'buddies',
                        'query' => [
                            'term' => [
                                'buddies.id' => [
                                    'value' => 3,
                                    'boost' => 1.0
                                ]
                            ]
                        ]
                    ]
                ],
            ],
        ];

        yield 'tags' => [
            [
                'tags' => [2, 3]
            ],
            [
                [
                    'nested' => [
                        'path' => 'tags',
                        'query' => [
                            'term' => [
                                'tags.id' => [
                                    'value' => 2,
                                    'boost' => 1.0
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'nested' => [
                        'path' => 'tags',
                        'query' => [
                            'term' => [
                                'tags.id' => [
                                    'value' => 3,
                                    'boost' => 1.0
                                ]
                            ]
                        ]
                    ]
                ],
            ],
        ];

        yield 'place' => [
            [
                'place' => 5
            ],
            [
                [
                    'nested' => [
                        'path' => 'place',
                        'query' => [
                            'term' => [
                                'place.id' => [
                                    'value' => 5,
                                    'boost' => 1.0
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    private function explorerResults(array $data): Results
    {
        return new Results([
            'hits' => [
                'total' => count($data),
                'hits' => Arrg::map($data, fn ($item) => ['_source' => $item])
            ]
        ]);
    }
}
