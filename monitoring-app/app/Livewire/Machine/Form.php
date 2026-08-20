<?php

namespace App\Livewire\Machine;

use App\Models\Machine;
use Livewire\Component;

class Form extends Component
{
    public ?Machine $machine = null;

    public string $code = '';
    public string $name = '';
    public string $location = '';
    public string $type = '';
    public string $install_date = '';
    public bool $is_active = true;

    public function mount(?Machine $machine = null): void
    {
        if ($machine?->exists) {
            $this->machine = $machine;
            $this->code = $machine->code;
            $this->name = $machine->name;
            $this->location = $machine->location ?? '';
            $this->type = $machine->type ?? '';
            $this->install_date = $machine->install_date?->format('Y-m-d') ?? '';
            $this->is_active = $machine->is_active;
        }
    }

    protected function rules(): array
    {
        $machineId = $this->machine?->id;

        return [
            'code' => 'required|string|max:50|unique:machines,code,' . $machineId,
            'name' => 'required|string|max:150',
            'location' => 'nullable|string|max:150',
            'type' => 'nullable|string|max:100',
            'install_date' => 'nullable|date',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $this->validate();

        Machine::updateOrCreate(
            ['id' => $this->machine?->id],
            [
                'code' => $this->code,
                'name' => $this->name,
                'location' => $this->location,
                'type' => $this->type,
                'install_date' => $this->install_date ?: null,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('message', $this->machine ? 'Mesin berhasil diperbarui.' : 'Mesin berhasil ditambahkan.');

        return redirect()->route('machines.index');
    }

    public function render()
    {
        return view('livewire.machine.form');
    }
}
