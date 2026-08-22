<?php

namespace App\Livewire;

use App\Models\Machine;
use Livewire\Component;

class Dashboard extends Component
{
    public string $search = '' ;
    public string $filterLocation = '';
    public string $filterType = '';

    public function render()
    {
         $machines = Machine::query()
            ->withLiveStatus()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%"))
            ->when($this->filterLocation, fn ($q) => $q->where('location', $this->filterLocation))
            ->when($this->filterType, fn ($q) => $q->where('type', $this->filterType))
            ->where('is_active', true)
            ->get();

        $locations = Machine::query()->distinct()->pluck('location')->filter();
        $types = Machine::query()->distinct()->pluck('type')->filter();

        return view('livewire.dashboard', compact('machines', 'locations', 'types'));
    }
}
