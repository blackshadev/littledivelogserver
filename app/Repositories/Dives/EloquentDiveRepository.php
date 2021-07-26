<?php

declare(strict_types=1);

namespace App\Repositories\Dives;

use App\Domain\Buddies\Entities\Buddy;
use App\Domain\Buddies\Repositories\BuddyRepository;
use App\Domain\Computers\Entities\Computer;
use App\Domain\Computers\Repositories\ComputerRepository;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Entities\DiveTank;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Dives\Repositories\DiveTankRepository;
use App\Domain\Places\Entities\Place;
use App\Domain\Places\Repositories\PlaceRepository;
use App\Domain\Support\Arrg;
use App\Domain\Tags\Entities\Tag;
use App\Domain\Tags\Repositories\TagRepository;
use App\Models\Dive as DiveModel;
use App\Models\DiveTank as DiveTankModel;
use Illuminate\Support\Facades\DB;

final class EloquentDiveRepository implements DiveRepository
{
    public function __construct(
        private PlaceRepository $placeRepository,
        private ComputerRepository $computerRepository,
        private TagRepository $tagRepository,
        private BuddyRepository $buddyRepository,
        private DiveTankRepository $diveTankRepository,
    ) {
    }

    public function findById(int $diveId): Dive
    {
        $model = DiveModel::findOrFail($diveId);
        return $this->createFromModel($model);
    }

    public function save(Dive $dive): void
    {
        DB::transaction(function () use ($dive) {
            if ($dive->isExisting()) {
                $model = DiveModel::findOrFail($dive->getDiveId());
            } else {
                $model = new DiveModel();
                $model->user_id = $dive->getUserId();
            }

            $model->date = $dive->getDate();
            $model->max_depth = $dive->getMaxDepth();
            $model->divetime = $dive->getDivetime();
            $model->samples = $dive->getSamples();
            $model->save();
            $dive->setDiveId($model->id);

            $place = $dive->getPlace();
            $this->setPlace($model, $place);

            $computer = $dive->getComputer();
            $this->setComputer($model, $computer);

            $this->setTags($model, $dive->getTags());

            $this->setBuddies($model, $dive->getBuddies());

            $this->setTanks($model, $dive->getTanks());
        });
    }

    private function setPlace(DiveModel $model, ?Place $place): void
    {
        if ($place === null) {
            $model->place()->disassociate();
            return;
        }

        if (!$place->isExisting()) {
            $this->placeRepository->save($place);
        }

        $model->place_id = $place->getId();
    }

    private function setComputer(
        DiveModel $model,
        ?Computer $computer,
    ) {
        if ($computer === null) {
            $model->computer()->disassociate();
            return;
        }

        $model->computer_id = $computer->getId();
        $this->computerRepository->save($computer);
    }

    private function setTags(
        DiveModel $model,
        array $tags
    ): void {
        $tagsIds = Arrg::map($tags, function (Tag $tag) {
            if (!$tag->isExisting()) {
                $this->tagRepository->save($tag);
            }

            return $tag->getId();
        });

        $model->tags()->sync($tagsIds);
    }

    private function setBuddies(
        DiveModel $model,
        array $buddies
    ): void {
        $buddyIds = Arrg::map($buddies, function (Buddy $buddy) {
            if (!$buddy->isExisting()) {
                $this->buddyRepository->save($buddy);
            }

            return $buddy->getId();
        });
        $model->buddies()->sync($buddyIds);
    }

    private function setTanks(
        DiveModel $model,
        array $tanks
    ): void {
        /** @var DiveTank $tank */
        foreach ($tanks as $tank) {
            $tank->setDiveId($model->id);
            $this->diveTankRepository->save($tank);
        }

        $ids = Arrg::map($tanks, fn (DiveTank $diveTank) => $diveTank->getId());
        $diveTankModelsToRemove = $model->tanks()->get()
            ->filter(fn (DiveTankModel $diveTankModel) => !in_array($diveTankModel->id, $ids, true));

        /** @var DiveTankModel $tankModel */
        foreach ($diveTankModelsToRemove as $tankModel) {
            $tankModel->delete();
        }
    }

    private function createFromModel(DiveModel $model): Dive
    {
        return new Dive(
            diveId: $model->id,
            date: $model->date,
            userId: $model->user_id,
            divetime: $model->divetime,
            maxDepth: $model->max_depth,
            computerId: $model->computer_id,
            fingerprint: $model->fingerprint,
            samples: $model->samples,
            place: $model->place_id ? $this->placeRepository->findById($model->place_id) : null,
            tags: $model->tags()->select(['dive_tag.tag_id'])->pluck('tag_id')->map(fn (int $id) => $this->tagRepository->findById($id))->toArray(),
            buddies: $model->buddies()->select(['buddy_dive.buddy_id'])->pluck('buddy_id')->map(fn (int $id) => $this->buddyRepository->findById($id))->toArray(),
            tanks: $model->tanks()->select(['dive_tanks.id'])->pluck('id')->map(fn (int $id) => $this->diveTankRepository->findById($id))->toArray(),
        );
    }
}
