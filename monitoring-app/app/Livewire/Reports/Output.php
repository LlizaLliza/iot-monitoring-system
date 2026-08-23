<?php

namespace App\Livewire\Reports;

use App\Models\Machine;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Output extends Component
{
    use WithPagination;

    public string $startDate;
    public string $endDate;
    public string $groupBy = 'day'; // day | month | shift
    public string $machineId = '';

    public function mount(): void
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updating($property): void
    {
        if (in_array($property, ['startDate', 'endDate', 'groupBy', 'machineId'])) {
            $this->resetPage();
        }
    }

    private function groupExpression(): string
    {
        return match ($this->groupBy) {
            'month' => "FORMAT(sensor_readings.recorded_at, 'yyyy-MM')",
            'shift' => "CONCAT(CONVERT(varchar, CAST(sensor_readings.recorded_at AS DATE), 23), ' - ',
                CASE
                    WHEN DATEPART(HOUR, sensor_readings.recorded_at) >= 6 AND DATEPART(HOUR, sensor_readings.recorded_at) < 14 THEN 'Shift 1 (06-14)'
                    WHEN DATEPART(HOUR, sensor_readings.recorded_at) >= 14 AND DATEPART(HOUR, sensor_readings.recorded_at) < 22 THEN 'Shift 2 (14-22)'
                    ELSE 'Shift 3 (22-06)'
                END)",
            default => 'CONVERT(varchar, CAST(sensor_readings.recorded_at AS DATE), 23)',
        };
    }

    private function estimatedHoursPerGroup(): int
    {
        return match ($this->groupBy) {
            'month' => 24 * 30,
            'shift' => 8,
            default => 24,
        };
    }

    public function render()
    {
        $groupExpr = $this->groupExpression();

        $reports = DB::table('sensor_readings')
            ->join('machines', 'machines.id', '=', 'sensor_readings.machine_id')
            ->whereBetween('sensor_readings.recorded_at', ["{$this->startDate} 00:00:00", "{$this->endDate} 23:59:59"])
            ->when($this->machineId, fn ($q) => $q->where('sensor_readings.machine_id', $this->machineId))
            ->selectRaw("
                sensor_readings.machine_id,
                machines.name as machine_name,
                machines.code as machine_code,
                {$groupExpr} as period,
                SUM(sensor_readings.output_qty) as total_output,
                COUNT(*) as total_readings,
                SUM(CASE WHEN sensor_readings.status = 'ON' THEN 1 ELSE 0 END) as on_readings
            ")
            ->groupBy('sensor_readings.machine_id', 'machines.name', 'machines.code')
            ->groupByRaw($groupExpr)
            ->orderByDesc('period')
            ->paginate(20);

        $hoursPerGroup = $this->estimatedHoursPerGroup();

        $reports->getCollection()->transform(function ($row) use ($hoursPerGroup) {
            $row->uptime_pct = $row->total_readings > 0
                ? round(($row->on_readings / $row->total_readings) * 100, 1)
                : 0;
            $row->downtime_pct = round(100 - $row->uptime_pct, 1);
            $row->avg_output_per_hour = $hoursPerGroup > 0
                ? round($row->total_output / $hoursPerGroup, 2)
                : 0;

            return $row;
        });

        $machines = Machine::orderBy('name')->get();

        return view('livewire.reports.output', compact('reports', 'machines'));
    }
}
