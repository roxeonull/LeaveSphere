<?php $__env->startSection('title', 'Delegation Management'); ?>
<?php $__env->startSection('page-title', 'Delegation Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 space-y-5" x-data="delegationPage()">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Delegation Management</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage approval delegations and delegation windows</p>
        </div>
        <button @click="openCreate()"
            class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> New Delegation
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'Active Delegations', 'value' => $stats['active'], 'icon' => 'users', 'color' => 'blue'],
            ['label' => 'Scheduled', 'value' => $stats['scheduled'], 'icon' => 'calendar', 'color' => 'purple'],
            ['label' => 'This Month', 'value' => $stats['this_month'], 'icon' => 'bar-chart', 'color' => 'green'],
            ['label' => 'Expired', 'value' => $stats['expired'], 'icon' => 'clock', 'color' => 'gray'],
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

    <!-- Delegations Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Delegations</h3>
            <p class="text-xs text-gray-400 mt-0.5">Manage your approval delegations</p>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php $__empty_1 = true; $__currentLoopData = $delegations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $del): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $now = now();
                $status = $now->lt($del->start_date) ? 'scheduled' : ($now->gt($del->end_date) ? 'expired' : 'active');
            ?>
            <div class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        <?php echo e(strtoupper(substr($del->delegate->name ?? '?', 0, 1))); ?>

                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($del->delegate->name ?? 'Unknown'); ?></p>
                        <p class="text-xs text-gray-400">Delegated from <?php echo e($del->delegator->name ?? 'Unknown'); ?></p>
                    </div>
                </div>
                <div class="text-xs text-gray-500 hidden md:block">
                    <?php echo e($del->start_date->format('Y-m-d')); ?> – <?php echo e($del->end_date->format('Y-m-d')); ?>

                </div>
                <div class="flex-wrap gap-1 hidden lg:flex">
                    <?php $__currentLoopData = ($del->permissions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full"><?php echo e(str_replace('_', ' ', $perm)); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                        <?php echo e($status === 'active' ? 'badge-approved' : ($status === 'scheduled' ? 'badge-scheduled' : 'badge-expired')); ?>">
                        <?php echo e($status); ?>

                    </span>
                    <?php if($status === 'active'): ?>
                    <button @click="revoke(<?php echo e($del->id); ?>)"
                        class="text-xs text-red-500 hover:text-red-700 px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Revoke</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-5 py-10 text-center text-sm text-gray-400">No delegations found.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create Delegation Modal -->
    <div x-show="showCreate" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-gray-900 dark:text-white">Create New Delegation</h3>
                <button @click="showCreate = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="formErrors.length > 0">
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-xs text-red-600 list-disc list-inside">
                        <template x-for="err in formErrors" :key="err"><li x-text="err"></li></template>
                    </ul>
                </div>
            </template>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Delegator (from)</label>
                        <select x-model="form.delegator_id" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                            <option value="">Select Manager</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Delegate To</label>
                        <select x-model="form.delegate_id" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                            <option value="">Select Employee</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Start Date</label>
                        <input type="date" x-model="form.start_date" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">End Date</label>
                        <input type="date" x-model="form.end_date" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Permissions</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300 cursor-pointer">
                            <input type="checkbox" value="approve_leave" x-model="form.permissions" class="rounded border-gray-300 text-blue-500 focus:ring-blue-400"> Approve Leave
                        </label>
                        <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300 cursor-pointer">
                            <input type="checkbox" value="view_reports" x-model="form.permissions" class="rounded border-gray-300 text-blue-500 focus:ring-blue-400"> View Reports
                        </label>
                        <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300 cursor-pointer">
                            <input type="checkbox" value="manage_team" x-model="form.permissions" class="rounded border-gray-300 text-blue-500 focus:ring-blue-400"> Manage Team
                        </label>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showCreate = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="button" @click="submitCreate()" :disabled="submitting"
                        class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium disabled:opacity-60">
                        <span x-text="submitting ? 'Creating...' : 'Create Delegation'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function delegationPage() {
    return {
        showCreate: false,
        submitting: false,
        formErrors: [],
        form: { delegator_id: '', delegate_id: '', start_date: '', end_date: '', permissions: [] },

        openCreate() {
            this.formErrors = [];
            this.form = { delegator_id: '', delegate_id: '', start_date: '', end_date: '', permissions: [] };
            this.showCreate = true;
        },

        async submitCreate() {
            this.submitting = true;
            this.formErrors = [];
            try {
                const res = await fetch(`<?php echo e(route('delegation.store')); ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.form)
                });
                const json = await res.json();
                if (res.ok) {
                    window.location.reload();
                } else if (res.status === 422 && json.errors) {
                    this.formErrors = Object.values(json.errors).flat();
                } else {
                    this.formErrors = [json.message || 'Something went wrong'];
                }
            } catch (e) {
                console.error('Create delegation failed', e);
            } finally {
                this.submitting = false;
            }
        },

        async revoke(id) {
            if (!confirm('Revoke this delegation?')) return;
            try {
                const res = await fetch(`/delegation/${id}/revoke`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                if (res.ok) {
                    window.location.reload();
                }
            } catch (e) {
                console.error('Revoke failed', e);
            }
        }
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\leavesphere\resources\views/delegation/index.blade.php ENDPATH**/ ?>