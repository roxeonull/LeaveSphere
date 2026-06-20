<?php $__env->startSection('title', 'Users'); ?>
<?php $__env->startSection('page-title', 'Users'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 space-y-5" x-data="usersPage()" x-init="init()">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Users</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage all system users</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Export -->
            <div class="relative" x-data="{ openExport: false }">
                <button @click="openExport = !openExport"
                    class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                    <i data-lucide="download" class="w-4 h-4"></i> Export
                </button>
                <div x-show="openExport" @click.away="openExport = false"
                    class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 py-2">
                    <a href="<?php echo e(route('users.export', 'excel')); ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <i data-lucide="table" class="w-4 h-4 text-green-600"></i> Excel
                    </a>
                    <a href="<?php echo e(route('users.export', 'pdf')); ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <i data-lucide="file-text" class="w-4 h-4 text-red-500"></i> PDF
                    </a>
                </div>
            </div>
            <!-- Create User -->
            <button @click="openCreate()"
                class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Create User
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?php echo e($stats['total']); ?></p>
                </div>
                <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Active Users</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?php echo e($stats['active']); ?></p>
                </div>
                <div class="w-9 h-9 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-check" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Managers</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?php echo e($stats['managers']); ?></p>
                </div>
                <div class="w-9 h-9 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="briefcase" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Employees</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1"><?php echo e($stats['employees']); ?></p>
                </div>
                <div class="w-9 h-9 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" x-model="search" @input.debounce.400ms="fetchData(1)" placeholder="Search by name, email, ID..."
                    class="w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 placeholder-gray-400 text-sm rounded-lg px-4 py-2.5 pl-9 border border-gray-200 dark:border-gray-600 focus:outline-none focus:border-blue-400">
                <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                <div x-show="loading" class="absolute right-3 top-2.5">
                    <svg class="animate-spin w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
            </div>
            <select x-model="filterRole" @change="fetchData(1)" class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg px-3 py-2.5 border border-gray-200 dark:border-gray-600 focus:outline-none min-w-32">
                <option value="">All Roles</option>
                <option value="super_admin">Super Admin</option>
                <option value="manager">Manager</option>
                <option value="employee">Employee</option>
            </select>
            <select x-model="filterDept" @change="fetchData(1)" class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg px-3 py-2.5 border border-gray-200 dark:border-gray-600 focus:outline-none min-w-40">
                <option value="">All Departments</option>
                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($dept->id); ?>"><?php echo e($dept->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select x-model="filterStatus" @change="fetchData(1)" class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg px-3 py-2.5 border border-gray-200 dark:border-gray-600 focus:outline-none min-w-32">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Employee ID</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Full Name</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Department</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Position</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-if="!loading && items.length === 0">
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-sm text-gray-400">No users found matching your filters.</td>
                        </tr>
                    </template>

                    <template x-for="user in items" :key="user.db_id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-4 text-xs font-mono text-gray-500 dark:text-gray-400" x-text="user.employee_id"></td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" x-text="user.initials"></div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="user.name"></span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="user.email"></td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="user.dept"></td>
                            <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400" x-text="user.position || '-'"></td>
                            <td class="px-4 py-4">
                                <span class="text-xs px-2 py-0.5 rounded-full"
                                    :class="user.role === 'super_admin' ? 'bg-red-100 text-red-700' : (user.role === 'manager' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600')"
                                    x-text="user.role.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase())"></span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full"
                                    :class="user.status === 'active' ? 'badge-approved' : 'badge-expired'">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="user.status === 'active' ? 'bg-green-500' : 'bg-gray-400'"></span>
                                    <span x-text="user.status.charAt(0).toUpperCase() + user.status.slice(1)"></span>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1">
                                    <button @click="openEdit(user)" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Edit">
                                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button @click="resetPassword(user)" class="p-1.5 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors" title="Reset Password">
                                        <i data-lucide="key" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button @click="confirmDelete(user)" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Delete">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100 dark:border-gray-700" x-show="total > 0">
            <p class="text-xs text-gray-500" x-text="'Showing ' + from + '–' + to + ' of ' + total + ' users'"></p>
            <div class="flex items-center gap-1">
                <button @click="fetchData(currentPage - 1)" :disabled="currentPage <= 1"
                    class="px-3 py-1.5 text-xs text-gray-500 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">Previous</button>
                <span class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded-lg" x-text="currentPage"></span>
                <button @click="fetchData(currentPage + 1)" :disabled="currentPage >= lastPage"
                    class="px-3 py-1.5 text-xs text-gray-500 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
            </div>
        </div>
    </div>

    <!-- CREATE/EDIT USER MODAL -->
    <div x-show="showForm" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-xl mx-4 p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="isEditing ? 'Edit User' : 'Create New User'"></h3>
                <button @click="showForm = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-4">
                <template x-if="formErrors.length > 0">
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                        <ul class="text-xs text-red-600 list-disc list-inside">
                            <template x-for="err in formErrors" :key="err">
                                <li x-text="err"></li>
                            </template>
                        </ul>
                    </div>
                </template>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Employee ID</label>
                        <input type="text" x-model="form.employee_id" :disabled="isEditing" placeholder="EMP001"
                            class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400 disabled:opacity-60">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
                        <input type="text" x-model="form.name" placeholder="John Doe"
                            class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                    <input type="email" x-model="form.email" placeholder="john@company.com"
                        class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Department</label>
                        <select x-model="form.department_id" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                            <option value="">Select Department</option>
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($dept->id); ?>"><?php echo e($dept->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Position</label>
                        <input type="text" x-model="form.position" placeholder="Software Engineer"
                            class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Role</label>
                        <select x-model="form.role" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                            <option value="employee">Employee</option>
                            <option value="manager">Manager</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Leave Balance (days)</label>
                        <input type="number" x-model="form.leave_balance"
                            class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                </div>
                <template x-if="isEditing">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                        <select x-model="form.status" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </template>
                <template x-if="!isEditing">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                        <input type="password" x-model="form.password" placeholder="Minimum 8 characters"
                            class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                </template>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showForm = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm">
                        Cancel
                    </button>
                    <button type="button" @click="submitForm()" :disabled="submitting"
                        class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium disabled:opacity-60">
                        <span x-text="submitting ? 'Saving...' : (isEditing ? 'Update User' : 'Create User')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRM MODAL -->
    <div x-show="showDeleteConfirm" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">Delete User?</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                Are you sure you want to delete <span class="font-medium" x-text="deleteTarget?.name"></span>? This action cannot be undone.
            </p>
            <div class="flex gap-3">
                <button @click="showDeleteConfirm = false" class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm">Cancel</button>
                <button @click="deleteUser()" :disabled="submitting" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium disabled:opacity-60">
                    <span x-text="submitting ? 'Deleting...' : 'Delete'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST -->
    <div x-show="toast.show" x-transition class="fixed bottom-6 right-6 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
        :class="toast.success ? 'bg-green-600 text-white' : 'bg-red-600 text-white'" x-cloak>
        <span x-text="toast.message"></span>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function usersPage() {
    return {
        search: '',
        filterRole: '',
        filterDept: '',
        filterStatus: '',
        items: [],
        loading: false,
        submitting: false,
        currentPage: 1,
        lastPage: 1,
        total: 0,
        from: 0,
        to: 0,

        showForm: false,
        isEditing: false,
        editingId: null,
        form: { employee_id: '', name: '', email: '', department_id: '', position: '', role: 'employee', leave_balance: 12, status: 'active', password: '' },
        formErrors: [],

        showDeleteConfirm: false,
        deleteTarget: null,

        toast: { show: false, success: true, message: '' },

        init() {
            this.fetchData(1);
        },

        showToast(message, success = true) {
            this.toast = { show: true, success, message };
            setTimeout(() => { this.toast.show = false }, 3000);
        },

        async fetchData(page = 1) {
            this.loading = true;
            const params = new URLSearchParams({
                page: page,
                search: this.search,
                role: this.filterRole,
                department: this.filterDept,
                status: this.filterStatus,
            });
            try {
                const res = await fetch(`<?php echo e(route('users.data')); ?>?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                this.items = json.data;
                this.currentPage = json.current_page;
                this.lastPage = json.last_page;
                this.total = json.total;
                this.from = json.from || 0;
                this.to = json.to || 0;
            } catch (e) {
                console.error('Failed to fetch users', e);
            } finally {
                this.loading = false;
                this.$nextTick(() => lucide.createIcons());
            }
        },

        openCreate() {
            this.isEditing = false;
            this.editingId = null;
            this.formErrors = [];
            this.form = { employee_id: '', name: '', email: '', department_id: '', position: '', role: 'employee', leave_balance: 12, status: 'active', password: '' };
            this.showForm = true;
        },

        openEdit(user) {
            this.isEditing = true;
            this.editingId = user.db_id;
            this.formErrors = [];
            this.form = {
                employee_id: user.employee_id,
                name: user.name,
                email: user.email,
                department_id: user.department_id || '',
                position: user.position || '',
                role: user.role,
                leave_balance: user.leave_balance,
                status: user.status,
                password: '',
            };
            this.showForm = true;
        },

        async submitForm() {
            this.submitting = true;
            this.formErrors = [];

            const url = this.isEditing ? `/users/${this.editingId}` : `<?php echo e(route('users.store')); ?>`;
            const method = this.isEditing ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method: method,
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
                    this.showForm = false;
                    this.showToast(json.message || 'Success');
                    await this.fetchData(this.currentPage);
                } else if (res.status === 422 && json.errors) {
                    this.formErrors = Object.values(json.errors).flat();
                } else {
                    this.showToast(json.message || 'Something went wrong', false);
                }
            } catch (e) {
                console.error('Submit failed', e);
                this.showToast('Network error', false);
            } finally {
                this.submitting = false;
            }
        },

        confirmDelete(user) {
            this.deleteTarget = user;
            this.showDeleteConfirm = true;
        },

        async deleteUser() {
            if (!this.deleteTarget) return;
            this.submitting = true;
            try {
                const res = await fetch(`/users/${this.deleteTarget.db_id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                if (res.ok) {
                    this.showDeleteConfirm = false;
                    this.showToast('User deleted successfully.');
                    await this.fetchData(this.currentPage);
                }
            } catch (e) {
                console.error('Delete failed', e);
                this.showToast('Failed to delete user', false);
            } finally {
                this.submitting = false;
            }
        },

        async resetPassword(user) {
            if (!confirm(`Reset password for ${user.name}?`)) return;
            try {
                const res = await fetch(`/users/${user.db_id}/reset-password`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                const json = await res.json();
                if (res.ok) {
                    alert(json.message);
                }
            } catch (e) {
                console.error('Reset password failed', e);
            }
        }
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\leavesphere\resources\views/users/index.blade.php ENDPATH**/ ?>