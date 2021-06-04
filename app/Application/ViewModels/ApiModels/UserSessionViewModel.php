<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\FromEloquentCollection;
use App\Application\ViewModels\ViewModel;
use App\Models\RefreshToken;

class UserSessionViewModel extends ViewModel
{
    use FromEloquentCollection;

    protected array $visible = ['token', 'last_used', 'last_ip', 'inserted', 'insert_ip'];

    protected RefreshToken $refreshToken;

    public function __construct(RefreshToken $token)
    {
        $this->refreshToken = $token;
    }

    public function getToken()
    {
        return $this->refreshToken->id;
    }

    public function getLastUsed()
    {
        return $this->refreshToken->updated_at;
    }

    public function getInserted()
    {
        return $this->refreshToken->created_at;
    }

    public function getLastIp()
    {
        return '';
    }

    public function getInsertIp()
    {
        return '';
    }
}
