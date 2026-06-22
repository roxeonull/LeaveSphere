<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * GET /api/calendar
     * Approved leave events for the employee's own department (team calendar).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = LeaveRequest::with('user')
            ->where('status', 'approved');

        // Show team (same department) leave by default
        if ($user->department_id) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        $colors = [
            'Annual Leave' => '#3b82f6',
            'Sick Leave' => '#ef4444',
            'Personal Leave' => '#f59e0b',
        ];

        $events = $query->get()->map(function ($leave) use ($colors, $user) {
            return [
                'id' => $leave->id,
                'title' => $leave->user_id === $user->id ? 'You (' . $leave->leave_type . ')' : $leave->user->name,
                'leave_type' => $leave->leave_type,
                'start_date' => $leave->start_date->format('Y-m-d'),
                'end_date' => $leave->end_date->format('Y-m-d'),
                'color' => $colors[$leave->leave_type] ?? '#6b7280',
                'is_mine' => $leave->user_id === $user->id,
            ];
        });

        return response()->json(['data' => $events]);
    }
}
