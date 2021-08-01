<?php

declare(strict_types=1);

namespace App\Http\Requests\Computers;

use App\Http\Requests\AuthenticatedRequest;

final class ComputerUpsertRequest extends AuthenticatedRequest
{
    public function rules()
    {
        return [
            'serial' => 'required|integer',
            'vendor' => 'required|string',
            'model' => 'required|integer',
            'type' => 'required|integer',
            'name' => 'required|string',
        ];
    }
}
