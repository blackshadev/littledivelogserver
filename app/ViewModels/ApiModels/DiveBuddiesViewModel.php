<?php


namespace App\ViewModels\ApiModels;


use App\Models\Buddy;
use App\ViewModels\FromEloquentCollection;
use App\ViewModels\ViewModel;

class DiveBuddiesViewModel extends ViewModel
{
    use FromEloquentCollection;

    protected array $visible = ["buddy_id", "color", "text"];

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
        return $this->buddy->text;
    }

}
