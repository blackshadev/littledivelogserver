<?php

declare(strict_types=1);

namespace App\Services\Places;

use App\Application\Places\CommandObjects\FindPlaceCommand;
use App\Application\Places\Services\PlaceFinder;
use App\Models\Place as PlaceModel;
use Typesense\Client;

final class TypesensePlaceFinder implements PlaceFinder
{
    public function __construct(private readonly Client $client)
    {
    }

    public function find(FindPlaceCommand $command): array
    {
        $q = [
            'searches' => [
                [
                    'q' => $command->getKeywords(),
                    'filter_by' => "created_by:" . $command->getUserId(),
                ],
                [
                    'q' => $command->getKeywords(),
                ],
            ],
        ];
        $result = $this->client->getMultiSearch()->perform($q, [
            'collection' => (new PlaceModel())->searchableAs(),
            'query_by' => implode(',', (new PlaceModel())->typesenseQueryBy()),
        ]);

        dd($result);
    }
}
