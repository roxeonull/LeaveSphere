@extends('layouts.app')

@section('title', 'Leave Approvals')
@section('page-title', 'Leave Approvals')

@section('content')
<div class="p-6 space-y-5" x-data="approvalsPage()" x-init="init()">

    <!-- Page Header -->
    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Leave Approvals</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Review and manage leave requests</p>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
        <div class="flex flex-col md:flex-row gap-3 items-start md:items-center">
            <!-- Search -->
            <div class="relative flex-1">
                <input type="text" x-model="search" @input.debounce.400ms="fetchData(1)" placeholder="Search employees or departments..."
                    class="w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 placeholder-gray-400 text-sm rounded-lg px-4 py-2.5 pl-9 border border-gray-200 dark:border-gray-600 focus:outline-none focus:border-blue-400">
                <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                <div x-show="loading" class="absolute right-3 top-2.5">
                    <svg class="animate-spin w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
            </div>

            <!-- Status Filter -->
            <select x-model="filterStatus" @change="fetchData(1)"
                class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg px-3 py-2.5 border border-gray-200 dark:border-gray-600 focus:outline-none focus:border-blue-400 min-w-32">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>

            <!-- Department Filter -->
            <select x-model="filterDept" @change="fetchData(1)"
                class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg px-3 py-2.5 border border-gray-200 dark:border-gray-600 focus:outline-none focus:border-blue-400 min-w-36">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>

            <!-- Leave Type Filter -->
            <select x-model="filterType" @change="fetchData(1)"
                class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg px-3 py-2.5 border border-gray-200 dark:border-gray-600 focus:outline-none focus:border-blue-400 min-w-36">
                <option value="">All Leave Types</option>
                <option value="Annual Leave">Annual Leave</option>
                <option value="Sick Leave">Sick Leave</option>
                <option value="Personal Leave">Personal Leave</option>
            </select>
        </div>

        <!-- Status Summary Tabs -->
        <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
            <button @click="filterStatus = ''; fetchData(1)" :class="filterStatus === '' ? 'text-blue-600 font-semibold' : 'text-gray-400'"
                class="text-xs hover:text-blue-500 transition-colors">All Requests</button>
            <button @click="filterStatus = 'pending'; fetchData(1)" :class="filterStatus === 'pending' ? 'text-amber-600 font-semibold' : 'text-gray-400'"
                class="text-xs hover:text-amber-500 transition-colors">
                Pending: <span class="font-bold">{{ $statusCounts['pending'] }}</span>
            </button>
            <button @click="filterStatus = 'approved'; fetchData(1)" :class="filterStatus === 'approved' ? 'text-green-600 font-semibold' : 'text-gray-400'"
                class="text-xs hover:text-green-500 transition-colors">
                Approved: <span class="font-bold">{{ $statusCounts['approved'] }}</span>
            </button>
            <button @click="filterStatus = 'rejected'; fetchData(1)" :class="filterStatus === 'rejected' ? 'text-red-600 font-semibold' : 'text-gray-400'"
                class="text-xs hover:text-red-500 transition-colors">
                Rejected: <span class="font-bold">{{ $statusCounts['rejected'] }}</span>
            </button>
        </div>
    </div>

    <!-- Approvals Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Employee</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Department</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Leave Type</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Start Date</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">End Date</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Duration</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-if="!loading && items.length === 0">
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-sm text-gray-400">
                                No leave requests found matching your filters.
                            </td>
                        </tr>
                    </template>

                    <template x-for="req in items" :key="req.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                        x-text="req.initials"></div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="req.name"></span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="req.dept"></td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full"
                                    :class="req.type === 'Annual Leave' ? 'bg-blue-100 text-blue-700' : (req.type === 'Sick Leave' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')">
                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                    <span x-text="req.type"></span>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="req.start"></td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="req.end"></td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="req.days + ' day(s)'"></td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full"
                                    :class="req.status === 'pending' ? 'badge-pending' : (req.status === 'approved' ? 'badge-approved' : 'badge-rejected')"
                                    x-text="req.status.charAt(0).toUpperCase() + req.status.slice(1)"></span>
                                <p class="text-xs text-red-500 mt-1" x-show="req.reason" x-text="'Rejection Reason: ' + req.reason"></p>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <template x-if="req.status === 'pending'">
                                        <div class="flex items-center gap-2">
                                            <button @click="openApprove(req.id, req.name)"
                                                class="flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg font-medium transition-colors">
                                                <i data-lucide="check" class="w-3 h-3"></i> Approve
                                            </button>
                                            <button @click="openReject(req.id, req.name)"
                                                class="flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg font-medium transition-colors">
                                                <i data-lucide="x" class="w-3 h-3"></i> Reject
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100 dark:border-gray-700" x-show="total > 0">
            <p class="text-xs text-gray-500" x-text="'Showing ' + from + '–' + to + ' of ' + total + ' requests'"></p>
            <div class="flex items-center gap-1">
                <button @click="fetchData(currentPage - 1)" :disabled="currentPage <= 1"
                    class="px-3 py-1.5 text-xs text-gray-500 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">Previous</button>
                <span class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded-lg" x-text="currentPage"></span>
                <button @click="fetchData(currentPage + 1)" :disabled="currentPage >= lastPage"
                    class="px-3 py-1.5 text-xs text-gray-500 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
            </div>
        </div>
    </div>

    <!-- APPROVE MODAL -->
    <div x-show="showApprove" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Approve Leave Request</h3>
                    <p class="text-xs text-gray-400" x-text="'For: ' + selectedEmployee"></p>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                <textarea x-model="approveNotes" rows="3"
                    class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:border-blue-400 resize-none"
                    placeholder="Add optional notes for the employee..."></textarea>
            </div>
            <div class="flex gap-3">
                <button @click="showApprove = false"
                    class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button @click="submitApprove()" :disabled="submitting"
                    class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-60">
                    <span x-text="submitting ? 'Processing...' : 'Confirm Approve'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- REJECT MODAL -->
    <div x-show="showReject" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Reject Leave Request</h3>
                    <p class="text-xs text-gray-400" x-text="'For: ' + selectedEmployee"></p>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Rejection Reason <span class="text-red-500">*</span></label>
                <textarea x-model="rejectReason" rows="3"
                    class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:border-red-400 resize-none"
                    placeholder="Provide a reason for rejection..."></textarea>
            </div>
            <div class="flex gap-3">
                <button @click="showReject = false"
                    class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button @click="submitReject()" :disabled="submitting || !rejectReason"
                    class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-60">
                    <span x-text="submitting ? 'Processing...' : 'Confirm Reject'"></span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function approvalsPage() {
    return {
        search: '',
        filterStatus: '',
        filterDept: '',
        filterType: '',
        items: [],
        loading: false,
        submitting: false,
        currentPage: 1,
        lastPage: 1,
        total: 0,
        from: 0,
        to: 0,
        showApprove: false,
        showReject: false,
        selectedId: null,
        selectedEmployee: '',
        approveNotes: '',
        rejectReason: '',

        init() {
            this.fetchData(1);
        },

        async fetchData(page = 1) {
            this.loading = true;
            const params = new URLSearchParams({
                page: page,
                search: this.search,
                status: this.filterStatus,
                department: this.filterDept,
                leave_type: this.filterType,
            });

            try {
                const res = await fetch(`{{ route('approvals.data') }}?${params.toString()}`, {
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
                console.error('Failed to fetch approvals', e);
            } finally {
                this.loading = false;
                this.$nextTick(() => lucide.createIcons());
            }
        },

        openApprove(id, name) {
            this.selectedId = id;
            this.selectedEmployee = name;
            this.approveNotes = '';
            this.showApprove = true;
        },
        openReject(id, name) {
            this.selectedId = id;
            this.selectedEmployee = name;
            this.rejectReason = '';
            this.showReject = true;
        },

        async submitApprove() {
            this.submitting = true;
            try {
                const res = await fetch(`/approvals/${this.selectedId}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ notes: this.approveNotes })
                });
                if (res.ok) {
                    this.showApprove = false;
                    await this.fetchData(this.currentPage);
                }
            } catch (e) {
                console.error('Approve failed', e);
            } finally {
                this.submitting = false;
            }
        },

        async submitReject() {
            if (!this.rejectReason) return;
            this.submitting = true;
            try {
                const res = await fetch(`/approvals/${this.selectedId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ reason: this.rejectReason })
                });
                if (res.ok) {
                    this.showReject = false;
                    await this.fetchData(this.currentPage);
                }
            } catch (e) {
                console.error('Reject failed', e);
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>
@endpush
