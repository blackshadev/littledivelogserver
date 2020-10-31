<?php

declare(strict_types=1);

namespace App\Services\Services\Merger;

use App\DataTransferObjects\BuddyData;
use App\DataTransferObjects\NewDiveData;
use App\DataTransferObjects\PlaceData;
use App\DataTransferObjects\TagData;
use App\DataTransferObjects\TankData;
use App\Helpers\Arrg;
use App\Models\Dive;
use App\Models\DiveTank;
use App\Services\Repositories\DiveRepository;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class DiveMergerService
{
    private const MAX_TIME_DISTANCE_IN_HOURS = 2;

    private DiveRepository $diveRepository;

    public function __construct(DiveRepository $diveRepository)
    {
        $this->diveRepository = $diveRepository;
    }

    /** @var Dive[] $dives */
    public function mergeDives(array $dives)
    {
        $exception = $this->canMergeDives($dives);
        if ($exception !== null) {
            throw $exception;
        }

        $target = new NewDiveData();
        $target->setUser($dives[0]->user);

        $divesWithComputerPreferred = $this->preferDivesWithComputer($dives);

        $target->setDate(min(...Arr::get($divesWithComputerPreferred, 'date')));
        $target->setMaxDepth(max(...Arr::get($divesWithComputerPreferred, 'max_depth')));
        $target->setPlace(PlaceData::fromId(Arrg::firstNotNull($dives, 'place_id')));
        $target->setComputerId(Arrg::firstNotNull($dives, 'computer_id'));

        $target->setTanks($this->mergeTanks($dives));
        $target->setBuddies($this->mergeBuddies($dives));
        $target->setTags($this->mergeTags($dives));

        $target->setSamples($this->mergeSamples($dives));

        $this->diveRepository->update(new Dive(), $target);
    }

    /**
     * @param Dive[] $dives
     */
    private function canMergeDives(array $dives): ?CannotMergeDivesException
    {
        if (count($dives) < 1) {
            return CannotMergeDivesException::tooFewDives();
        }

        /** @var Carbon $minDate */
        $minDate = min(...Arr::get($dives, 'date'));
        $maxDate = max(...Arr::get($dives, 'date'));

        if ($minDate->diffInHours($maxDate) > self::MAX_TIME_DISTANCE_IN_HOURS) {
            return CannotMergeDivesException::timeDifferenceToBig();
        }

        $places = array_unique($this->notNull(Arr::get($dives, 'place_id')));
        if (count($places) > 1) {
            return CannotMergeDivesException::placesDifference();
        }

        $computers = array_unique($this->notNull(Arr::get($dives, 'computer_id')));
        if (count($computers) > 1) {
            return CannotMergeDivesException::computerDifference();
        }

        $users = array_unique($this->notNull(Arr::get($dives, 'user_id')));
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
            $pressures->setBegin(min($pressures->getBegin(), $diveTank->pressure_begin));
            $pressures->setEnd(max($pressures->getEnd(), $diveTank->pressure_end));
            $pressures->setType($diveTank->pressure_type);
        }

        return $tanks;
    }

    /**
     * @param Dive[] $dives
     * @return BuddyData[]
     */
    private function mergeBuddies(array $dives): array
    {
        $allBuddies = Arr::flatten(array_map(fn ($dive) => $dive->buddies, $dives));
        $ids = Arrg::unique($allBuddies, "id");
        return array_map(fn ($id) => BuddyData::fromId($id), $ids);
    }

    /**
     * @param Dive[] $dives
     * @return TagData[]
     */
    private function mergeTags(array $dives): array
    {
        $allTags = Arr::flatten(array_map(fn ($dive) => $dive->tags, $dives));
        $ids = Arrg::unique($allTags, "id");
        return array_map(fn ($id) => TagData::fromId($id), $ids);
    }

    /**
     * @param Dive[] $dives
     * @return array
     */
    private function mergeSamples(array $dives): array
    {
        $orderedDives = array_merge([], $dives);

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

        foreach ($dives as $dive) {
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
