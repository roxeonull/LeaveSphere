<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * GET /api/home
     * Summary data for the employee home screen.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $stats = [
            'leave_balance' => $user->leave_balance,
            'pending_count' => $user->leaveRequests()->where('status', 'pending')->count(),
            'approved_count' => $user->leaveRequests()->where('status', 'approved')->count(),
            'rejected_count' => $user->leaveRequests()->where('status', 'rejected')->count(),
        ];

        $recentRequests = $user->leaveRequests()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($l) => [
                'id' => $l->id,
                'leave_type' => $l->leave_type,
                'start_date' => $l->start_date->format('Y-m-d'),
                'end_date' => $l->end_date->format('Y-m-d'),
                'total_days' => $l->total_days,
                'status' => $l->status,
            ]);

        // Upcoming approved leave (next one in the future)
        $upcoming = $user->leaveRequests()
            ->where('status', 'approved')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->first();

        return response()->json([
            'user' => [
                'name' => $user->name,
                'initials' => $user->initials,
                'position' => $user->position,
                'department' => $user->department->name ?? null,
            ],
            'stats' => $stats,
            'recent_requests' => $recentRequests,
            'upcoming_leave' => $upcoming ? [
                'leave_type' => $upcoming->leave_type,
                'start_date' => $upcoming->start_date->format('Y-m-d'),
                'end_date' => $upcoming->end_date->format('Y-m-d'),
                'total_days' => $upcoming->total_days,
            ] : null,
        ]);
    }
}
