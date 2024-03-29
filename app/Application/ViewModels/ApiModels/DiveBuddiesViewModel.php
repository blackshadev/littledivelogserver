<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\FromEloquentCollection;
use App\Application\ViewModels\ViewModel;
use App\Models\Buddy;

final class DiveBuddiesViewModel extends ViewModel
{
    use FromEloquentCollection;

    protected array $visible = ['buddy_id', 'color', 'text'];

    private Buddy $buddy;

    public function __construct(Buddy $buddy)
    {
        $this->buddy = $buddy;
    }

    public function getBuddyId()
    {
        return $this->buddy->id;
    }

    public function getColor()
    {
        return $this->buddy->color;
    }

    public function getText()
    {
        return $this->buddy->name;
    }
}
