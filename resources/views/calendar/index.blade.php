@extends('layouts.app')

@section('title', 'Team Calendar')
@section('page-title', 'Team Calendar')

@section('content')
<div class="p-6 space-y-5" x-data="calendarPage()" x-init="init()">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Team Calendar</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">View team leave schedules and conflicts</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 flex flex-wrap items-center gap-3">
        <!-- Legend -->
        <div class="flex items-center gap-4 flex-1 flex-wrap">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Legend:</span>
            <span class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-300">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span> Annual Leave
            </span>
            <span class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-300">
                <span class="w-3 h-3 rounded-full bg-red-500"></span> Sick Leave
            </span>
            <span class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-300">
                <span class="w-3 h-3 rounded-full bg-yellow-400"></span> Personal Leave
            </span>
        </div>

        <!-- Filters -->
        <select x-model="filterDept" @change="reloadEvents()"
            class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-lg px-3 py-2 border border-gray-200 dark:border-gray-600 focus:outline-none">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
        <select x-model="filterType" @change="reloadEvents()"
            class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-lg px-3 py-2 border border-gray-200 dark:border-gray-600 focus:outline-none">
            <option value="">All Leave Types</option>
            <option value="Annual Leave">Annual Leave</option>
            <option value="Sick Leave">Sick Leave</option>
            <option value="Personal Leave">Personal Leave</option>
        </select>
    </div>

    <!-- Calendar Container -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <div id="calendar"></div>
    </div>
</div>

<style>
    /* FullCalendar Dark Mode Fix */
    .dark .fc { color: #e5e7eb; }
    .dark .fc-theme-standard td,
    .dark .fc-theme-standard th,
    .dark .fc-theme-standard .fc-scrollgrid { border-color: #374151; }
    .dark .fc-col-header-cell { background: #1f2937; }
    .dark .fc-daygrid-day { background: #1f2937; }
    .dark .fc-daygrid-day:hover { background: #374151; }
    .dark .fc-toolbar-title { color: #f9fafb; }
    .fc-event { border-radius: 4px !important; border: none !important; font-size: 11px !important; }
    .fc-toolbar-title { font-size: 1rem !important; font-weight: 600 !important; }
    .fc-button { font-size: 12px !important; padding: 4px 12px !important; border-radius: 8px !important; }
    .fc-button-primary { background: #3b82f6 !important; border-color: #3b82f6 !important; }
    .fc-button-primary:hover { background: #2563eb !important; }
    .fc-button-active { background: #1d4ed8 !important; }
</style>
@endsection

@push('scripts')
<script>
function calendarPage() {
    return {
        filterDept: '',
        filterType: '',
        calendar: null,

        init() {
            const calendarEl = document.getElementById('calendar');
            this.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 680,
                events: this.buildEventsUrl(),
                eventClick: function(info) {
                    alert('Employee: ' + info.event.title + '\nDate: ' + info.event.startStr + ' – ' + info.event.endStr);
                }
            });
            this.calendar.render();
        },

        buildEventsUrl() {
            const params = new URLSearchParams({
                department: this.filterDept,
                leave_type: this.filterType,
            });
            return `{{ route('calendar.events') }}?${params.toString()}`;
        },

        reloadEvents() {
            this.calendar.removeAllEventSources();
            this.calendar.addEventSource(this.buildEventsUrl());
        }
    }
}
</script>
@endpush
