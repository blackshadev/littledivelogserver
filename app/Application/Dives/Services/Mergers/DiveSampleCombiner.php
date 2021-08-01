<?php

declare(strict_types=1);

namespace App\Application\Dives\Services\Mergers;

use App\Domain\Dives\Entities\Dive;
use App\Domain\Support\Arrg;

final class DiveSampleCombiner
{
    /**
     * @param Dive[] $dives
     * @return array[]
     */
    public function combine(array $dives): array
    {
        if (empty($dives)) {
            return [];
        }

        $divesWithSamples = Arrg::filter(
            $dives,
            fn (Dive $dive) => !empty($dive->getSamples())
        );
        $orderedDives = $this->orderDives($divesWithSamples);

        $prevDive = array_shift($orderedDives);
        $samples = Arrg::copy($prevDive->getSamples());

        $this->mergeSampleStart($samples);

        /** @var Dive $dive */
        foreach ($orderedDives as $dive) {
            $prevSample = last($samples);
            $curSamples = $dive->getSamples();
            $lastDiveSampleTime = (last($prevDive->getSamples()) ?: [])['Time'] ?? 0;

            $prevDiveEndTime = $prevDive->getDate()->getTimestamp() + $lastDiveSampleTime;
            $surfaceTime = $dive->getDate()->getTimestamp() - $prevDiveEndTime;
            if ($surfaceTime < 0) {
                $surfaceTime = 0;
            }
            $timeOffset = $prevSample['Time'] + $surfaceTime;

            // Stitch some surface time in between
            if ($surfaceTime > 10) {
                $samples[] = [
                    "Time" => $prevSample['Time'] + 2,
                    "Depth" => 0.0,
                ];
                $samples[] = [
                    "Time" => $timeOffset - 2,
                    "Depth" => 0.0,
                ];
            }

            $this->mergeSampleStart($curSamples);

            foreach ($dive->getSamples() as $sample) {
                $sample['Time'] += $timeOffset;
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
            function (Dive $a, Dive $b) {
                return $a->getDate()->getTimestamp() - $b->getDate()->getTimestamp();
            }
        );

        return $dives;
    }

    private function mergeSampleStart(array &$samples): void
    {
        while (count($samples) > 1 && $samples[0]['Time'] === $samples[1]['Time']) {
            $samples[1] = $samples[0] + $samples[1];
            array_shift($samples);
        }
    }
}
