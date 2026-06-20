<?php $__env->startSection('title', 'Smart Recommendations'); ?>
<?php $__env->startSection('page-title', 'Smart Recommendations'); ?>

<?php $__env->startSection('content'); ?>
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
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?php echo e($stats['high_risk_depts']); ?></p>
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
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?php echo e($stats['active_conflicts']); ?></p>
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
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?php echo e($stats['predicted_spikes']); ?></p>
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
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?php echo e($stats['recommendations']); ?></p>
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
            <?php $__currentLoopData = ['recommendations' => 'Recommendations', 'conflicts' => 'Conflict Detection', 'risk' => 'Risk Scoring', 'patterns' => 'Patterns']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button @click="activeTab = '<?php echo e($tab); ?>'"
                :class="activeTab === '<?php echo e($tab); ?>' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-400 hover:text-gray-600'"
                class="py-3.5 px-4 text-sm font-medium transition-colors">
                <?php echo e($label); ?>

            </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Recommendations Tab (= patterns rendered as actionable cards) -->
        <div x-show="activeTab === 'recommendations'" class="p-5 space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $patterns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $levelStyles = [
                    'high' => ['border-red-200 dark:border-red-900/40', 'bg-red-50 dark:bg-red-900/10', 'bg-red-600', 'bg-red-600 hover:bg-red-700'],
                    'medium' => ['border-amber-200 dark:border-amber-900/40', 'bg-amber-50 dark:bg-amber-900/10', 'bg-amber-500', 'bg-amber-500 hover:bg-amber-600'],
                    'low' => ['border-green-200 dark:border-green-900/40', 'bg-green-50 dark:bg-green-900/10', 'bg-green-600', 'bg-green-600 hover:bg-green-700'],
                ];
                [$borderClr, $bgClr, $badgeClr, $btnClr] = $levelStyles[$rec['level']];
            ?>
            <div class="border <?php echo e($borderClr); ?> <?php echo e($bgClr); ?> rounded-xl p-5">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full <?php echo e($badgeClr); ?> text-white"><?php echo e($rec['level']); ?></span>
                            <span class="text-xs text-gray-500"><?php echo e($rec['tag']); ?></span>
                        </div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm"><?php echo e($rec['title']); ?></h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><?php echo e($rec['description']); ?></p>
                        <div class="mt-3 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-500"></i>
                            <span>Suggested Action: <?php echo e($rec['action']); ?></span>
                        </div>
                    </div>
                    <button class="flex items-center gap-1.5 px-4 py-2 <?php echo e($btnClr); ?> text-white text-xs rounded-lg font-medium transition-colors ml-4 flex-shrink-0">
                        Take Action <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center text-sm text-gray-400 py-10">
                No notable patterns detected right now. Recommendations will appear here as leave data accumulates.
            </div>
            <?php endif; ?>
        </div>

        <!-- Conflict Detection Tab -->
        <div x-show="activeTab === 'conflicts'" class="p-5 space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $conflicts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conflict): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $isHigh = $conflict['risk'] === 'High Risk';
            ?>
            <div class="border <?php echo e($isHigh ? 'border-red-200 dark:border-red-900/40 bg-red-50 dark:bg-red-900/10' : 'border-amber-200 dark:border-amber-900/40 bg-amber-50 dark:bg-amber-900/10'); ?> rounded-xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 <?php echo e($isHigh ? 'bg-red-100' : 'bg-amber-100'); ?> rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="users" class="w-5 h-5 <?php echo e($isHigh ? 'text-red-600' : 'text-amber-600'); ?>"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Team Overlap — <?php echo e($conflict['department']); ?></h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <?php echo e($conflict['count']); ?> of <?php echo e($conflict['total']); ?> employees have overlapping leave on <?php echo e(\Carbon\Carbon::parse($conflict['date'])->format('M j, Y')); ?>. Only <?php echo e($conflict['coverage']); ?>% team coverage.
                        </p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-xs px-2 py-0.5 rounded-full <?php echo e($isHigh ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'); ?>"><?php echo e($conflict['risk']); ?></span>
                            <span class="text-xs text-gray-400"><?php echo e(\Carbon\Carbon::parse($conflict['date'])->format('M j, Y')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center text-sm text-gray-400 py-10">No scheduling conflicts detected.</div>
            <?php endif; ?>
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
                        <?php $__empty_1 = true; $__currentLoopData = $riskScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="py-3.5 font-medium text-gray-900 dark:text-white"><?php echo e($row['name']); ?></td>
                            <td class="py-3.5 text-gray-500"><?php echo e($row['type']); ?></td>
                            <td class="py-3.5">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                    <?php echo e($row['risk'] === 'High' ? 'bg-red-100 text-red-700' : ($row['risk'] === 'Medium' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700')); ?>">
                                    <?php echo e($row['risk']); ?>

                                </span>
                            </td>
                            <td class="py-3.5 text-gray-500 text-xs"><?php echo e($row['reason']); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="py-10 text-center text-gray-400">No pending requests to score.</td></tr>
                        <?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('patternChart');
    if (ctx) {
        fetch('<?php echo e(route("recommendations.pattern-data")); ?>')
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\leavesphere\resources\views/recommendations/index.blade.php ENDPATH**/ ?>