<?php

declare(strict_types=1);

namespace Tests\Unit\Services\DiveMerger;

use App\Application\Dives\Services\Mergers\DiveSampleCombiner;
use App\Domain\Dives\Entities\Dive;
use PHPUnit\Framework\TestCase;

class DiveSampleCombinerTest extends TestCase
{
    /**
     * @dataProvider diveProvider
     * @param Dive[] $dives
     * @param array[] $expectedSamples
     */
    public function testItWorks(array $dives, array $expectedSamples): void
    {
        $combiner = new DiveSampleCombiner();
        $result = $combiner->combine($dives);

        self::assertEquals($expectedSamples, $result);
    }

    public function diveProvider()
    {
        yield 'empty' => [
            [],
            []
        ];

        yield 'Single dive' => [
            [
                $this->createDiveWithSamples(
                    '2020-10-10 10:10:10',
                    [
                        $this->createSample(0, 1),
                        $this->createSample(30, 2),
                        $this->createSample(60, 5),
                        $this->createSample(120, 3),
                        $this->createSample(180, 0),
                    ]
                ),
            ],
            [
                $this->createSample(0, 1),
                $this->createSample(30, 2),
                $this->createSample(60, 5),
                $this->createSample(120, 3),
                $this->createSample(180, 0),
            ]
        ];

        yield 'Two dives' => [
            [
                $this->createDiveWithSamples(
                    '2020-10-10 10:10:10',
                    [
                        $this->createSample(0, 1),
                        $this->createSample(30, 2),
                        $this->createSample(60, 5),
                        $this->createSample(120, 3),
                        $this->createSample(180, 0),
                    ]
                ),
                $this->createDiveWithSamples(
                    '2020-10-10 10:13:12',
                    [
                        $this->createSample(0, 1),
                        $this->createSample(30, 2),
                        $this->createSample(60, 5),
                        $this->createSample(120, 3),
                        $this->createSample(180, 0),
                    ]
                ),
            ],
            [
                $this->createSample(0, 1),
                $this->createSample(30, 2),
                $this->createSample(60, 5),
                $this->createSample(120, 3),
                $this->createSample(180, 0),

                $this->createSample(182, 1),
                $this->createSample(212, 2),
                $this->createSample(242, 5),
                $this->createSample(302, 3),
                $this->createSample(362, 0),
            ]
        ];

        yield 'Add surface time to dives' => [
            [
                $this->createDiveWithSamples(
                    '2020-10-10 10:10:10',
                    [
                        $this->createSample(0, 1),
                    ]
                ),
                $this->createDiveWithSamples(
                    '2020-10-10 10:13:12',
                    [
                        $this->createSample(0, 1),
                    ]
                ),
            ],
            [
                $this->createSample(0, 1),
                $this->createSample(2, 0),
                $this->createSample(180, 0),
                $this->createSample(182, 1),
            ]
        ];

        yield 'Keeps attributes' => [
            [
                $this->createDiveWithSamples(
                    '2020-10-10 10:10:10',
                    [
                        $this->createSample(0, 1, [ 'Misc' => [ 'yes' => true ] ]),
                    ]
                ),
                $this->createDiveWithSamples(
                    '2020-10-10 10:10:12',
                    [
                        $this->createSample(0, 1, [ 'Temperature' => 20 ]),
                    ]
                ),
            ],
            [
                $this->createSample(0, 1, [ 'Misc' => [ 'yes' => true ]]),
                $this->createSample(2, 1, [ 'Temperature' => 20 ]),
            ]
        ];

        yield 'Merges same starting samples' => [
            [
                $this->createDiveWithSamples(
                    '2020-10-10 10:10:10',
                    [
                        $this->createSample(0, 1),
                        $this->createSample(0, 1, [ 'Misc' => [ 'yes' => true ] ]),
                        $this->createSample(0, 1, [ 'Temperature' => 20 ]),
                    ]
                ),
            ],
            [
                $this->createSample(0, 1, [ 'Temperature' => 20, 'Misc' => [ 'yes' => true ] ]),
            ]
        ];
    }

    private function createDiveWithSamples(string $date, array $samples): Dive
    {
        return Dive::new(null, new \DateTimeImmutable($date), samples: $samples);
    }

    private function createSample(
        int $time,
        ?float $depth = null,
        array $other = [],
    ): array {
        return array_merge([
            'Time' => $time,
            'Depth' => $depth,
        ], $other);
    }
}
