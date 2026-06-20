<?php $__env->startSection('title', 'SLA Monitoring'); ?>
<?php $__env->startSection('page-title', 'SLA Monitoring'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 space-y-5">

    <!-- Header -->
    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">SLA Monitoring</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Track and manage SLA compliance</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'Total Pending', 'value' => $stats['total_pending'], 'icon' => 'inbox', 'color' => 'blue'],
            ['label' => 'Breached', 'value' => $stats['breached'], 'icon' => 'x-circle', 'color' => 'red'],
            ['label' => 'At Risk', 'value' => $stats['at_risk'], 'icon' => 'alert-triangle', 'color' => 'amber'],
            ['label' => 'Avg Response', 'value' => $stats['avg_response'], 'icon' => 'check-circle', 'color' => 'green'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?php echo e($stat['label']); ?></p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?php echo e($stat['value']); ?></p>
                </div>
                <div class="w-9 h-9 bg-<?php echo e($stat['color']); ?>-100 dark:bg-<?php echo e($stat['color']); ?>-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="<?php echo e($stat['icon']); ?>" class="w-5 h-5 text-<?php echo e($stat['color']); ?>-600"></i>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- SLA Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">SLA Status</h3>
            <p class="text-xs text-gray-400 mt-0.5">Current status of all pending leave requests</p>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php $__empty_1 = true; $__currentLoopData = $slaRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sla): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $user = $sla->leaveRequest->user ?? null;
                $hoursLeft = (int) round(now()->diffInHours($sla->deadline, false));
                $status = $sla->breached ? 'breached' : ($hoursLeft < 4 ? 'warning' : 'safe');
                $remainingLabel = $hoursLeft >= 0 ? $hoursLeft . 'h left' : abs($hoursLeft) . 'h overdue';
            ?>
            <div class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            <?php echo e($user->initials ?? 'NA'); ?>

                        </div>
                        <div class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800
                            <?php echo e($status === 'breached' ? 'bg-red-500' : ($status === 'warning' ? 'bg-amber-500' : 'bg-green-500')); ?>">
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($user->name ?? 'Unknown'); ?></p>
                        <p class="text-xs text-gray-400"><?php echo e($user->department->name ?? '-'); ?> · <?php echo e($sla->leaveRequest->leave_type ?? ''); ?></p>
                    </div>
                </div>
                <div class="hidden md:block text-right">
                    <p class="text-xs text-gray-500">Submitted: <?php echo e($sla->leaveRequest->created_at->format('Y-m-d H:i')); ?></p>
                    <p class="text-xs text-gray-400">Deadline: <?php echo e($sla->deadline->format('Y-m-d H:i')); ?></p>
                </div>
                <span class="text-xs font-semibold px-3 py-1.5 rounded-full
                    <?php echo e($status === 'breached' ? 'bg-red-100 text-red-700' : ($status === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700')); ?>">
                    <?php echo e($remainingLabel); ?>

                </span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-5 py-10 text-center text-sm text-gray-400">No pending requests with SLA tracking.</div>
            <?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deptLabels = <?php echo json_encode($deptPerformance->pluck('name'), 15, 512) ?>;
    const deptData = <?php echo json_encode($deptPerformance->pluck('avg_hours'), 15, 512) ?>;

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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\leavesphere\resources\views/sla/index.blade.php ENDPATH**/ ?>