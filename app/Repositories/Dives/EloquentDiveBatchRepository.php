<?php

declare(strict_types=1);

namespace App\Repositories\Dives;

use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Repositories\DiveBatchRepository;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Factories\Dives\DiveFactory;
use App\Domain\Support\Arrg;
use App\Models\Dive as DiveModel;
use Illuminate\Support\Facades\DB;
use Webmozart\Assert\Assert;

final class EloquentDiveBatchRepository implements DiveBatchRepository
{
    public function __construct(
        private DiveFactory $diveFactory,
        private DiveRepository $diveRepository,
    ) {
    }

    public function findByIds(array $diveIds): array
    {
        return DiveModel::whereIn('id', $diveIds)
            ->get()
            ->map(fn (DiveModel $model) => $this->diveFactory->createFrom($model))
            ->toArray();
    }

    public function replace(array $divesToReplace, Dive $newDive): void
    {
        Assert::allIsInstanceOf($divesToReplace, Dive::class);

        DB::transaction(function () use ($divesToReplace, $newDive): void {
            $ids = Arrg::call($divesToReplace, 'getDiveId');
            DiveModel::whereIn('id', $ids)->delete();

            $this->diveRepository->save($newDive);
        });
    }
}
