<?php

namespace App\Livewire;

use Livewire\Component;

class AllStockFunctionalities extends Component
{
    public $activeTab = 1; // Default tab

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.all-stock-functionalities');
    }
}
