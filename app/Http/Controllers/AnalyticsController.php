<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $kpis = [
            'total_requests' => LeaveRequest::count(),
            'approval_rate' => $this->calculateApprovalRate(),
            'avg_days' => round(LeaveRequest::avg('total_days') ?? 0, 1),
            'total_days_used' => LeaveRequest::where('status', 'approved')->sum('total_days'),
        ];

        return view('analytics.index', compact('kpis'));
    }

    private function calculateApprovalRate(): int
    {
        $total = LeaveRequest::whereIn('status', ['approved', 'rejected'])->count();
        if ($total === 0) return 0;
        $approved = LeaveRequest::where('status', 'approved')->count();
        return round(($approved / $total) * 100);
    }

    /**
     * JSON endpoint: monthly trends (last 6 months) — total/approved/rejected counts.
     */
    public function monthlyTrends()
    {
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i));

        $labels = $months->map(fn($m) => $m->format('M'))->values();
        $total = [];
        $approved = [];
        $rejected = [];

        foreach ($months as $month) {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $total[] = LeaveRequest::whereBetween('created_at', [$start, $end])->count();
            $approved[] = LeaveRequest::whereBetween('created_at', [$start, $end])->where('status', 'approved')->count();
            $rejected[] = LeaveRequest::whereBetween('created_at', [$start, $end])->where('status', 'rejected')->count();
        }

        return response()->json([
            'labels' => $labels,
            'total' => $total,
            'approved' => $approved,
            'rejected' => $rejected,
        ]);
    }

    /**
     * JSON endpoint: leave request counts per department, broken down by leave type.
     */
    public function departmentComparison()
    {
        $departments = Department::orderBy('name')->get();
        $types = ['Annual Leave', 'Sick Leave', 'Personal Leave'];

        $labels = $departments->pluck('name');
        $datasets = [];

        foreach ($types as $type) {
            $datasets[$type] = $departments->map(function ($dept) use ($type) {
                return LeaveRequest::whereHas('user', fn($q) => $q->where('department_id', $dept->id))
                    ->where('leave_type', $type)
                    ->count();
            })->values();
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets,
        ]);
    }

    /**
     * JSON endpoint: distribution of leave requests by type.
     */
    public function leaveTypeDistribution()
    {
        $types = LeaveRequest::selectRaw('leave_type, count(*) as total')
            ->groupBy('leave_type')
            ->pluck('total', 'leave_type');

        return response()->json([
            'labels' => $types->keys(),
            'data' => $types->values(),
        ]);
    }

    public function export($format)
    {
        // In production: use barryvdh/laravel-dompdf for PDF, maatwebsite/excel for Excel
        if ($format === 'pdf') {
            return response('PDF export - integrate with barryvdh/laravel-dompdf package', 200)
                ->header('Content-Type', 'text/plain');
        }

        if ($format === 'excel') {
            return response('Excel export - integrate with maatwebsite/excel package', 200)
                ->header('Content-Type', 'text/plain');
        }

        return back()->with('error', 'Invalid export format.');
    }
}
