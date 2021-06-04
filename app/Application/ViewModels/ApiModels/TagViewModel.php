<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\FromEloquentCollection;
use App\Application\ViewModels\ViewModel;
use App\Models\Tag;

class TagViewModel extends ViewModel
{
    use FromEloquentCollection;

    protected array $visible = ['tag_id', 'text', 'color', 'dive_count', 'last_dive'];

    protected Tag $tag;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function getTagId()
    {
        return $this->tag->id;
    }

    public function getText()
    {
        return $this->tag->text;
    }

    public function getColor()
    {
        return $this->tag->color;
    }

    public function getDiveCount()
    {
        return $this->tag->dives()->count();
    }

    public function getLastDive()
    {
        return $this->tag->dives()->max('date');
    }
}
