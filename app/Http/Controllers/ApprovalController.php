<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $statusCounts = [
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];

        $departments = \App\Models\Department::orderBy('name')->get(['id', 'name']);

        return view('approvals.index', compact('statusCounts', 'departments'));
    }

    /**
     * AJAX endpoint - returns JSON for live search/filter without page reload.
     */
    public function data(Request $request)
    {
        $query = LeaveRequest::with('user.department');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('department', function ($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $leaveRequests = $query->latest()->paginate(10);

        $items = $leaveRequests->getCollection()->map(function ($req) {
            $latestApproval = $req->approvals()->latest()->first();
            return [
                'id' => $req->id,
                'initials' => $req->user->initials ?? 'NA',
                'name' => $req->user->name ?? 'Unknown',
                'dept' => $req->user->department->name ?? '-',
                'type' => $req->leave_type,
                'start' => $req->start_date->format('Y-m-d'),
                'end' => $req->end_date->format('Y-m-d'),
                'days' => $req->total_days,
                'status' => $req->status,
                'reason' => $req->status === 'rejected' ? ($latestApproval->notes ?? '') : '',
            ];
        });

        return response()->json([
            'data' => $items,
            'current_page' => $leaveRequests->currentPage(),
            'last_page' => $leaveRequests->lastPage(),
            'total' => $leaveRequests->total(),
            'from' => $leaveRequests->firstItem(),
            'to' => $leaveRequests->lastItem(),
        ]);
    }

    public function approve(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->update(['status' => 'approved']);

        Approval::create([
            'leave_request_id' => $leave->id,
            'approver_id' => Auth::id(),
            'status' => 'approved',
            'notes' => $request->input('notes'),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Leave request approved successfully.']);
        }

        return back()->with('success', 'Leave request approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        $leave = LeaveRequest::findOrFail($id);
        $leave->update(['status' => 'rejected']);

        Approval::create([
            'leave_request_id' => $leave->id,
            'approver_id' => Auth::id(),
            'status' => 'rejected',
            'notes' => $request->reason,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Leave request rejected.']);
        }

        return back()->with('success', 'Leave request rejected.');
    }

    public function show($id)
    {
        $leave = LeaveRequest::with('user', 'approvals')->findOrFail($id);
        return response()->json($leave);
    }
}
