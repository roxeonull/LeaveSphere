<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkflowController extends Controller
{
    public function index()
    {
        $workflows = Workflow::with('department', 'steps')->latest()->get();
        $departments = \App\Models\Department::orderBy('name')->get(['id', 'name']);
        return view('workflows.index', compact('workflows', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'steps' => 'required|array|min:1',
            'steps.*' => 'required|string|max:100',
        ]);

        DB::transaction(function () use ($validated) {
            $workflow = Workflow::create([
                'name' => $validated['name'],
                'department_id' => $validated['department_id'] ?? null,
            ]);

            foreach ($validated['steps'] as $index => $step) {
                WorkflowStep::create([
                    'workflow_id' => $workflow->id,
                    'approver_role' => $step,
                    'level' => $index + 1,
                ]);
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Workflow created successfully.']);
        }

        return redirect()->route('workflows.index')->with('success', 'Workflow created successfully.');
    }

    public function update(Request $request, $id)
    {
        $workflow = Workflow::findOrFail($id);
        $workflow->update($request->only('name', 'department_id'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Workflow updated.']);
        }

        return redirect()->route('workflows.index')->with('success', 'Workflow updated.');
    }

    public function destroy(Request $request, $id)
    {
        Workflow::findOrFail($id)->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Workflow deleted.']);
        }

        return redirect()->route('workflows.index')->with('success', 'Workflow deleted.');
    }
}
