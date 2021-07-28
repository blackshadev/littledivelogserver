<?php

declare(strict_types=1);

namespace App\Application\Dives\Services\Mergers;

use App\Domain\Dives\Entities\Dive;
use App\Domain\Support\Arrg;

class DiveSampleCombiner
{
    /**
     * @param Dive[] $dives
     * @return array[]
     */
    public function combine(array $dives): array
    {
        $divesWithSamples = Arrg::filter(
            $dives,
            fn ($dive) => $dive->samples !== null && count($dive->samples) > 0
        );
        $orderedDives = $this->orderDives($divesWithSamples);

        $prevDive = array_shift($dives);
        $samples = Arrg::copy($prevDive->getSamples());

        /** @var Dive $dive */
        foreach ($orderedDives as $dive) {
            $prevSample = last($samples);
            $curSamples = $dive->getSamples();

            $timeDiff = $dive->getDate()->getTimestamp() - $prevDive->getDate()->getTimestamp();
            $timeOffset = $prevSample->Time + $timeDiff;

            if ($timeDiff < 0) {
                throw new \UnexpectedValueException('Timediff should be greater than 0 at this point');
            }

            // Stitch some surface time in between
            if ($timeDiff < 10) {
                $samples[] = [
                    "Time" => $timeOffset + floor($timeDiff / 2),
                    "Depth" => 0,
                ];
            } else {
                $samples[] = [
                    "Time" => $prevSample->Time + 2,
                    "Depth" => 0,
                ];
                $samples[] = [
                    "Time" => $timeOffset,
                    "Depth" => 0,
                ];
            }

            while ($dive->getSamples()[0]->Time === $dive->getSamples()[1]->Time) {
                $curSamples[0] = array_merge($curSamples[0], $curSamples[1]);
                array_shift($curSamples);
            }

            foreach ($dive->getSamples() as $sample) {
                $sample->Time += $timeOffset;
                $samples[] = $sample;
            }

            $prevDive = $dive;
        }

        return $samples;
    }

    private function orderDives(array $dives): array
    {
        usort(
            $dives,
            function ($a, $b) {
                if ($a->date->lessThan($b->date)) {
                    return -1;
                }

                if ($a->date->greaterThan($b->date)) {
                    return 1;
                }

                return 0;
            }
        );

        return $dives;
    }
}
