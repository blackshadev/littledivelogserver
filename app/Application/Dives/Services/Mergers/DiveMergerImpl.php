<?php

declare(strict_types=1);

namespace App\Application\Dives\Services\Mergers;

use App\Application\Dives\Exceptions\CannotMergeDivesException;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Entities\DiveWithSamples;
use App\Domain\Support\ArrayUtil;
use App\Domain\Support\Arrg;

final class DiveMergerImpl implements DiveMerger
{
    public const HOURS_IN_SECONDS = 60 * 60;

    private const MAX_TIME_DISTANCE_IN_HOURS = 2;

    public function __construct(
        private DiveTankMerger $diveTankMerger,
        private DiveEntityMerger $diveEntityMerger,
        private DiveSampleCombiner $diveSampleStitcher,
    ) {
    }

    /**
     * @param Dive[] $dives
     */
    public function merge(array $dives): Dive
    {
        $exception = $this->canMergeDives($dives);
        if (!is_null($exception)) {
            throw $exception;
        }

        $divesWithComputerPreferred = $this->preferDivesWithComputer($dives);

        $dive = Dive::new(
            userId: $dives[0]->getUserId(),
            date: min(Arrg::call($divesWithComputerPreferred, 'getDate')),
            divetime: array_sum(Arrg::call($divesWithComputerPreferred, 'getDivetime')),
            maxDepth: max(Arrg::call($divesWithComputerPreferred, 'getMaxDepth')),
            computer: Arrg::firstNotNull(Arrg::call($divesWithComputerPreferred, 'getComputer')),
            place: Arrg::firstNotNull(Arrg::call($dives, 'getPlace')),
            tanks: $this->diveTankMerger->mergeForDives($dives),
            tags: $this->diveEntityMerger->unique(ArrayUtil::flatten(Arrg::call($dives, 'getTags'))),
            buddies: $this->diveEntityMerger->unique(ArrayUtil::flatten(Arrg::call($dives, 'getBuddies')))
        );

        $samples = $this->diveSampleStitcher->combine($dives);
        if (empty($samples)) {
            return $dive;
        }

        return DiveWithSamples::addSamples($dive, $samples);
    }

    /**
     * @param Dive[] $dives
     * @return Dive[]
     */
    private function preferDivesWithComputer(array $dives): array
    {
        $divesWithComputers = array_filter($dives, fn (Dive $dive) => !is_null($dive->getComputer()));

        if (!empty($divesWithComputers)) {
            return $divesWithComputers;
        }

        return $dives;
    }

    private function canMergeDives(array $dives): ?\Exception
    {
        if (count($dives) < 2) {
            return CannotMergeDivesException::tooFewDives();
        }

        /** @var \DateTimeInterface $minDate */
        $minDate = min(...Arrg::call($dives, 'getDate'));
        /** @var \DateTimeInterface $maxDate */
        $maxDate = max(...Arrg::call($dives, 'getDate'));

        $diffInHours = ($maxDate->getTimestamp() - $minDate->getTimestamp()) / self::HOURS_IN_SECONDS;
        if ($diffInHours > self::MAX_TIME_DISTANCE_IN_HOURS) {
            return CannotMergeDivesException::timeDifferenceToBig();
        }

        $places = array_unique(Arrg::notNull(Arrg::call($dives, 'getPlace.getId')));
        if (count($places) > 1) {
            return CannotMergeDivesException::placesDifference();
        }

        $computers = array_unique(Arrg::notNull(Arrg::call($dives, 'getComputer.getId')));
        if (count($computers) > 1) {
            return CannotMergeDivesException::computerDifference();
        }

        $users = array_unique(Arrg::notNull(Arrg::call($dives, 'getUserId')));
        if (count($users) > 1) {
            return CannotMergeDivesException::userDifference();
        }

        return null;
    }
}
