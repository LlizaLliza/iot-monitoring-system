<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <h1 class="mb-4 text-2xl font-bold">Rekap Output Produksi</h1>

    <div class="mb-4 flex flex-wrap gap-3">
        <div>
            <label class="block text-sm">Dari Tanggal</label>
            <input wire:model.live="startDate" type="date" class="rounded border-gray-300">
        </div>
        <div>
            <label class="block text-sm">Sampai Tanggal</label>
            <input wire:model.live="endDate" type="date" class="rounded border-gray-300">
        </div>
        <div>
            <label class="block text-sm">Kelompokkan Per</label>
            <select wire:model.live="groupBy" class="rounded border-gray-300">
                <option value="day">Hari</option>
                <option value="shift">Shift</option>
                <option value="month">Bulan</option>
            </select>
        </div>
        <div>
            <label class="block text-sm">Mesin</label>
            <select wire:model.live="machineId" class="rounded border-gray-300">
                <option value="">Semua Mesin</option>
                @foreach ($machines as $m)
                    <option value="{{ $m->id }}">{{ $m->code }} - {{ $m->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div wire:loading class="mb-2 text-sm text-gray-500">Memuat data...</div>

    <table class="w-full border-collapse border text-sm">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2">Periode</th>
                <th class="border p-2">Mesin</th>
                <th class="border p-2">Total Output</th>
                <th class="border p-2">Rata-rata Output/Jam</th>
                <th class="border p-2">Uptime %</th>
                <th class="border p-2">Downtime %</th>
                <th class="border p-2">Jumlah Data</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $row)
                <tr>
                    <td class="border p-2">{{ $row->period }}</td>
                    <td class="border p-2">{{ $row->machine_code }} - {{ $row->machine_name }}</td>
                    <td class="border p-2">{{ number_format($row->total_output) }}</td>
                    <td class="border p-2">{{ $row->avg_output_per_hour }}</td>
                    <td class="border p-2 text-green-600">{{ $row->uptime_pct }}%</td>
                    <td class="border p-2 text-red-600">{{ $row->downtime_pct }}%</td>
                    <td class="border p-2">{{ $row->total_readings }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="border p-4 text-center">Tidak ada data untuk rentang ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $reports->links() }}</div>
</div>
