<?php

declare(strict_types=1);

namespace App\ViewModels\ApiModels;

use App\Models\Dive;
use App\ViewModels\FromEloquentCollection;
use App\ViewModels\ViewModel;
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

    public function getPlace(): PlaceViewModel
    {
        return new PlaceViewModel($this->dive->place);
    }
}
