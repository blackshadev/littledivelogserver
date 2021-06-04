<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\FromEloquentCollection;
use App\Application\ViewModels\ViewModel;
use App\Models\Buddy;

class BuddyListViewModel extends ViewModel
{
    use FromEloquentCollection;

    protected array $visible = ['buddy_id', 'text', 'color', 'dive_count', 'last_dive'];

    protected Buddy $buddy;

    public function __construct(Buddy $buddy)
    {
        $this->buddy = $buddy;
    }

    public function getBuddyId()
    {
        return $this->buddy->id;
    }

    public function getText()
    {
        return $this->buddy->name;
    }

    public function getColor()
    {
        return $this->buddy->color;
    }

    public function getDiveCount()
    {
        return $this->buddy->dives()->count();
    }

    public function getLastDive()
    {
        return $this->buddy->dives()->max('date');
    }
}
