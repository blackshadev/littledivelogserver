<?php

declare(strict_types=1);

namespace App\Services\Places;

use App\Application\Places\CommandObjects\FindPlaceCommand;
use App\Application\Places\Services\PlaceFinder;
use App\Domain\Places\Entities\Place;
use App\Domain\Support\Arrg;
use App\Models\Place as PlaceModel;
use Typesense\LaravelTypesense\Typesense;

final class TypesensePlaceFinder implements PlaceFinder
{
    public function __construct(private readonly Typesense $typesense)
    {
    }

    public function find(FindPlaceCommand $command): array
    {
        $model = new PlaceModel();
        $results = $this->typesense->getCollectionIndex($model)->getDocuments()->search([
            'q' => $command->keywords,
            'sort_by' => sprintf('_eval(created_by:%d):desc,_text_match:desc', $command->userId),
            'query_by' => implode(',', $model->typesenseQueryBy()),
            'infix' => 'always'
        ]);

        return Arrg::map(
            $results['hits'],
            static fn ($result) => Place::existing(
                id: (int)$result['document']['id'],
                createdBy: (int)$result['document']['created_by'],
                name: $result['document']['name'],
                countryCode: $result['document']['country_code']
            )
        );
    }
}
