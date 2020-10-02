<?php

namespace App\ViewModels\ApiModels;

use App\Models\Computer;
use App\ViewModels\FromEloquentCollection;
use App\ViewModels\ViewModel;

class ComputerListViewModel extends ViewModel
{
    use FromEloquentCollection;

    protected array $visible = ['computer_id', 'serial'];

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
}
