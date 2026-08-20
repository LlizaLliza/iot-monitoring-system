<?php

namespace App\Livewire\Machine;

use App\Models\Machine;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterLocation = '';
    public string $filterStatus = '';

    public function delete(int $id): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        Machine::findOrFail($id)->delete();
        session()->flash('message', 'Mesin berhasil dihapus.');
    }

    public function render()
    {
        $machines = Machine::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%"))
            ->when($this->filterLocation, fn ($q) => $q->where('location', $this->filterLocation))
            ->when($this->filterStatus !== '', fn ($q) => $q->where('is_active', $this->filterStatus))
            ->latest()
            ->paginate(15);

        return view('livewire.machine.index', compact('machines'));
    }
}
