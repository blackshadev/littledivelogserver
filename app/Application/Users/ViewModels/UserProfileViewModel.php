<?php

declare(strict_types=1);

namespace App\Application\Users\ViewModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Users\Entities\DetailUser;

final class UserProfileViewModel extends ViewModel
{
    protected array $visible = [
        'user_id', 'name', 'email',
        'inserted', 'dive_count', 'computer_count',
        'buddy_count', 'tag_count',
    ];

    public function __construct(
        private int $userId,
        private string $name,
        private string $email,
        private \DateTimeInterface $inserted,
        private int $tagCount,
        private int $buddyCount,
        private int $computerCount,
        private int $diveCount,
    ) {
    }

    public static function fromDetailUser(DetailUser $user)
    {
        return new self(
            userId: $user->getUserId(),
            name: $user->getName(),
            email: $user->getEmail(),
            inserted: $user->getInserted(),
            diveCount: $user->getDiveCount(),
            buddyCount: $user->getBuddyCount(),
            computerCount: $user->getComputerCount(),
            tagCount: $user->getTagCount(),
        );
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getInserted()
    {
        return $this->inserted;
    }

    public function getTagCount()
    {
        return $this->tagCount;
    }

    public function getBuddyCount()
    {
        return $this->buddyCount;
    }

    public function getComputerCount()
    {
        return $this->computerCount;
    }

    public function getDiveCount()
    {
        return $this->diveCount;
    }
}
