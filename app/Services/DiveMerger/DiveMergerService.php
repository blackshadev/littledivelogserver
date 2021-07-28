<?php

declare(strict_types=1);

namespace App\Services\DiveMerger;

use App\Application\Buddies\DataTransferObjects\BuddyData;
use App\Application\Dives\DataTransferObjects\NewDiveData;
use App\Application\Dives\Exceptions\CannotMergeDivesException;
use App\Application\Equipment\DataTransferObjects\TankData;
use App\Application\Places\DataTransferObjects\PlaceData;
use App\Application\Tags\DataTransferObjects\TagData;
use App\Domain\Support\Arrg;
use App\Domain\Support\Math;
use App\Models\Dive;
use App\Models\DiveTank;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class DiveMergerService
{
    private const MAX_TIME_DISTANCE_IN_HOURS = 2;

    /** @var Dive[] $dives */
    public function mergeDives(array $dives): NewDiveData
    {
        $exception = $this->canMergeDives($dives);
        if ($exception !== null) {
            throw $exception;
        }

        $target = new NewDiveData();
        $target->setUser($dives[0]->user);

        $divesWithComputerPreferred = $this->preferDivesWithComputer($dives);

        $target->setDate(min(...Arrg::get($divesWithComputerPreferred, 'date')));
        $target->setMaxDepth(max(...Arrg::get($divesWithComputerPreferred, 'max_depth')));
        $target->setPlace(PlaceData::fromId(Arrg::firstNotNull($dives, 'place_id')));
        $target->setComputerId(Arrg::firstNotNull($dives, 'computer_id'));

        $target->setTanks($this->mergeTanks($dives));
        $target->setBuddies($this->mergeBuddies($dives));
        $target->setTags($this->mergeTags($dives));

        $target->setSamples($this->mergeSamples($dives));

        return $target;
    }

    /**
     * @param Dive[] $dives
     */
    private function canMergeDives(array $dives): ?CannotMergeDivesException
    {
        if (count($dives) < 2) {
            return CannotMergeDivesException::tooFewDives();
        }

        /** @var Carbon $minDate */
        $minDate = min(...Arrg::get($dives, 'date'));
        $maxDate = max(...Arrg::get($dives, 'date'));

        if ($minDate->diffInHours($maxDate) > self::MAX_TIME_DISTANCE_IN_HOURS) {
            return CannotMergeDivesException::timeDifferenceToBig();
        }

        $places = array_unique(Arrg::notNull(Arrg::get($dives, 'place_id')));
        if (count($places) > 1) {
            return CannotMergeDivesException::placesDifference();
        }

        $computers = array_unique(Arrg::notNull(Arrg::get($dives, 'computer_id')));
        if (count($computers) > 1) {
            return CannotMergeDivesException::computerDifference();
        }

        $users = array_unique(Arrg::notNull(Arrg::get($dives, 'user_id')));
        if (count($users) > 1) {
            return CannotMergeDivesException::userDifference();
        }

        return null;
    }

    private function preferDivesWithComputer(array $dives): array
    {
        $divesWithComputers = array_filter($dives, fn ($dive) => $dive->computer_id);

        // limit scope to dives with computers because they are preferred
        if (count($divesWithComputers)) {
            return $divesWithComputers;
        }

        return $dives;
    }

    /**
     * @param Dive[] $dives
     * @return DiveTank[]
     */
    private function mergeTanks(array $dives): array
    {
        /** @var TankData[] $tanks */
        $tanks = [];

        foreach ($dives as $dive) {

            /** @var DiveTank[] $diveTanks */
            $diveTank = $dive->tanks()->first();

            if ($diveTank === null) {
                continue;
            }

            if (!isset($tanks[0])) {
                $tanks[] = new TankData();
            }

            $tank = $tanks[0];

            $tank->setVolume($diveTank->volume);
            $tank->setOxygen($diveTank->oxygen);

            $pressures = $tank->getPressures();
            $pressures->setBegin(Math::max($pressures->getBegin(), $diveTank->pressure_begin));
            $pressures->setEnd(Math::min($pressures->getEnd(), $diveTank->pressure_end));
            $pressures->setType($diveTank->pressure_type);
        }

        return $tanks;
    }

    /**
     * @param Dive[] $dives
     * @return BuddyData[]
     */
    private function mergeBuddies(array $dives): ?array
    {
        $allBuddies = Arr::flatten(array_map(fn ($dive) => $dive->buddies, $dives));
        $ids = Arrg::unique($allBuddies, "id");
        return Arrg::map($ids, fn ($id) => BuddyData::fromId($id));
    }

    /**
     * @param Dive[] $dives
     * @return TagData[]
     */
    private function mergeTags(array $dives): ?array
    {
        $allTags = Arr::flatten(array_map(fn ($dive) => $dive->tags, $dives));
        $ids = Arrg::unique($allTags, "id");
        return Arrg::map($ids, fn ($id) => TagData::fromId($id));
    }

    /**
     * @param Dive[] $dives
     * @return array
     */
    private function mergeSamples(array $dives): ?array
    {
        $orderedDives = Arrg::filter(
            $dives,
            fn ($dive) => $dive->samples !== null && count($dive->samples) > 0
        );

        usort(
            $orderedDives,
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


        $prevDive = array_shift($dives);
        $samples = Arrg::copy($prevDive->samples);

        foreach ($orderedDives as $dive) {
            $prevSample = Arr::last($samples);

            $timeDiff = $dive->date->getTimestamp() - $prevDive->date->getTimestamp();
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

            while ($dive->samples[0]->Time === $dive->samples[1]->Time) {
                $dive->samples[0] = array_merge($dive->samples[0], $dive->samples[1]);
                array_shift($dive->samples);
            }

            foreach ($dive->samples as $sample) {
                $sample->Time += $timeOffset;
                $samples[] = $sample;
            }

            $prevDive = $dive;
        }

        return $samples;
    }
}
