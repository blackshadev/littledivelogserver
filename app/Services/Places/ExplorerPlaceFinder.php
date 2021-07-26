<?php

declare(strict_types=1);

namespace App\Services\Places;

use App\Application\Places\CommandObjects\FindPlaceCommand;
use App\Application\Places\Services\PlaceFinder;
use App\Domain\Places\Entities\Place;
use App\Domain\Support\Arrg;
use App\Explorer\Syntax\Wildcard;
use App\Models\Place as PlaceModel;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\Results;
use JeroenG\Explorer\Application\SearchCommand;
use JeroenG\Explorer\Domain\Query\Query;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Syntax\Matching;
use JeroenG\Explorer\Domain\Syntax\Term;

class ExplorerPlaceFinder implements PlaceFinder
{
    public function __construct(
        private IndexAdapterInterface $adapter
    ) {
    }

    public function find(FindPlaceCommand $command): array
    {
        $boolQuery = $this->createBoolQuery($command);

        $searchCommand = $this->createExplorerSearchCommand($boolQuery);

        $results = $this->adapter->search($searchCommand);

        return $this->transformResults($results);
    }

    private function createExplorerSearchCommand(BoolQuery $boolQuery): SearchCommand
    {
        $searchCommand = new SearchCommand();
        $searchCommand->setQuery(Query::with($boolQuery));
        $searchCommand->setIndex((new PlaceModel())->searchableAs());

        return $searchCommand;
    }

    private function createBoolQuery(FindPlaceCommand $command): BoolQuery
    {
        $boolQuery = new BoolQuery();
        if ($command->getCountry()) {
            $boolQuery->filter(new Term('country_code', $command->getCountry()));
        }
        if ($command->getKeywords()) {
            $innerQuery = new BoolQuery();
            $innerQuery->should(new Matching('name', $command->getKeywords()));
            $innerQuery->should(new Wildcard(
                field: 'name',
                value: "*{$command->getKeywords()}*"
            ));

            $boolQuery->must($innerQuery);
        }
        if ($command->getUserId()) {
            $boolQuery->should(new Term('created_by', $command->getUserId()));
        }

        return $boolQuery;
    }

    private function transformResults(Results $results): array
    {
        return Arrg::map(
            $results->hits(),
            fn ($result) => new Place(
                id: $result['_source']['id'],
                name: $result['_source']['name'],
                countryCode: $result['_source']['country_code'],
                createdBy:  $result['_source']['created_by']
            )
        );
    }
}
