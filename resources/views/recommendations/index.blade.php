@extends('layouts.app')

@section('title', 'Smart Recommendations')
@section('page-title', 'Smart Recommendations')

@section('content')
<div class="p-6 space-y-5" x-data="{ activeTab: 'recommendations' }">

    <!-- Header -->
    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Smart Recommendations</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">AI-powered insights and predictions for leave management</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">High Risk Depts</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['high_risk_depts'] }}</p>
                </div>
                <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="alert-octagon" class="w-5 h-5 text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Active Conflicts</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['active_conflicts'] }}</p>
                </div>
                <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Predicted Spikes</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['predicted_spikes'] }}</p>
                </div>
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Recommendations</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['recommendations'] }}</p>
                </div>
                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="lightbulb" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex border-b border-gray-200 dark:border-gray-700 px-5">
            @foreach(['recommendations' => 'Recommendations', 'conflicts' => 'Conflict Detection', 'risk' => 'Risk Scoring', 'patterns' => 'Patterns'] as $tab => $label)
            <button @click="activeTab = '{{ $tab }}'"
                :class="activeTab === '{{ $tab }}' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-400 hover:text-gray-600'"
                class="py-3.5 px-4 text-sm font-medium transition-colors">
                {{ $label }}
            </button>
            @endforeach
        </div>

        <!-- Recommendations Tab (= patterns rendered as actionable cards) -->
        <div x-show="activeTab === 'recommendations'" class="p-5 space-y-4">
            @forelse($patterns as $rec)
            @php
                $levelStyles = [
                    'high' => ['border-red-200 dark:border-red-900/40', 'bg-red-50 dark:bg-red-900/10', 'bg-red-600', 'bg-red-600 hover:bg-red-700'],
                    'medium' => ['border-amber-200 dark:border-amber-900/40', 'bg-amber-50 dark:bg-amber-900/10', 'bg-amber-500', 'bg-amber-500 hover:bg-amber-600'],
                    'low' => ['border-green-200 dark:border-green-900/40', 'bg-green-50 dark:bg-green-900/10', 'bg-green-600', 'bg-green-600 hover:bg-green-700'],
                ];
                [$borderClr, $bgClr, $badgeClr, $btnClr] = $levelStyles[$rec['level']];
            @endphp
            <div class="border {{ $borderClr }} {{ $bgClr }} rounded-xl p-5">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badgeClr }} text-white">{{ $rec['level'] }}</span>
                            <span class="text-xs text-gray-500">{{ $rec['tag'] }}</span>
                        </div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $rec['title'] }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $rec['description'] }}</p>
                        <div class="mt-3 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-500"></i>
                            <span>Suggested Action: {{ $rec['action'] }}</span>
                        </div>
                    </div>
                    <button class="flex items-center gap-1.5 px-4 py-2 {{ $btnClr }} text-white text-xs rounded-lg font-medium transition-colors ml-4 flex-shrink-0">
                        Take Action <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center text-sm text-gray-400 py-10">
                No notable patterns detected right now. Recommendations will appear here as leave data accumulates.
            </div>
            @endforelse
        </div>

        <!-- Conflict Detection Tab -->
        <div x-show="activeTab === 'conflicts'" class="p-5 space-y-4">
            @forelse($conflicts as $conflict)
            @php
                $isHigh = $conflict['risk'] === 'High Risk';
            @endphp
            <div class="border {{ $isHigh ? 'border-red-200 dark:border-red-900/40 bg-red-50 dark:bg-red-900/10' : 'border-amber-200 dark:border-amber-900/40 bg-amber-50 dark:bg-amber-900/10' }} rounded-xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 {{ $isHigh ? 'bg-red-100' : 'bg-amber-100' }} rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="users" class="w-5 h-5 {{ $isHigh ? 'text-red-600' : 'text-amber-600' }}"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Team Overlap — {{ $conflict['department'] }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $conflict['count'] }} of {{ $conflict['total'] }} employees have overlapping leave on {{ \Carbon\Carbon::parse($conflict['date'])->format('M j, Y') }}. Only {{ $conflict['coverage'] }}% team coverage.
                        </p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $isHigh ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">{{ $conflict['risk'] }}</span>
                            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($conflict['date'])->format('M j, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-sm text-gray-400 py-10">No scheduling conflicts detected.</div>
            @endforelse
        </div>

        <!-- Risk Scoring Tab -->
        <div x-show="activeTab === 'risk'" class="p-5">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 text-xs font-semibold text-gray-500 uppercase">Employee</th>
                            <th class="text-left py-3 text-xs font-semibold text-gray-500 uppercase">Leave Type</th>
                            <th class="text-left py-3 text-xs font-semibold text-gray-500 uppercase">Risk Score</th>
                            <th class="text-left py-3 text-xs font-semibold text-gray-500 uppercase">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($riskScores as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="py-3.5 font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                            <td class="py-3.5 text-gray-500">{{ $row['type'] }}</td>
                            <td class="py-3.5">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                    {{ $row['risk'] === 'High' ? 'bg-red-100 text-red-700' : ($row['risk'] === 'Medium' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $row['risk'] }}
                                </span>
                            </td>
                            <td class="py-3.5 text-gray-500 text-xs">{{ $row['reason'] }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-10 text-center text-gray-400">No pending requests to score.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Patterns Tab -->
        <div x-show="activeTab === 'patterns'" class="p-5">
            <canvas id="patternChart" height="120"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('patternChart');
    if (ctx) {
        fetch('{{ route("recommendations.pattern-data") }}')
            .then(res => res.json())
            .then(data => {
                new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            { label: 'Annual Leave', data: data.annual, backgroundColor: '#3b82f6', borderRadius: 4 },
                            { label: 'Sick Leave', data: data.sick, backgroundColor: '#ef4444', borderRadius: 4 },
                            { label: 'Personal Leave', data: data.personal, backgroundColor: '#f59e0b', borderRadius: 4 },
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            });
    }
});
</script>
@endpush
