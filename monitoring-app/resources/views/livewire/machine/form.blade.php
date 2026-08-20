<div>
    <h2 class="mb-4 text-xl font-semibold">{{ $machine ? 'Edit Mesin' : 'Tambah Mesin' }}</h2>

    <form wire:submit="save" class="space-y-4">
        <div>
            <label>Kode Mesin</label>
            <input wire:model="code" type="text" class="w-full rounded border-gray-300">
            @error('code') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Nama Mesin</label>
            <input wire:model="name" type="text" class="w-full rounded border-gray-300">
            @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Lokasi</label>
            <input wire:model="location" type="text" class="w-full rounded border-gray-300">
        </div>

        <div>
            <label>Tipe Mesin</label>
            <input wire:model="type" type="text" class="w-full rounded border-gray-300">
        </div>

        <div>
            <label>Tanggal Instalasi</label>
            <input wire:model="install_date" type="date" class="w-full rounded border-gray-300">
        </div>

        <div>
            <label><input wire:model="is_active" type="checkbox"> Aktif</label>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white">Simpan</button>
            <a href="{{ route('machines.index') }}" class="rounded bg-gray-300 px-4 py-2">Batal</a>
        </div>
    </form>
</div>