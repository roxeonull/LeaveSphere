@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="p-6 space-y-5" x-data="{ activeTab: 'general' }">

    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Settings</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage system preferences and configuration</p>
    </div>

    <div class="flex gap-6">
        <!-- Sidebar Tabs -->
        <div class="w-48 flex-shrink-0">
            <nav class="space-y-1">
                @foreach([
                    ['tab' => 'general', 'icon' => 'settings', 'label' => 'General'],
                    ['tab' => 'notifications', 'icon' => 'bell', 'label' => 'Notifications'],
                    ['tab' => 'leave-policy', 'icon' => 'file-text', 'label' => 'Leave Policy'],
                    ['tab' => 'sla', 'icon' => 'clock', 'label' => 'SLA Config'],
                    ['tab' => 'security', 'icon' => 'shield', 'label' => 'Security'],
                ] as $item)
                <button @click="activeTab = '{{ $item['tab'] }}'"
                    :class="activeTab === '{{ $item['tab'] }}' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 border-l-2 border-blue-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-left">
                    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4"></i>
                    {{ $item['label'] }}
                </button>
                @endforeach
            </nav>
        </div>

        <!-- Settings Content -->
        <div class="flex-1 space-y-5">

            <!-- General Settings -->
            <div x-show="activeTab === 'general'" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm border-b border-gray-100 dark:border-gray-700 pb-3">General Settings</h3>
                <form action="{{ route('settings.update') }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Company Name</label>
                            <input type="text" name="company_name" value="LeaveSphere Inc."
                                class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Company Email</label>
                            <input type="email" name="company_email" value="hr@company.com"
                                class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Timezone</label>
                            <select name="timezone" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                                <option>Asia/Jakarta (WIB)</option>
                                <option>Asia/Singapore (SGT)</option>
                                <option>UTC</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Date Format</label>
                            <select name="date_format" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                                <option>YYYY-MM-DD</option>
                                <option>DD/MM/YYYY</option>
                                <option>MM/DD/YYYY</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">System Logo URL</label>
                        <input type="text" name="logo_url" placeholder="https://..."
                            class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                        Save Changes
                    </button>
                </form>
            </div>

            <!-- Notifications -->
            <div x-show="activeTab === 'notifications'" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm border-b border-gray-100 dark:border-gray-700 pb-3">Notification Preferences</h3>
                <div class="space-y-4">
                    @foreach([
                        ['label' => 'New leave request submitted', 'desc' => 'Notify when an employee submits a new leave request', 'checked' => true],
                        ['label' => 'SLA breach warning', 'desc' => 'Alert when a request is approaching SLA deadline', 'checked' => true],
                        ['label' => 'Leave approved/rejected', 'desc' => 'Notify employees of approval decisions', 'checked' => true],
                        ['label' => 'Weekly summary report', 'desc' => 'Send weekly digest of leave activity', 'checked' => false],
                        ['label' => 'AI spike prediction alerts', 'desc' => 'Notify when AI detects upcoming leave spikes', 'checked' => true],
                    ] as $notif)
                    <div class="flex items-start justify-between py-3 border-b border-gray-50 dark:border-gray-700 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $notif['label'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $notif['desc'] }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-4 flex-shrink-0">
                            <input type="checkbox" {{ $notif['checked'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                        </label>
                    </div>
                    @endforeach
                </div>
                <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Save Preferences</button>
            </div>

            <!-- Leave Policy -->
            <div x-show="activeTab === 'leave-policy'" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm border-b border-gray-100 dark:border-gray-700 pb-3">Leave Policy Configuration</h3>
                <div class="grid grid-cols-2 gap-5">
                    @foreach([
                        ['label' => 'Annual Leave Days', 'name' => 'annual_leave', 'value' => 12],
                        ['label' => 'Sick Leave Days', 'name' => 'sick_leave', 'value' => 6],
                        ['label' => 'Personal Leave Days', 'name' => 'personal_leave', 'value' => 3],
                        ['label' => 'Maternity Leave Days', 'name' => 'maternity_leave', 'value' => 90],
                    ] as $policy)
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ $policy['label'] }}</label>
                        <input type="number" name="{{ $policy['name'] }}" value="{{ $policy['value'] }}"
                            class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    @endforeach
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Blackout Dates</label>
                    <input type="text" placeholder="Add blackout date ranges (e.g. 2026-12-24 to 2026-12-26)"
                        class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                </div>
                <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Save Policy</button>
            </div>

            <!-- SLA Config -->
            <div x-show="activeTab === 'sla'" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm border-b border-gray-100 dark:border-gray-700 pb-3">SLA Configuration</h3>
                <div class="grid grid-cols-2 gap-5">
                    @foreach([
                        ['label' => 'Standard SLA (hours)', 'name' => 'sla_standard', 'value' => 24],
                        ['label' => 'Urgent SLA (hours)', 'name' => 'sla_urgent', 'value' => 8],
                        ['label' => 'Warning threshold (%)', 'name' => 'sla_warning_pct', 'value' => 75],
                        ['label' => 'Auto-escalate after (hours)', 'name' => 'sla_escalate', 'value' => 20],
                    ] as $sla)
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ $sla['label'] }}</label>
                        <input type="number" name="{{ $sla['name'] }}" value="{{ $sla['value'] }}"
                            class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    @endforeach
                </div>
                <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Save SLA Config</button>
            </div>

            <!-- Security -->
            <div x-show="activeTab === 'security'" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm border-b border-gray-100 dark:border-gray-700 pb-3">Security Settings</h3>
                <form class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Current Password</label>
                        <input type="password" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">New Password</label>
                        <input type="password" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Confirm New Password</label>
                        <input type="password" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Two-Factor Authentication</p>
                            <p class="text-xs text-gray-400 mt-0.5">Add extra layer of security to your account</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500 dark:bg-gray-600"></div>
                        </label>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Update Security</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
