<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\FromEloquentCollection;
use App\Application\ViewModels\ViewModel;
use App\Models\Tag;

class DiveTagsViewModel extends ViewModel
{
    use FromEloquentCollection;

    protected array $visible = ['tag_id', 'color', 'text'];

    private Tag $tag;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function getTagId()
    {
        return $this->tag->id;
    }

    public function getColor()
    {
        return $this->tag->color;
    }

    public function getText()
    {
        return $this->tag->text;
    }
}
