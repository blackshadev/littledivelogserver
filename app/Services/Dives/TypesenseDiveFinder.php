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
            'q' => $findDivesCommand->getKeywords(),
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
        $filters = [sprintf("user_id:=%d", $findDivesCommand->getUserId())];

        if ($findDivesCommand->getBuddies()) {
            $filters[] = sprintf('buddies.id:=[%s]', implode(',', $findDivesCommand->getBuddies()));
        }

        if ($findDivesCommand->getTags()) {
            $filters[] = sprintf('tags.id:=[%s]', implode(',', $findDivesCommand->getTags()));
        }

        if ($findDivesCommand->getPlaceId()) {
            $filters[] = sprintf('place.id:=%d', $findDivesCommand->getPlaceId());
        }

        if ($findDivesCommand->getBefore()) {
            $filters[] = sprintf('date:<%d', $findDivesCommand->getBefore()->getTimestamp());
        }

        if ($findDivesCommand->getAfter()) {
            $filters[] = sprintf('date:>%d', $findDivesCommand->getAfter()->getTimestamp());
        }

        return implode(' && ', $filters);
    }
}
