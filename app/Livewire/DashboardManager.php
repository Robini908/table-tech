<?php

namespace App\Livewire;

use Livewire\Component;

class DashboardManager extends Component

{

    public $activeTab = 1; // Default tab

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
    
    public function render()
    {
        return view('livewire.dashboard-manager');
    }
}
