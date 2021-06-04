<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\FromEloquentCollection;
use App\Application\ViewModels\ViewModel;
use App\Models\Computer;

class ComputerListViewModel extends ViewModel
{
    use FromEloquentCollection;

    protected array $visible = [
        'computer_id', 'serial', 'vendor', 'model', 'type', 'dive_count', 'last_read', 'last_fingerprint',
    ];

    private Computer $computer;

    public function __construct(Computer $computer)
    {
        $this->computer = $computer;
    }

    public function getComputerId()
    {
        return $this->computer->id;
    }

    public function getSerial()
    {
        return $this->computer->serial;
    }

    public function getVendor()
    {
        return $this->computer->vendor;
    }

    public function getModel()
    {
        return $this->computer->model;
    }

    public function getType()
    {
        return $this->computer->type;
    }

    public function getDiveCount()
    {
        return $this->computer->dives()->count();
    }

    public function getLastRead()
    {
        return $this->computer->last_read;
    }

    public function getLastFingerprint()
    {
        return $this->computer->last_fingerprint;
    }
}
