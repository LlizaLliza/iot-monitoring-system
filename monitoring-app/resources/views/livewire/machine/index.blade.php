<div>
    @if (session('message'))
        <div class="mb-4 rounded bg-green-100 p-3 text-green-800">{{ session('message') }}</div>
    @endif

    <div class="mb-4 flex items-center justify-between">
        <input wire:model.live="search" type="text" placeholder="Cari kode/nama mesin..." class="rounded border-gray-300">

        @if (auth()->user()->isAdmin())
            <a href="{{ route('machines.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Tambah Mesin</a>
        @endif
    </div>

    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2">Kode</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">Lokasi</th>
                <th class="border p-2">Tipe</th>
                <th class="border p-2">Status</th>
                @if (auth()->user()->isAdmin())
                    <th class="border p-2">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($machines as $machine)
                <tr wire:key="machine-{{ $machine->id }}">
                    <td class="border p-2">{{ $machine->code }}</td>
                    <td class="border p-2">{{ $machine->name }}</td>
                    <td class="border p-2">{{ $machine->location }}</td>
                    <td class="border p-2">{{ $machine->type }}</td>
                    <td class="border p-2">
                        <span class="{{ $machine->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $machine->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    @if (auth()->user()->isAdmin())
                        <td class="border p-2">
                            <a href="{{ route('machines.edit', $machine) }}" class="text-blue-600">Edit</a>
                            <button wire:click="delete({{ $machine->id }})" wire:confirm="Yakin hapus mesin ini?" class="ml-2 text-red-600">Hapus</button>
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="6" class="border p-4 text-center">Belum ada data mesin.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $machines->links() }}</div>
</div>