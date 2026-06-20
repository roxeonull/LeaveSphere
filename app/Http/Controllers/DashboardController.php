<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\SlaRecord;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending_approvals' => LeaveRequest::where('status', 'pending')->count(),
            'sla_alerts' => SlaRecord::where('breached', false)
                ->where('deadline', '<=', now()->addHours(12))
                ->count(),
            'total_employees' => User::where('status', 'active')->count(),
        ];

        // Predicted spike % = compare next 7 days scheduled leave vs previous 7 days
        $nextWeek = LeaveRequest::whereBetween('start_date', [now(), now()->addDays(7)])->count();
        $prevWeek = LeaveRequest::whereBetween('start_date', [now()->subDays(7), now()])->count();
        $stats['spike_percentage'] = $prevWeek > 0
            ? round((($nextWeek - $prevWeek) / $prevWeek) * 100)
            : ($nextWeek > 0 ? 100 : 0);

        $pendingRequests = LeaveRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($req) {
                return [
                    'initials' => $req->user->initials ?? 'NA',
                    'name' => $req->user->name ?? 'Unknown',
                    'type' => $req->leave_type,
                    'days' => $req->total_days . ' day(s)',
                    'date' => $req->start_date->format('Y-m-d'),
                    'color' => 'bg-blue-500',
                ];
            });

        $slaAlerts = SlaRecord::with('leaveRequest.user')
            ->where('breached', false)
            ->orderBy('deadline')
            ->take(3)
            ->get()
            ->map(function ($sla) {
                $hoursLeft = (int) round(now()->diffInHours($sla->deadline, false));
                return [
                    'initials' => $sla->leaveRequest->user->initials ?? 'NA',
                    'name' => $sla->leaveRequest->user->name ?? 'Unknown',
                    'type' => $sla->leaveRequest->leave_type ?? '',
                    'time' => $hoursLeft > 0 ? $hoursLeft . 'h left' : abs($hoursLeft) . 'h overdue',
                    'level' => $hoursLeft < 4 ? 'danger' : ($hoursLeft < 12 ? 'warning' : 'safe'),
                ];
            });

        return view('dashboard.index', compact('stats', 'pendingRequests', 'slaAlerts'));
    }

    /**
     * JSON endpoint: leave volume for the next 7 days (for spike prediction chart).
     */
    public function spikesData()
    {
        $labels = [];
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $date = now()->addDays($i);
            $labels[] = $date->format('D');
            $data[] = LeaveRequest::whereDate('start_date', $date->format('Y-m-d'))->count();
        }

        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    /**
     * JSON endpoint: current employees on leave today, grouped by department.
     */
    public function departmentLoadData()
    {
        $departments = Department::orderBy('name')->get();

        $labels = $departments->pluck('name');
        $data = $departments->map(function ($dept) {
            return LeaveRequest::whereHas('user', fn($q) => $q->where('department_id', $dept->id))
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->count();
        });

        return response()->json(['labels' => $labels, 'data' => $data]);
    }
}
