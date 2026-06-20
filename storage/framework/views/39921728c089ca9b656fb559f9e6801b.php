<?php $__env->startSection('title', 'Workflows'); ?>
<?php $__env->startSection('page-title', 'Workflows'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 space-y-5" x-data="workflowsPage()">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Workflows</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Configure leave approval workflows per department</p>
        </div>
        <button @click="openCreate()"
            class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Create Workflow
        </button>
    </div>

    <!-- Workflow Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <?php $__empty_1 = true; $__currentLoopData = $workflows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $colors = ['bg-blue-500', 'bg-purple-500', 'bg-green-500', 'bg-orange-500', 'bg-pink-500', 'bg-teal-500'];
        ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="git-branch" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm"><?php echo e($wf->name); ?></h3>
                        <p class="text-xs text-gray-400"><?php echo e($wf->department->name ?? 'All Departments'); ?> · <?php echo e($wf->steps->count()); ?> approval levels</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="if(confirm('Delete this workflow?')) window.deleteWorkflow(<?php echo e($wf->id); ?>)"
                        class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Approval Flow Visualization -->
            <div class="px-5 py-4">
                <p class="text-xs text-gray-400 mb-3 uppercase tracking-wide">Approval Flow</p>
                <div class="flex items-center gap-2 flex-wrap">
                    <?php $__empty_2 = true; $__currentLoopData = $wf->steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                    <div class="flex items-center gap-2">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 <?php echo e($colors[$i % count($colors)]); ?> rounded-full flex items-center justify-center text-white text-xs font-bold">
                                L<?php echo e($level->level); ?>

                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 whitespace-nowrap"><?php echo e($level->approver_role); ?></span>
                        </div>
                        <?php if(!$loop->last): ?>
                        <div class="flex items-center mb-4">
                            <div class="w-6 h-0.5 bg-gray-200 dark:bg-gray-600"></div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                    <p class="text-xs text-gray-400">No approval steps configured.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-10 text-center text-sm text-gray-400">
            No workflows configured yet. Click "Create Workflow" to get started.
        </div>
        <?php endif; ?>
    </div>

    <!-- Create Workflow Modal -->
    <div x-show="showCreate" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-gray-900 dark:text-white">Create Workflow</h3>
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
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Workflow Name</label>
                    <input type="text" x-model="form.name" placeholder="e.g. Standard Approval"
                        class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Department</label>
                    <select x-model="form.department_id" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                        <option value="">All Departments</option>
                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($dept->id); ?>"><?php echo e($dept->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Approval Steps -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Approval Steps</label>
                    <div class="space-y-2">
                        <template x-for="(step, index) in form.steps" :key="index">
                            <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0" x-text="index + 1"></span>
                                <input type="text" x-model="form.steps[index]"
                                    class="flex-1 bg-transparent text-sm text-gray-700 dark:text-gray-300 border-none focus:outline-none">
                                <button type="button" @click="form.steps.splice(index, 1)" class="text-gray-400 hover:text-red-500 transition-colors">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="form.steps.push('')"
                        class="mt-2 flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-700 transition-colors">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Step
                    </button>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showCreate = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm">
                        Cancel
                    </button>
                    <button type="button" @click="submitCreate()" :disabled="submitting"
                        class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium disabled:opacity-60">
                        <span x-text="submitting ? 'Creating...' : 'Create Workflow'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function workflowsPage() {
    return {
        showCreate: false,
        submitting: false,
        formErrors: [],
        form: { name: '', department_id: '', steps: ['Supervisor', 'Manager', 'HR'] },

        openCreate() {
            this.formErrors = [];
            this.form = { name: '', department_id: '', steps: ['Supervisor', 'Manager', 'HR'] };
            this.showCreate = true;
        },

        async submitCreate() {
            this.submitting = true;
            this.formErrors = [];

            const steps = this.form.steps.filter(s => s.trim() !== '');
            if (steps.length === 0) {
                this.formErrors = ['At least one approval step is required.'];
                this.submitting = false;
                return;
            }

            try {
                const res = await fetch(`<?php echo e(route('workflows.store')); ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name: this.form.name, department_id: this.form.department_id, steps })
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
                console.error('Create workflow failed', e);
            } finally {
                this.submitting = false;
            }
        }
    }
}

window.deleteWorkflow = async function(id) {
    try {
        const res = await fetch(`/workflows/${id}`, {
            method: 'DELETE',
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
        console.error('Delete workflow failed', e);
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\leavesphere\resources\views/workflows/index.blade.php ENDPATH**/ ?>