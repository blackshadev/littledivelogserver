<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\FromEloquentCollection;
use App\Application\ViewModels\ViewModel;
use App\Models\Dive;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DiveListViewModel extends ViewModel
{
    use FromEloquentCollection;

    protected array $visible = ['dive_id', 'divetime', 'date', 'tags', 'place'];

    private Dive $dive;

    public function __construct(Dive $dive)
    {
        $this->dive = $dive;
    }

    public function getDiveId(): int
    {
        return $this->dive->id;
    }

    public function getDivetime(): ?int
    {
        return $this->dive->divetime;
    }

    public function getDate(): ?Carbon
    {
        return $this->dive->date;
    }

    public function getTags(): Collection
    {
        return DiveTagsViewModel::fromCollection($this->dive->tags);
    }

    public function getPlace(): ?PlaceViewModel
    {
        return $this->dive->place ? new PlaceViewModel($this->dive->place) : null;
    }
}
