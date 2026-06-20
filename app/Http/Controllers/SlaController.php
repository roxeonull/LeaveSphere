<?php

namespace App\Http\Controllers;

use App\Models\SlaRecord;

class SlaController extends Controller
{
    public function index()
    {
        $slaRecords = SlaRecord::with('leaveRequest.user.department')
            ->whereHas('leaveRequest', function ($q) {
                $q->where('status', 'pending');
            })
            ->orderBy('deadline')
            ->get();

        $stats = [
            'total_pending' => $slaRecords->count(),
            'breached' => $slaRecords->where('breached', true)->count(),
            'at_risk' => $slaRecords->filter(function ($s) {
                if ($s->breached) return false;
                $hoursLeft = now()->diffInHours($s->deadline, false);
                return $hoursLeft >= 0 && $hoursLeft < 4;
            })->count(),
            'avg_response' => '3.8h', // Placeholder: compute from historical approvals if needed
        ];

        // Department performance: avg hours between leave_request creation and its approval
        $deptPerformance = \App\Models\Department::with(['users.leaveRequests' => function ($q) {
                $q->whereIn('status', ['approved', 'rejected']);
            }])
            ->get()
            ->map(function ($dept) {
                $hours = [];
                foreach ($dept->users as $user) {
                    foreach ($user->leaveRequests as $leave) {
                        $approval = $leave->approvals()->latest()->first();
                        if ($approval) {
                            $hours[] = $leave->created_at->diffInHours($approval->created_at);
                        }
                    }
                }
                return [
                    'name' => $dept->name,
                    'avg_hours' => count($hours) ? round(array_sum($hours) / count($hours), 1) : 0,
                ];
            });

        return view('sla.index', compact('slaRecords', 'stats', 'deptPerformance'));
    }
}
