<?php

declare(strict_types=1);

namespace App\Application\Dives\Services\Mergers;

use App\Application\Dives\Exceptions\CannotMergeTankException;
use App\Domain\Dives\Entities\DiveTank;
use App\Domain\Dives\ValueObjects\GasMixture;
use App\Domain\Dives\ValueObjects\TankPressures;
use App\Domain\Support\ArrayUtil;
use App\Domain\Support\Arrg;
use App\Domain\Support\Math;

final class DiveTankMergerImpl implements DiveTankMerger
{
    /**
     * @inheritdoc
     */
    public function mergeForDives(array $dives): array
    {
        if (empty($dives)) {
            return [];
        }

        $groupedByTank = ArrayUtil::transpose(Arrg::call($dives, 'getTanks'));
        return Arrg::map($groupedByTank, fn (array $tanks) => $this->merge($tanks));
    }

    /**
     * @inheritdoc
     */
    public function merge(array $tanks): ?DiveTank
    {
        if (empty($tanks)) {
            return null;
        }

        $exception = $this->getException($tanks);
        if (!is_null($exception)) {
            throw $exception;
        }

        $begin = Math::max(Arrg::call($tanks, 'getPressures.getBegin'));
        $end = Math::min(Arrg::call($tanks, 'getPressures.getEnd'));

        return DiveTank::new(
            diveId: null,
            volume: $tanks[0]->getVolume(),
            pressures: new TankPressures(
                type: $tanks[0]->getPressures()->getType(),
                begin: $begin,
                end: $end,
            ),
            gasMixture: new GasMixture(
                oxygen: $tanks[0]->getGasMixture()->getOxygen(),
            )
        );
    }

    private function getException(array $tanks): ?CannotMergeTankException
    {
        if (count(array_unique(Arrg::notNull(Arrg::call($tanks, 'getPressures.getType')))) > 1) {
            return CannotMergeTankException::differentPressureTypes();
        }

        if (count(array_unique(Arrg::notNull(Arrg::call($tanks, 'getVolume')))) > 1) {
            return CannotMergeTankException::differentVolumes();
        }

        if (count(array_unique(Arrg::notNull(Arrg::call($tanks, 'getGasMixture.getOxygen')))) > 1) {
            return CannotMergeTankException::differentGasmixture();
        }

        return null;
    }
}
