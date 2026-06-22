<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\SlaRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LeaveRequestController extends Controller
{
    /**
     * GET /api/leave-requests
     * History list for the logged-in employee, newest first.
     */
    public function index(Request $request)
    {
        $query = $request->user()->leaveRequests()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->get()->map(fn($l) => $this->formatLeave($l));

        return response()->json(['data' => $leaves]);
    }

    /**
     * GET /api/leave-requests/{id}
     */
    public function show(Request $request, $id)
    {
        $leave = $request->user()->leaveRequests()->with('approvals.approver')->findOrFail($id);

        $data = $this->formatLeave($leave);
        $data['approvals'] = $leave->approvals->map(fn($a) => [
            'approver' => $a->approver->name ?? 'Unknown',
            'status' => $a->status,
            'notes' => $a->notes,
            'date' => $a->created_at->format('Y-m-d H:i'),
        ]);

        return response()->json(['data' => $data]);
    }

    /**
     * POST /api/leave-requests
     * Submit a new leave request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:Annual Leave,Sick Leave,Personal Leave',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $user = $request->user();

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $totalDays = $start->diffInDays($end) + 1;

        // Max 25 days per request
        if ($totalDays > 25) {
            return response()->json([
                'success' => false,
                'message' => 'Maksimal pengajuan cuti adalah 25 hari per pengajuan.',
            ], 422);
        }

        if ($validated['leave_type'] === 'Annual Leave') {
            if ($totalDays > $user->leave_balance) {
                return response()->json([
                    'success' => false,
                    'message' => "Sisa cuti tidak cukup. Sisa cuti Anda: {$user->leave_balance} hari.",
                ], 422);
            }
        }

        $leave = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type' => $validated['leave_type'],
            'start_date' => $start,
            'end_date' => $end,
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        SlaRecord::create([
            'leave_request_id' => $leave->id,
            'deadline' => now()->addHours(24),
            'breached' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan cuti berhasil dikirim.',
            'data' => $this->formatLeave($leave),
        ], 201);
    }

    /**
     * DELETE /api/leave-requests/{id}
     * Cancel a pending leave request.
     */
    public function destroy(Request $request, $id)
    {
        $leave = $request->user()->leaveRequests()->findOrFail($id);

        if ($leave->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan dengan status pending yang dapat dibatalkan.',
            ], 422);
        }

        $leave->delete();

        return response()->json(['success' => true, 'message' => 'Pengajuan cuti dibatalkan.']);
    }

    private function formatLeave(LeaveRequest $leave): array
    {
        return [
            'id' => $leave->id,
            'leave_type' => $leave->leave_type,
            'start_date' => $leave->start_date->format('Y-m-d'),
            'end_date' => $leave->end_date->format('Y-m-d'),
            'total_days' => $leave->total_days,
            'reason' => $leave->reason,
            'status' => $leave->status,
            'submitted_at' => $leave->created_at->format('Y-m-d H:i'),
        ];
    }
}
