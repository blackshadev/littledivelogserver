<?php

declare(strict_types=1);

namespace App\Services\Dives;

use App\Application\Dives\CommandObjects\FindDivesCommand;
use App\Application\Dives\Services\DiveFinder;
use App\Domain\Dives\Repositories\DiveSummaryRepository;
use App\Domain\Support\Arrg;
use App\Models\Dive;
use Typesense\LaravelTypesense\Typesense;

final class TypesenseDiveFinder implements DiveFinder
{
    public function __construct(
        private readonly Typesense $typesense,
        private readonly DiveSummaryRepository $diveSummaryRepository,
    ) {
    }

    public function search(FindDivesCommand $findDivesCommand): array
    {
        $model = new Dive();
        $documents = $this->typesense->getCollectionIndex($model)->getDocuments();

        $results = $documents->search([
            'q' => $findDivesCommand->keywords,
            'query_by' => implode(',', $model->typesenseQueryBy()),
            'filter_by' => $this->createFilterBy($findDivesCommand),
        ]);

        $ids = Arrg::map(
            $results['hits'],
            static fn ($result) => (int)$result['document']['id'],
        );

        return $this->diveSummaryRepository->findByIds($ids);
    }

    private function createFilterBy(FindDivesCommand $findDivesCommand): string
    {
        $filters = [sprintf("user_id:=%d", $findDivesCommand->userId)];

        if ($findDivesCommand->buddies) {
            $filters[] = sprintf('buddies.id:=[%s]', implode(',', $findDivesCommand->buddies));
        }

        if ($findDivesCommand->tags) {
            $filters[] = sprintf('tags.id:=[%s]', implode(',', $findDivesCommand->tags));
        }

        if ($findDivesCommand->placeId) {
            $filters[] = sprintf('place.id:=%d', $findDivesCommand->placeId);
        }

        if ($findDivesCommand->before) {
            $filters[] = sprintf('date:<%d', $findDivesCommand->before->getTimestamp());
        }

        if ($findDivesCommand->after) {
            $filters[] = sprintf('date:>%d', $findDivesCommand->after->getTimestamp());
        }

        return implode(' && ', $filters);
    }
}
