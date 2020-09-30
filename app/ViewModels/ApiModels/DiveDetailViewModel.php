<?php

namespace App\ViewModels\ApiModels;

use App\Models\Dive;
use App\ViewModels\ViewModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DiveDetailViewModel extends ViewModel
{
    protected $visible = [
        'dive_id', 'date', 'divetime',
        'max_depth', 'place', 'buddies',
        'tags', 'tanks'
    ];
    private Dive $dive;

    public function __construct(Dive $dive)
    {
        $this->dive = $dive;
    }

    public function getDiveId()
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

    public function getMaxDepth(): ?int
    {
        return $this->dive->max_depth;
    }

    public function getTags(): Collection
    {
        return DiveTagsViewModel::fromCollection($this->dive->tags);
    }

    public function getBuddies(): Collection
    {
        return DiveBuddiesViewModel::fromCollection($this->dive->buddies);
    }

    public function getPlace(): PlaceViewModel
    {
        return new PlaceViewModel($this->dive->place);
    }

    public function getTanks(): Collection
    {
        return DiveTankViewModel::fromCollection($this->dive->tanks);
    }
}
