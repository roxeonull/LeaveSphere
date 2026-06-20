@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Admin Overview')

@section('content')
<div class="p-6 space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Admin Overview</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Real-time HR analytics and leave management dashboard</p>
        </div>
        <button onclick="location.reload()"
            class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i> Refresh
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Pending Approvals -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pending Approvals</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['pending_approvals'] ?? 24 }}</p>
                    <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
                        <i data-lucide="trending-up" class="w-3 h-3"></i>
                        +5 from yesterday
                    </p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- SLA Risk Alerts -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">SLA Risk Alerts</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['sla_alerts'] ?? 8 }}</p>
                    <p class="text-xs text-red-600 mt-1 flex items-center gap-1">
                        <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                        +3 requiring attention
                    </p>
                </div>
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                </div>
            </div>
        </div>

        <!-- Predicted Leave Spikes -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Predicted Leave Spikes</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['spike_percentage'] >= 0 ? '+' : '' }}{{ $stats['spike_percentage'] }}%</p>
                    <p class="text-xs {{ $stats['spike_percentage'] >= 0 ? 'text-amber-600' : 'text-green-600' }} mt-1 flex items-center gap-1">
                        <i data-lucide="trending-up" class="w-3 h-3"></i>
                        vs previous 7 days
                    </p>
                </div>
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-5 h-5 text-amber-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Employees -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Employees</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_employees'] ?? 156 }}</p>
                    <p class="text-xs text-blue-600 mt-1 flex items-center gap-1">
                        <i data-lucide="users" class="w-3 h-3"></i>
                        +3 this month
                    </p>
                </div>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Row: Pending Approvals + SLA Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Pending Approvals Widget -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Pending Approvals</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Leave requests awaiting your review</p>
                </div>
                <a href="{{ route('approvals.index') }}"
                   class="text-xs text-blue-600 hover:text-blue-700 flex items-center gap-1 font-medium">
                    View All <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-700">
                @php
                $pendingRequests = $pendingRequests ?? [
                    ['initials' => 'JD', 'name' => 'John Doe', 'type' => 'Annual Leave', 'days' => '3 day(s)', 'date' => '2026-02-24', 'color' => 'bg-blue-500'],
                    ['initials' => 'SS', 'name' => 'Sarah Smith', 'type' => 'Sick Leave', 'days' => '1 day(s)', 'date' => '2026-02-25', 'color' => 'bg-green-500'],
                    ['initials' => 'MJ', 'name' => 'Mike Johnson', 'type' => 'Personal Leave', 'days' => '2 day(s)', 'date' => '2026-02-26', 'color' => 'bg-purple-500'],
                    ['initials' => 'EB', 'name' => 'Emily Brown', 'type' => 'Annual Leave', 'days' => '5 day(s)', 'date' => '2026-02-27', 'color' => 'bg-orange-500'],
                ];
                @endphp
                @foreach($pendingRequests as $req)
                <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 {{ $req['color'] }} rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ $req['initials'] }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $req['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $req['type'] }} · {{ $req['days'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">{{ $req['date'] }}</span>
                        <a href="{{ route('approvals.index') }}"
                           class="w-6 h-6 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                            <i data-lucide="arrow-right" class="w-3 h-3 text-gray-500 dark:text-gray-400"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- SLA Risk Alerts -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">SLA Risk Alerts</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Requests approaching SLA breach</p>
                </div>
                <a href="{{ route('sla.index') }}"
                   class="text-xs text-blue-600 hover:text-blue-700 flex items-center gap-1 font-medium">
                    View All <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-700">
                @php
                $slaAlerts = $slaAlerts ?? [
                    ['initials' => 'AW', 'name' => 'Alice Williams', 'type' => 'Sick Leave', 'time' => '3h left', 'level' => 'danger'],
                    ['initials' => 'BW', 'name' => 'Bob Wilson', 'type' => 'Annual Leave', 'time' => '6h left', 'level' => 'warning'],
                    ['initials' => 'CD', 'name' => 'Carol Davis', 'type' => 'Personal Leave', 'time' => '12h left', 'level' => 'safe'],
                ];
                @endphp
                @foreach($slaAlerts as $alert)
                <div class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0
                            {{ $alert['level'] === 'danger' ? 'bg-red-500' : ($alert['level'] === 'warning' ? 'bg-amber-500' : 'bg-green-500') }}">
                            {{ $alert['initials'] }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alert['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $alert['type'] }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                        {{ $alert['level'] === 'danger' ? 'bg-red-100 text-red-700' : ($alert['level'] === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                        {{ $alert['time'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bottom Row: Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Leave Spikes Prediction Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <div class="mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Leave Spikes Prediction</h3>
                <p class="text-xs text-gray-400 mt-0.5">AI-predicted leave demand for next 7 days</p>
            </div>
            <canvas id="spikesChart" height="180"></canvas>
        </div>

        <!-- Department Load Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <div class="mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Department Load</h3>
                <p class="text-xs text-gray-400 mt-0.5">Current leave utilization by department</p>
            </div>
            <canvas id="deptChart" height="180"></canvas>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Leave Spikes Chart
    fetch('{{ route("dashboard.spikes-data") }}')
        .then(res => res.json())
        .then(data => {
            new Chart(document.getElementById('spikesChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Scheduled Leaves',
                        data: data.data,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.08)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3b82f6',
                        pointRadius: 4,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 }, stepSize: 1 } },
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                    }
                }
            });
        });

    // Department Load Chart
    fetch('{{ route("dashboard.department-load-data") }}')
        .then(res => res.json())
        .then(data => {
            new Chart(document.getElementById('deptChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Employees on Leave',
                        data: data.data,
                        backgroundColor: ['#3b82f6', '#6366f1', '#8b5cf6', '#ec4899', '#f59e0b'],
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 }, stepSize: 1 } },
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                    }
                }
            });
        });
});
</script>
@endpush
