<?php

declare(strict_types=1);

namespace App\Repositories\Dives;

use App\Application\Dives\CommandObjects\FindDivesCommand;
use App\Application\Dives\Services\DiveFinder;
use App\Domain\Dives\Entities\DiveSummary;
use App\Domain\Dives\Repositories\DiveSummaryRepository;
use App\Domain\Support\Arrg;
use App\Models\Dive as DiveModel;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\Results;
use JeroenG\Explorer\Application\SearchCommand;
use JeroenG\Explorer\Domain\Query\Query;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Syntax\Matching;
use JeroenG\Explorer\Domain\Syntax\Nested;
use JeroenG\Explorer\Domain\Syntax\Range;
use JeroenG\Explorer\Domain\Syntax\Term;

class ExplorerDiveFinder implements DiveFinder
{
    public function __construct(
        private IndexAdapterInterface $adapter,
        private DiveSummaryRepository $repository,
    ) {
    }

    /** @return DiveSummary[] */
    public function search(FindDivesCommand $command): array
    {
        $boolQuery = $this->createBoolQuery($command);

        $searchCommand = $this->createExplorerSearchCommand($boolQuery);

        $results = $this->adapter->search($searchCommand);

        return $this->transformResults($results);
    }

    private function createBoolQuery(FindDivesCommand $command): BoolQuery
    {
        $toplevelQuery = new BoolQuery();
        $toplevelQuery->filter(new Term('user_id', $command->getUserId()));

        if ($command->getKeywords()) {
            $keywordQuery = new BoolQuery();
            $keywordQuery->should(new Nested('place', new Matching('place.name', $command->getKeywords())));
            $keywordQuery->should(new Nested('buddies', new Matching('buddies.name', $command->getKeywords())));
            $keywordQuery->should(new Nested('tags', new Matching('tags.text', $command->getKeywords())));
            $toplevelQuery->must($keywordQuery);
        }

        if ($command->getAfter() !== null) {
            $toplevelQuery->must(new Range('date', [
                'gt' => $command->getAfter()
            ]));
        }
        if ($command->getBefore() !== null) {
            $toplevelQuery->must(new Range('date', [
                'lt' => $command->getBefore()
            ]));
        }
        if ($command->getPlaceId() !== null) {
            $toplevelQuery->must(new Nested('place', new Term('place.id', $command->getPlaceId())));
        }
        if ($command->getBuddies() !== null) {
            foreach ($command->getBuddies() as $buddyId) {
                $toplevelQuery->must(new Nested('buddies', new Term('buddies.id', $buddyId)));
            }
        }
        if ($command->getTags() !== null) {
            foreach ($command->getTags() as $tagId) {
                $toplevelQuery->must(new Nested('tags', new Term('tags.id', $tagId)));
            }
        }

        return $toplevelQuery;
    }

    private function createExplorerSearchCommand(BoolQuery $boolQuery): SearchCommand
    {
        $searchCommand = new SearchCommand();
        $searchCommand->setQuery(Query::with($boolQuery));
        $searchCommand->setIndex((new DiveModel())->searchableAs());

        return $searchCommand;
    }

    private function transformResults(Results $results): array
    {
        $ids = Arrg::map(
            $results->hits(),
            fn ($result) => $result['_source']['id']
        );

        return $this->repository->findByIds($ids);
    }
}
