<div>
    <h1 class="mb-4 text-2xl font-bold">Dashboard Monitoring Mesin</h1>

    <div class="mb-4 flex gap-3">
        <input wire:model.live="search" type="text" placeholder="Cari kode/nama..." class="rounded border-gray-300">

        <select wire:model.live="filterLocation" class="rounded border-gray-300">
            <option value="">Semua Lokasi</option>
            @foreach ($locations as $loc)
                <option value="{{ $loc }}">{{ $loc }}</option>
            @endforeach
        </select>

        <select wire:model.live="filterType" class="rounded border-gray-300">
            <option value="">Semua Tipe</option>
            @foreach ($types as $type)
                <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>
    </div>

    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2">Kode</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">Lokasi</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Maintenance</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($machines as $machine)
                <tr wire:key="dash-{{ $machine->id }}">
                    <td class="border p-2">{{ $machine->code }}</td>
                    <td class="border p-2">{{ $machine->name }}</td>
                    <td class="border p-2">{{ $machine->location }}</td>
                    <td class="border p-2">
                        <span id="status-{{ $machine->id }}" class="font-semibold {{ $machine->current_status === 'ON' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $machine->current_status ?? 'OFF' }}
                        </span>
                    </td>
                    <td class="border p-2">
                        <span id="maint-{{ $machine->id }}" class="rounded bg-yellow-200 px-2 py-1 text-xs" style="display: {{ $machine->needs_maintenance ? 'inline-block' : 'none' }}">
                            ⚠ Perlu Maintenance
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="border p-4 text-center">Belum ada mesin.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div wire:ignore>
        <script>
            (function () {
                function connect() {
                    const source = new EventSource('{{ route('dashboard.stream') }}');

                    source.onmessage = (e) => {
                        const machines = JSON.parse(e.data);
                        machines.forEach((m) => {
                            const statusEl = document.getElementById(`status-${m.id}`);
                            if (statusEl) {
                                statusEl.textContent = m.status;
                                statusEl.className = 'font-semibold ' + (m.status === 'ON' ? 'text-green-600' : 'text-red-600');
                            }

                            const maintEl = document.getElementById(`maint-${m.id}`);
                            if (maintEl) {
                                maintEl.style.display = m.needs_maintenance ? 'inline-block' : 'none';
                            }
                        });
                    };

                    source.onerror = () => {
                        source.close();
                        setTimeout(connect, 2000);
                    };
                }

                connect();
            })();
        </script>
    </div>
</div>