<?php

declare(strict_types=1);

namespace App\Repositories\Dives;

use App\Domain\Dives\Entities\DiveSummary;
use App\Domain\Dives\Repositories\DiveSummaryRepository;
use App\Domain\Places\Entities\Place;
use App\Domain\Tags\Entities\Tag;
use App\Domain\Users\Entities\User;
use App\Models\Dive;
use App\Models\Place as PlaceModel;
use App\Models\Tag as TagModel;

final class EloquentDiveSummaryRepository implements DiveSummaryRepository
{
    /** @return DiveSummary[] */
    public function listForUser(User $user): array
    {
        return Dive::with(['buddies', 'tags'])
            ->where('user_id', $user->getId())->orderBy('date')
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn (Dive $dive) => $this->createDiveSummaryFromModel($dive))
            ->toArray();
    }

    public function findByIds(array $ids): array
    {
        return Dive::with(['buddies', 'tags'])
            ->whereIn('id', $ids)
            ->get()
            ->map(fn (Dive $dive) => $this->createDiveSummaryFromModel($dive))
            ->toArray();
    }

    private function createDiveSummaryFromModel(Dive $dive): DiveSummary
    {
        /** @var PlaceModel|null $place */
        $place = $dive->place()->first();
        $tags = $dive->tags()->get()
            ->map(fn (TagModel $tag) => $this->createTagFromModel($tag))
            ->toArray();

        return new DiveSummary(
            diveId: $dive->id,
            divetime: $dive->divetime,
            date: $dive->date,
            tags: $tags,
            place: $place !== null ? $this->createPlaceFromModel($place) : null,
        );
    }

    private function createPlaceFromModel(PlaceModel $model): Place
    {
        return Place::existing(
            id: $model->id,
            name: $model->name,
            countryCode: $model->country_code,
            createdBy: $model->created_by,
        );
    }

    private function createTagFromModel(TagModel $tag): Tag
    {
        return new Tag(
            id: $tag->id,
            userId: $tag->user_id,
            text: $tag->text,
            color: $tag->color
        );
    }
}
