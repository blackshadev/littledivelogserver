<?php

namespace App\ViewModels\ApiModels;

class BuddyDetailViewModel extends BuddyListViewModel
{
    protected array $visible = ['buddy_id', 'text', 'color', 'dive_count', 'last_dive', 'email', 'buddy_user_id'];

    public function getEmail()
    {
        return $this->buddy->email;
    }

    public function getBuddyUserId()
    {
        return $this->buddy->buddy_user_id;
    }
}
