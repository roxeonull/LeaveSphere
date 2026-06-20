<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false, sidebarCollapsed: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>LeaveSphere - <?php echo $__env->yieldContent('title', 'HR Analytics'); ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        },
                        sidebar: '#0f172a',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link { transition: all 0.15s ease; }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); }
        .sidebar-link.active { background: rgba(59,130,246,0.15); border-left: 3px solid #3b82f6; }
        .stat-card { transition: all 0.2s ease; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-scheduled { background: #dbeafe; color: #1e40af; }
        .badge-expired { background: #f3f4f6; color: #6b7280; }
        .sla-safe { color: #059669; }
        .sla-warning { color: #d97706; }
        .sla-breached { color: #dc2626; }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 3px; }
        .modal-backdrop { background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased" x-cloak>
<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside
        class="flex flex-col h-full bg-[#0f172a] text-white transition-all duration-300 ease-in-out flex-shrink-0"
        :class="sidebarCollapsed ? 'w-16' : 'w-60'"
    >
        <!-- Logo -->
        <div class="flex items-center gap-3 px-4 py-5 border-b border-white/10 h-16">
            <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">LS</div>
            <span class="font-semibold text-white text-base truncate" x-show="!sidebarCollapsed">LeaveSphere</span>
        </div>

        <!-- Search -->
        <div class="px-3 py-3" x-show="!sidebarCollapsed">
            <div class="relative">
                <input type="text" placeholder="Search employees, requests..."
                    class="w-full bg-white/10 text-white placeholder-gray-400 text-xs rounded-lg px-3 py-2 pl-8 border border-white/10 focus:outline-none focus:border-blue-400">
                <i data-lucide="search" class="absolute left-2.5 top-2.5 w-3.5 h-3.5 text-gray-400"></i>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-2 py-2 space-y-0.5 overflow-y-auto">
            <?php
                $navItems = [
                    ['route' => 'dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
                    ['route' => 'approvals.index', 'icon' => 'check-square', 'label' => 'Approvals'],
                    ['route' => 'recommendations.index', 'icon' => 'lightbulb', 'label' => 'Recommendations'],
                    ['route' => 'calendar.index', 'icon' => 'calendar', 'label' => 'Calendar'],
                    ['route' => 'delegation.index', 'icon' => 'users', 'label' => 'Delegation'],
                    ['route' => 'sla.index', 'icon' => 'clock', 'label' => 'SLA Monitor'],
                    ['route' => 'analytics.index', 'icon' => 'bar-chart-2', 'label' => 'Analytics'],
                    ['route' => 'workflows.index', 'icon' => 'git-branch', 'label' => 'Workflows'],
                    ['route' => 'users.index', 'icon' => 'user-cog', 'label' => 'Users'],
                ];
            ?>

            <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route($item['route'])); ?>"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 text-sm <?php echo e(request()->routeIs($item['route']) ? 'active text-white' : ''); ?>">
                    <i data-lucide="<?php echo e($item['icon']); ?>" class="w-4 h-4 flex-shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="truncate"><?php echo e($item['label']); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>

        <!-- Bottom -->
        <div class="border-t border-white/10 p-2 space-y-0.5">
            <a href="<?php echo e(route('settings')); ?>"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 text-sm">
                <i data-lucide="settings" class="w-4 h-4 flex-shrink-0"></i>
                <span x-show="!sidebarCollapsed">Settings</span>
            </a>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit"
                    class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 text-sm hover:text-red-400">
                    <i data-lucide="log-out" class="w-4 h-4 flex-shrink-0"></i>
                    <span x-show="!sidebarCollapsed">Logout</span>
                </button>
            </form>

            <!-- Collapse Button -->
            <button @click="sidebarCollapsed = !sidebarCollapsed"
                class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-400 text-xs mt-2">
                <i data-lucide="chevrons-left" class="w-4 h-4 flex-shrink-0 transition-transform"
                   :class="sidebarCollapsed ? 'rotate-180' : ''"></i>
                <span x-show="!sidebarCollapsed">Collapse</span>
            </button>

            <!-- User Profile -->
            <div class="flex items-center gap-3 px-3 py-3 mt-1" x-show="!sidebarCollapsed">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                    <?php echo e(strtoupper(substr(auth()->user()->name ?? 'JA', 0, 2))); ?>

                </div>
                <div class="min-w-0">
                    <p class="text-white text-xs font-medium truncate"><?php echo e(auth()->user()->name ?? 'John Admin'); ?></p>
                    <p class="text-gray-400 text-xs truncate"><?php echo e(auth()->user()->role ?? 'Super Admin'); ?></p>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- TOP HEADER -->
        <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-6 flex-shrink-0 shadow-sm">
            <div class="flex items-center gap-4">
                <h1 class="text-gray-800 dark:text-white font-semibold text-base"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
            </div>
            <div class="flex items-center gap-3">
                <!-- Search -->
                <div class="relative hidden md:block">
                    <input type="text" placeholder="Search employees, requests..."
                        class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 placeholder-gray-400 text-sm rounded-lg px-4 py-2 pl-9 w-64 border border-gray-200 dark:border-gray-600 focus:outline-none focus:border-blue-400">
                    <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                </div>

                <!-- Refresh -->
                <button class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>

                <!-- Dark mode toggle -->
                <button @click="darkMode = !darkMode"
                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i data-lucide="moon" class="w-4 h-4" x-show="!darkMode"></i>
                    <i data-lucide="sun" class="w-4 h-4" x-show="darkMode"></i>
                </button>

                <!-- Notification -->
                <div class="relative" x-data="notificationDropdown()" x-init="init()">
                    <button @click="toggle()"
                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 relative">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                        <span x-show="unreadCount > 0" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 p-4" x-cloak>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Notifications</h3>
                            <button @click="markAllRead()" x-show="unreadCount > 0" class="text-xs text-blue-600 hover:text-blue-700">Mark all read</button>
                        </div>
                        <div class="space-y-1 max-h-80 overflow-y-auto">
                            <template x-if="notifications.length === 0">
                                <p class="text-xs text-gray-400 text-center py-6">No notifications yet.</p>
                            </template>
                            <template x-for="notif in notifications" :key="notif.id">
                                <div @click="markRead(notif)" class="flex items-start gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer"
                                    :class="!notif.is_read ? 'bg-blue-50/50 dark:bg-blue-900/10' : ''">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="bell" class="w-4 h-4 text-blue-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-800 dark:text-gray-200" x-text="notif.title"></p>
                                        <p class="text-xs text-gray-400 mt-0.5" x-text="notif.message"></p>
                                        <p class="text-xs text-gray-400 mt-0.5" x-text="notif.time"></p>
                                    </div>
                                    <span x-show="!notif.is_read" class="w-1.5 h-1.5 rounded-full bg-blue-500 flex-shrink-0 mt-1.5"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Profile -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <div class="w-7 h-7 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            <?php echo e(strtoupper(substr(auth()->user()->name ?? 'JA', 0, 2))); ?>

                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-300 hidden md:block"><?php echo e(auth()->user()->name ?? 'John Admin'); ?></span>
                        <i data-lucide="chevron-down" class="w-3 h-3 text-gray-400"></i>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 py-2">
                        <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i data-lucide="user" class="w-4 h-4"></i> Profile
                        </a>
                        <a href="<?php echo e(route('settings')); ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i data-lucide="settings" class="w-4 h-4"></i> Settings
                        </a>
                        <hr class="my-1 border-gray-200 dark:border-gray-600">
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="flex items-center gap-2 px-4 py-2 text-sm text-red-500 hover:bg-gray-50 dark:hover:bg-gray-700 w-full text-left">
                                <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
            <?php if(session('success')): ?>
                <div class="mx-6 mt-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="mx-6 mt-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</div>

<script>
    // Init Lucide icons
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
    document.addEventListener('alpine:init', function() {
        setTimeout(() => lucide.createIcons(), 50);
    });

    function notificationDropdown() {
        return {
            open: false,
            notifications: [],
            unreadCount: 0,

            init() {
                this.fetchNotifications();
            },

            toggle() {
                this.open = !this.open;
                if (this.open) this.fetchNotifications();
            },

            async fetchNotifications() {
                try {
                    const res = await fetch('<?php echo e(route("notifications.index")); ?>', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const json = await res.json();
                    this.notifications = json.notifications;
                    this.unreadCount = json.unread_count;
                } catch (e) {
                    console.error('Failed to fetch notifications', e);
                } finally {
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            async markRead(notif) {
                if (notif.is_read) return;
                try {
                    await fetch(`/notifications/${notif.id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                    notif.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                } catch (e) {
                    console.error('Mark read failed', e);
                }
            },

            async markAllRead() {
                try {
                    await fetch('<?php echo e(route("notifications.read-all")); ?>', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                    this.notifications.forEach(n => n.is_read = true);
                    this.unreadCount = 0;
                } catch (e) {
                    console.error('Mark all read failed', e);
                }
            }
        }
    }
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\leavesphere\resources\views/layouts/app.blade.php ENDPATH**/ ?>