<?php

declare(strict_types=1);

namespace App\Repositories\Dives;

use App\Domain\Buddies\Repositories\BuddyRepository;
use App\Domain\Computers\Repositories\ComputerRepository;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Repositories\DiveTankRepository;
use App\Domain\Places\Repositories\PlaceRepository;
use App\Domain\Tags\Repositories\TagRepository;
use App\Models\Dive as DiveModel;

class DiveFactory
{
    public function __construct(
        private PlaceRepository $placeRepository,
        private TagRepository $tagRepository,
        private BuddyRepository $buddyRepository,
        private DiveTankRepository $diveTankRepository,
        private ComputerRepository $computerRepository,
    ) {
    }

    public function createFromModel(DiveModel $model): Dive
    {
        return new Dive(
            diveId: $model->id,
            date: $model->date->toDateTimeImmutable(),
            userId: $model->user_id,
            divetime: $model->divetime,
            maxDepth: $model->max_depth,
            computerId: $model->computer_id ? $this->computerRepository->findById($model->computer_id) : null,
            fingerprint: $model->fingerprint,
            samples: $model->samples,
            place: $model->place_id ? $this->placeRepository->findById($model->place_id) : null,
            tags: $model->tags()->select(['dive_tag.tag_id'])->pluck('tag_id')->map(fn (int $id) => $this->tagRepository->findById($id))->toArray(),
            buddies: $model->buddies()->select(['buddy_dive.buddy_id'])->pluck('buddy_id')->map(fn (int $id) => $this->buddyRepository->findById($id))->toArray(),
            tanks: $model->tanks()->select(['dive_tanks.id'])->pluck('id')->map(fn (int $id) => $this->diveTankRepository->findById($id))->toArray(),
        );
    }
}
