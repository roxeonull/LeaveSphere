<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'managers' => User::where('role', 'manager')->count(),
            'employees' => User::where('role', 'employee')->count(),
        ];

        $departments = Department::orderBy('name')->get(['id', 'name']);

        return view('users.index', compact('stats', 'departments'));
    }

    /**
     * AJAX endpoint - returns JSON for live search/filter without page reload.
     */
    public function data(Request $request)
    {
        $query = User::with('department');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $paginated = $query->orderBy('name')->paginate(10);

        $items = $paginated->getCollection()->map(function ($u) {
            return [
                'db_id' => $u->id,
                'employee_id' => $u->employee_id,
                'name' => $u->name,
                'email' => $u->email,
                'department_id' => $u->department_id,
                'dept' => $u->department->name ?? '-',
                'position' => $u->position,
                'role' => $u->role,
                'status' => $u->status,
                'leave_balance' => $u->leave_balance,
                'initials' => $u->initials,
            ];
        });

        return response()->json([
            'data' => $items,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'total' => $paginated->total(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|unique:users,employee_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string',
            'role' => ['required', Rule::in(['super_admin', 'manager', 'employee'])],
            'leave_balance' => 'nullable|integer|min:0|max:25',
            'password' => 'required|string|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'active';

        $user = User::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User created successfully.', 'user' => $user]);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string',
            'role' => ['required', Rule::in(['super_admin', 'manager', 'employee'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'leave_balance' => 'nullable|integer|min:0|max:25',
        ]);

        $user->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User updated successfully.', 'user' => $user]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        User::findOrFail($id)->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User deleted.']);
        }

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $tempPassword = 'Temp' . rand(1000, 9999) . '!';
        $user->update(['password' => Hash::make($tempPassword)]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Password reset. Temporary password: {$tempPassword}",
                'temp_password' => $tempPassword,
            ]);
        }

        return back()->with('success', "Password reset. Temporary password: {$tempPassword}");
    }

    public function export($format)
    {
        // In production: use maatwebsite/excel for Excel, barryvdh/laravel-dompdf for PDF
        if ($format === 'excel') {
            return response('Excel export - integrate with maatwebsite/excel package', 200)
                ->header('Content-Type', 'text/plain');
        }
        if ($format === 'pdf') {
            return response('PDF export - integrate with barryvdh/laravel-dompdf package', 200)
                ->header('Content-Type', 'text/plain');
        }

        return back()->with('error', 'Invalid export format.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        // In production: use maatwebsite/excel Import classes
        return back()->with('success', 'Users imported successfully. (Integrate maatwebsite/excel for actual processing)');
    }
}
