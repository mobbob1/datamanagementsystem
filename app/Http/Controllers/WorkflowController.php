<?php

namespace App\Http\Controllers;

use App\Models\WorkflowDefinition;
use App\Models\WorkflowStep;
use App\Models\DocumentType;
use App\Models\Role;
use App\Models\User;
use App\Models\OrganizationUnit;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class WorkflowController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('admin');
        $defs = WorkflowDefinition::withCount('steps')->with('documentType')->orderBy('name')->paginate(15);
        return view('workflows.index', compact('defs'));
    }

    public function create(): View
    {
        $this->authorize('admin');
        return view('workflows.create', [
            'types' => DocumentType::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'key' => ['required','string','max:50','unique:workflow_definitions,key'],
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:1000'],
            'document_type_id' => ['nullable','exists:document_types,id'],
            'is_active' => ['sometimes','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? true);
        $def = WorkflowDefinition::create($data);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'workflow.definition.created',
            'subject_type' => WorkflowDefinition::class,
            'subject_id' => $def->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return redirect()->route('workflows.edit', $def)->with('status', 'Workflow created. Add steps below.');
    }

    public function edit(WorkflowDefinition $workflow): View
    {
        $this->authorize('admin');
        $workflow->load(['steps']);
        return view('workflows.edit', [
            'workflow' => $workflow,
            'types' => DocumentType::orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),
            'units' => OrganizationUnit::orderBy('name')->get(),
            'users' => User::orderBy('name')->limit(200)->get(),
        ]);
    }

    public function update(Request $request, WorkflowDefinition $workflow): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'key' => ['required','string','max:50','unique:workflow_definitions,key,'.$workflow->id],
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:1000'],
            'document_type_id' => ['nullable','exists:document_types,id'],
            'is_active' => ['sometimes','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $workflow->update($data);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'workflow.definition.updated',
            'subject_type' => WorkflowDefinition::class,
            'subject_id' => $workflow->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return back()->with('status', 'Workflow updated.');
    }

    public function destroy(WorkflowDefinition $workflow): RedirectResponse
    {
        $this->authorize('admin');
        $id = $workflow->id;
        $workflow->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'workflow.definition.deleted',
            'subject_type' => WorkflowDefinition::class,
            'subject_id' => $id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('workflows.index')->with('status', 'Workflow deleted.');
    }

    public function storeStep(Request $request, WorkflowDefinition $workflow): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'key' => ['required','string','max:50'],
            'name' => ['required','string','max:255'],
            'assignee_type' => ['required','in:role,unit,user,registrar'],
            'assignee_value' => ['nullable','string','max:255'],
            'requires_approval' => ['sometimes','boolean'],
            'allow_edit' => ['sometimes','boolean'],
        ]);
        $data['requires_approval'] = (bool)($data['requires_approval'] ?? true);
        $data['allow_edit'] = (bool)($data['allow_edit'] ?? false);
        $pos = (int) WorkflowStep::where('workflow_definition_id', $workflow->id)->max('position');
        $step = $workflow->steps()->create(array_merge($data, ['position' => $pos + 1]));
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'workflow.step.created',
            'subject_type' => WorkflowStep::class,
            'subject_id' => $step->id,
            'properties' => ['workflow_definition_id' => $workflow->id],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return back()->with('status', 'Step added.');
    }

    public function destroyStep(WorkflowDefinition $workflow, WorkflowStep $step): RedirectResponse
    {
        $this->authorize('admin');
        if ($step->workflow_definition_id !== $workflow->id) abort(404);
        $deletedPos = $step->position;
        $step->delete();
        WorkflowStep::where('workflow_definition_id', $workflow->id)
            ->where('position', '>', $deletedPos)
            ->orderBy('position')
            ->get()
            ->each(function($s) { $s->decrement('position'); });
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'workflow.step.deleted',
            'subject_type' => WorkflowStep::class,
            'subject_id' => $step->id,
            'properties' => ['workflow_definition_id' => $workflow->id],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return back()->with('status', 'Step removed.');
    }

    public function moveStepUp(WorkflowDefinition $workflow, WorkflowStep $step): RedirectResponse
    {
        $this->authorize('admin');
        if ($step->workflow_definition_id !== $workflow->id) abort(404);
        if ($step->position <= 1) return back();
        $prev = WorkflowStep::where('workflow_definition_id', $workflow->id)
            ->where('position', $step->position - 1)
            ->first();
        if ($prev) {
            DB::transaction(function() use ($step, $prev) {
                $currentPos = $step->position; // e.g., 3
                $prevPos = $prev->position;    // e.g., 2
                // Use a temporary position to avoid violating the unique constraint
                $step->update(['position' => 0]);
                $prev->update(['position' => $currentPos]);
                $step->update(['position' => $prevPos]);
            });
        }
        return back()->with('status', 'Step moved up.');
    }

    public function moveStepDown(WorkflowDefinition $workflow, WorkflowStep $step): RedirectResponse
    {
        $this->authorize('admin');
        if ($step->workflow_definition_id !== $workflow->id) abort(404);
        $next = WorkflowStep::where('workflow_definition_id', $workflow->id)
            ->where('position', $step->position + 1)
            ->first();
        if ($next) {
            DB::transaction(function() use ($step, $next) {
                $currentPos = $step->position; // e.g., 2
                $nextPos = $next->position;    // e.g., 3
                // Use a temporary position to avoid violating the unique constraint
                $next->update(['position' => 0]);
                $step->update(['position' => $nextPos]);
                $next->update(['position' => $currentPos]);
            });
        }
        return back()->with('status', 'Step moved down.');
    }
}
