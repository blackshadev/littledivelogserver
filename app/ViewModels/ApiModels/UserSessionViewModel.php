<?php

declare(strict_types=1);

namespace App\ViewModels\ApiModels;

use App\Models\RefreshToken;
use App\ViewModels\FromEloquentCollection;
use App\ViewModels\ViewModel;

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
