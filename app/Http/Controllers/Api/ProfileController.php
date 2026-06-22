<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * GET /api/profile
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'employee_id' => $user->employee_id,
                'name' => $user->name,
                'email' => $user->email,
                'department' => $user->department->name ?? null,
                'position' => $user->position,
                'role' => $user->role,
                'leave_balance' => $user->leave_balance,
                'status' => $user->status,
                'initials' => $user->initials,
                'joined_at' => $user->created_at->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * PUT /api/profile/password
     * Employee self-service password change.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini salah.'],
            ]);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['success' => true, 'message' => 'Password berhasil diubah.']);
    }
}
