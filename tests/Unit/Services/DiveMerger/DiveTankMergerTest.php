<?php

declare(strict_types=1);

namespace Tests\Unit\Services\DiveMerger;

use App\Application\Dives\Exceptions\CannotMergeTankException;
use App\Application\Dives\Services\Mergers\DiveTankMergerImpl;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Entities\DiveTank;
use App\Domain\Dives\ValueObjects\GasMixture;
use App\Domain\Dives\ValueObjects\TankPressures;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DiveTankMergerTest extends TestCase
{
    public const FAKE_DIVE_ID = 1;

    public function testItMergesEmptyDiveTanks(): void
    {
        $merger = new DiveTankMergerImpl();

        $result = $merger->merge([]);

        self::assertNull($result);
    }

    #[DataProvider('diveTankDataProvider')]
    public function testItMergesTanks(array $tanks, DiveTank $expected): void
    {
        $merger = new DiveTankMergerImpl();

        $result = $merger->merge($tanks);

        self::assertEquals($expected->getVolume(), $result->getVolume());
        self::assertEquals($expected->getPressures()->getType(), $result->getPressures()->getType());
        self::assertEquals($expected->getPressures()->getBegin(), $result->getPressures()->getBegin());
        self::assertEquals($expected->getPressures()->getEnd(), $result->getPressures()->getEnd());
        self::assertEquals($expected->getGasMixture()->getOxygen(), $result->getGasMixture()->getOxygen());
        self::assertNotSame($expected, $result);
    }

    public function diveTankDataProvider()
    {
        yield 'single' => [
            [$this->createTank(200, 50)],
            $this->createTank(200, 50),
        ];

        yield 'single PSI' => [
            [$this->createTank(200, 50, type: 'psi')],
            $this->createTank(200, 50, type: 'psi'),
        ];

        yield 'single non standard O2' => [
            [$this->createTank(200, 50, oxygen: 30)],
            $this->createTank(200, 50, oxygen: 30),
        ];

        yield 'Merges many' => [
            [
                $this->createTank(70, 30),
                $this->createTank(210, 150),
                $this->createTank(150, 70),
            ],
            $this->createTank(210, 30),
        ];
    }

    #[DataProvider('invalidDiveTankDataProvider')]
    public function testItThrowsExceptionOnInvalidTanks(array $tanks): void
    {
        $this->expectException(CannotMergeTankException::class);

        $merger = new DiveTankMergerImpl();

        $merger->merge($tanks);
    }

    public function invalidDiveTankDataProvider()
    {
        yield 'Different pressure types' => [
            [
                $this->createTank(200, 50, type: 'psi'),
                $this->createTank(150, 30, type: 'bar'),
            ]
        ];

        yield 'Different oxygen percentages' => [
            [
                $this->createTank(200, 50, oxygen: 32),
                $this->createTank(150, 30, oxygen: 40),
            ]
        ];

        yield 'Different volumes' => [
            [
                $this->createTank(200, 50, volume: 7),
                $this->createTank(150, 30, volume: 12),
            ]
        ];
    }

    /**
     * @param Dive[] $dives
     * @param DiveTank[] $expected
     */
    #[DataProvider('divesWithDiveTankDataProvider')]
    public function testItMergesForDives(array $dives, array $expected): void
    {
        $merger = new DiveTankMergerImpl();

        $result = $merger->mergeForDives($dives);

        self::assertEquals($expected, $result);
    }

    public function divesWithDiveTankDataProvider()
    {
        yield 'empty' => [[], []];

        yield 'Dives without tank' => [
            [
                $this->createDiveFor([]),
                $this->createDiveFor([]),
            ],
            [],
        ];

        yield 'Single dive' => [
            [
                $this->createDiveFor([
                    $this->createTank(200, 142, 7, 32),
                    $this->createTank(142, 31, 12, 43),
                ]),
            ], [
                    $this->createTank(200, 142, 7, 32),
                    $this->createTank(142, 31, 12, 43),
            ]];

        yield 'Single tanks' => [
            [
                $this->createDiveFor([
                    $this->createTank(200, 142),
                ]),

                $this->createDiveFor([
                    $this->createTank(142, 31),
                ]),
            ], [
                    $this->createTank(200, 31),
            ]];

        yield 'Many tanks many dives' => [
            [
                $this->createDiveFor([
                    $this->createTank(201, 141),
                    $this->createTank(202, 202),
                    $this->createTank(203, 43),
                ]),

                $this->createDiveFor([
                    $this->createTank(141, 51),
                    $this->createTank(202, 202),
                    $this->createTank(43, 33),
                ]),

                $this->createDiveFor([
                    $this->createTank(51, 51),
                    $this->createTank(202, 52),
                    $this->createTank(33, 33),
                ]),
            ], [
                    $this->createTank(201, 51),
                    $this->createTank(202, 52),
                    $this->createTank(203, 33),
            ]
        ];
    }

    private function createDiveFor(array $diveTanks): Dive
    {
        return Dive::new(null, null, tanks: $diveTanks);
    }

    private function createTank(int $start, int $end, int $volume = 10, int $oxygen = 21, $type = 'bar')
    {
        return DiveTank::new(null, $volume, new GasMixture($oxygen), new TankPressures($type, $start, $end));
    }
}
