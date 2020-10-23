<?php

declare(strict_types=1);

namespace App\ViewModels\ApiModels;

use App\Models\User;
use App\ViewModels\ViewModel;

class UserProfileViewModel extends ViewModel
{
    protected array $visible = [
        'user_id', 'name', 'email',
        'inserted', 'dive_count', 'computer_count',
        'buddy_count', 'tag_count',
    ];

    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUserId()
    {
        return $this->user->id;
    }

    public function getName()
    {
        return $this->user->name;
    }

    public function getEmail()
    {
        return $this->user->email;
    }

    public function getInserted()
    {
        return $this->user->created_at;
    }

    public function getTagCount()
    {
        return $this->user->tags()->count();
    }

    public function getBuddyCount()
    {
        return $this->user->buddies()->count();
    }

    public function getComputerCount()
    {
        return $this->user->computers()->count();
    }

    public function getDiveCount()
    {
        return $this->user->dives()->count();
    }
}
