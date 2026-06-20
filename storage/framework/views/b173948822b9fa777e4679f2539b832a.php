<?php $__env->startSection('title', 'Analytics & Insights'); ?>
<?php $__env->startSection('page-title', 'Analytics & Insights'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 space-y-5" x-data="{ activeTab: 'monthly', filterPeriod: 'monthly' }">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Analytics & Insights</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Comprehensive leave analytics and trends</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Period Filter -->
            <select x-model="filterPeriod"
                class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm rounded-lg px-3 py-2 border border-gray-200 dark:border-gray-600 focus:outline-none">
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="yearly">Yearly</option>
            </select>
            <!-- Export Dropdown -->
            <div class="relative" x-data="{ openExport: false }">
                <button @click="openExport = !openExport"
                    class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                    <i data-lucide="download" class="w-4 h-4"></i> Export Report
                </button>
                <div x-show="openExport" @click.away="openExport = false"
                    class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 py-2">
                    <a href="<?php echo e(route('analytics.export', 'pdf')); ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <i data-lucide="file-text" class="w-4 h-4 text-red-500"></i> Export PDF
                    </a>
                    <a href="<?php echo e(route('analytics.export', 'excel')); ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <i data-lucide="table" class="w-4 h-4 text-green-500"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'Total Requests', 'value' => 342, 'change' => '+12%', 'up' => true, 'icon' => 'file-text', 'color' => 'blue'],
            ['label' => 'Approval Rate', 'value' => '78%', 'change' => '+5%', 'up' => true, 'icon' => 'trending-up', 'color' => 'green'],
            ['label' => 'Avg Days/Request', 'value' => '2.4', 'change' => '-0.3', 'up' => false, 'icon' => 'calendar', 'color' => 'purple'],
            ['label' => 'Total Days Used', 'value' => 856, 'change' => '+8%', 'up' => true, 'icon' => 'calendar-check', 'color' => 'amber'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?php echo e($kpi['label']); ?></p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?php echo e($kpi['value']); ?></p>
                    <p class="text-xs mt-1 <?php echo e($kpi['up'] ? 'text-green-600' : 'text-red-500'); ?> flex items-center gap-0.5">
                        <i data-lucide="<?php echo e($kpi['up'] ? 'trending-up' : 'trending-down'); ?>" class="w-3 h-3"></i>
                        <?php echo e($kpi['change']); ?> from last month
                    </p>
                </div>
                <div class="w-9 h-9 bg-<?php echo e($kpi['color']); ?>-100 dark:bg-<?php echo e($kpi['color']); ?>-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="<?php echo e($kpi['icon']); ?>" class="w-5 h-5 text-<?php echo e($kpi['color']); ?>-600"></i>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Charts Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex border-b border-gray-200 dark:border-gray-700 px-5">
            <?php $__currentLoopData = ['monthly' => 'Monthly Trends', 'department' => 'Department Comparison', 'types' => 'Leave Types']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button @click="activeTab = '<?php echo e($tab); ?>'"
                :class="activeTab === '<?php echo e($tab); ?>' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-400 hover:text-gray-600'"
                class="py-3.5 px-4 text-sm font-medium transition-colors">
                <?php echo e($label); ?>

            </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Monthly Trends -->
        <div x-show="activeTab === 'monthly'" class="p-5">
            <div class="mb-3">
                <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Monthly Leave Trends</h4>
                <p class="text-xs text-gray-400">Request volume and approval rates over time</p>
            </div>
            <canvas id="monthlyChart" height="140"></canvas>
        </div>

        <!-- Department Comparison -->
        <div x-show="activeTab === 'department'" class="p-5">
            <div class="mb-3">
                <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Department Comparison</h4>
                <p class="text-xs text-gray-400">Leave request count by department</p>
            </div>
            <canvas id="deptCompChart" height="140"></canvas>
        </div>

        <!-- Leave Types Pie -->
        <div x-show="activeTab === 'types'" class="p-5">
            <div class="mb-3">
                <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Leave Types Distribution</h4>
                <p class="text-xs text-gray-400">Breakdown by leave category</p>
            </div>
            <div class="flex items-center justify-center">
                <div class="w-72">
                    <canvas id="leaveTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trends
    fetch('<?php echo e(route("analytics.monthly-trends")); ?>')
        .then(res => res.json())
        .then(data => {
            new Chart(document.getElementById('monthlyChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Total Requests',
                            data: data.total,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.08)',
                            fill: true, tension: 0.4, pointRadius: 4
                        },
                        {
                            label: 'Approved',
                            data: data.approved,
                            borderColor: '#22c55e',
                            backgroundColor: 'transparent',
                            tension: 0.4, pointRadius: 4
                        },
                        {
                            label: 'Rejected',
                            data: data.rejected,
                            borderColor: '#ef4444',
                            backgroundColor: 'transparent',
                            tension: 0.4, pointRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 11 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                    }
                }
            });
        });

    // Department Comparison
    fetch('<?php echo e(route("analytics.department-comparison")); ?>')
        .then(res => res.json())
        .then(data => {
            const colors = { 'Annual Leave': '#3b82f6', 'Sick Leave': '#ef4444', 'Personal Leave': '#f59e0b' };
            const datasets = Object.keys(data.datasets).map(type => ({
                label: type,
                data: data.datasets[type],
                backgroundColor: colors[type] || '#6b7280',
                borderRadius: 4,
            }));

            new Chart(document.getElementById('deptCompChart').getContext('2d'), {
                type: 'bar',
                data: { labels: data.labels, datasets: datasets },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });

    // Leave Types Pie
    fetch('<?php echo e(route("analytics.leave-type-distribution")); ?>')
        .then(res => res.json())
        .then(data => {
            new Chart(document.getElementById('leaveTypeChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.data,
                        backgroundColor: ['#3b82f6', '#ef4444', '#f59e0b', '#8b5cf6', '#06b6d4'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '60%',
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } }
                    }
                }
            });
        });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\leavesphere\resources\views/analytics/index.blade.php ENDPATH**/ ?>