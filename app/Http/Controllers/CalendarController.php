<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $departments = \App\Models\Department::orderBy('name')->get(['id', 'name']);
        return view('calendar.index', compact('departments'));
    }

    public function events(Request $request)
    {
        $colors = [
            'Annual Leave' => '#3b82f6',
            'Sick Leave' => '#ef4444',
            'Personal Leave' => '#fbbf24',
        ];

        $query = LeaveRequest::with('user.department')->where('status', 'approved');

        if ($request->filled('department')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        $leaveRequests = $query->get();

        $events = $leaveRequests->map(function ($req) use ($colors) {
            return [
                'title' => $req->user->name ?? 'Unknown',
                'start' => $req->start_date->format('Y-m-d'),
                'end' => $req->end_date->copy()->addDay()->format('Y-m-d'),
                'color' => $colors[$req->leave_type] ?? '#6b7280',
                'textColor' => '#fff',
            ];
        });

        return response()->json($events);
    }
}
