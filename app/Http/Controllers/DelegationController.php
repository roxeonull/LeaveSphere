<?php

namespace App\Http\Controllers;

use App\Models\Delegation;
use App\Models\User;
use Illuminate\Http\Request;

class DelegationController extends Controller
{
    public function index()
    {
        $delegations = Delegation::with('delegator', 'delegate')->latest()->get();

        $now = now();
        $stats = [
            'active' => $delegations->filter(fn($d) => $now->gte($d->start_date) && $now->lte($d->end_date))->count(),
            'scheduled' => $delegations->filter(fn($d) => $now->lt($d->start_date))->count(),
            'this_month' => $delegations->filter(fn($d) => $d->created_at->isCurrentMonth())->count(),
            'expired' => $delegations->filter(fn($d) => $now->gt($d->end_date))->count(),
        ];

        $users = User::orderBy('name')->get(['id', 'name']);

        return view('delegation.index', compact('delegations', 'stats', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'delegator_id' => 'required|exists:users,id|different:delegate_id',
            'delegate_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'permissions' => 'array',
        ]);

        Delegation::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Delegation created successfully.']);
        }

        return redirect()->route('delegation.index')->with('success', 'Delegation created successfully.');
    }

    public function update(Request $request, $id)
    {
        $delegation = Delegation::findOrFail($id);
        $delegation->update($request->all());

        return redirect()->route('delegation.index')->with('success', 'Delegation updated.');
    }

    public function destroy(Request $request, $id)
    {
        Delegation::findOrFail($id)->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Delegation deleted.']);
        }

        return redirect()->route('delegation.index')->with('success', 'Delegation deleted.');
    }

    public function revoke(Request $request, $id)
    {
        $delegation = Delegation::findOrFail($id);
        $delegation->update(['end_date' => now()->subSecond()]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Delegation revoked.']);
        }

        return redirect()->route('delegation.index')->with('success', 'Delegation revoked.');
    }
}
