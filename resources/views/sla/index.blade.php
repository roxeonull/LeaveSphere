@extends('layouts.app')

@section('title', 'SLA Monitoring')
@section('page-title', 'SLA Monitoring')

@section('content')
<div class="p-6 space-y-5">

    <!-- Header -->
    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">SLA Monitoring</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Track and manage SLA compliance</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Total Pending', 'value' => $stats['total_pending'], 'icon' => 'inbox', 'color' => 'blue'],
            ['label' => 'Breached', 'value' => $stats['breached'], 'icon' => 'x-circle', 'color' => 'red'],
            ['label' => 'At Risk', 'value' => $stats['at_risk'], 'icon' => 'alert-triangle', 'color' => 'amber'],
            ['label' => 'Avg Response', 'value' => $stats['avg_response'], 'icon' => 'check-circle', 'color' => 'green'],
        ] as $stat)
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $stat['label'] }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stat['value'] }}</p>
                </div>
                <div class="w-9 h-9 bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="{{ $stat['icon'] }}" class="w-5 h-5 text-{{ $stat['color'] }}-600"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- SLA Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">SLA Status</h3>
            <p class="text-xs text-gray-400 mt-0.5">Current status of all pending leave requests</p>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($slaRecords as $sla)
            @php
                $user = $sla->leaveRequest->user ?? null;
                $hoursLeft = (int) round(now()->diffInHours($sla->deadline, false));
                $status = $sla->breached ? 'breached' : ($hoursLeft < 4 ? 'warning' : 'safe');
                $remainingLabel = $hoursLeft >= 0 ? $hoursLeft . 'h left' : abs($hoursLeft) . 'h overdue';
            @endphp
            <div class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            {{ $user->initials ?? 'NA' }}
                        </div>
                        <div class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800
                            {{ $status === 'breached' ? 'bg-red-500' : ($status === 'warning' ? 'bg-amber-500' : 'bg-green-500') }}">
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-400">{{ $user->department->name ?? '-' }} · {{ $sla->leaveRequest->leave_type ?? '' }}</p>
                    </div>
                </div>
                <div class="hidden md:block text-right">
                    <p class="text-xs text-gray-500">Submitted: {{ $sla->leaveRequest->created_at->format('Y-m-d H:i') }}</p>
                    <p class="text-xs text-gray-400">Deadline: {{ $sla->deadline->format('Y-m-d H:i') }}</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1.5 rounded-full
                    {{ $status === 'breached' ? 'bg-red-100 text-red-700' : ($status === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                    {{ $remainingLabel }}
                </span>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-sm text-gray-400">No pending requests with SLA tracking.</div>
            @endforelse
        </div>
    </div>

    <!-- Department Performance -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <div class="mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Department Performance</h3>
            <p class="text-xs text-gray-400 mt-0.5">Average approval time by department</p>
        </div>
        <canvas id="deptPerfChart" height="100"></canvas>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deptLabels = @json($deptPerformance->pluck('name'));
    const deptData = @json($deptPerformance->pluck('avg_hours'));

    new Chart(document.getElementById('deptPerfChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [
                {
                    label: 'Avg Approval Time (hours)',
                    data: deptData,
                    backgroundColor: function(ctx) {
                        const val = ctx.parsed.y;
                        return val > 5 ? '#ef4444' : val > 4 ? '#f59e0b' : '#22c55e';
                    },
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ctx.parsed.y + ' hours' } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { callback: v => v + 'h' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
