<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\WorkflowNotifier;

class WorkflowRuntimeController extends Controller
{
    public function approve(Request $request, Document $document): RedirectResponse
    {
        $instance = WorkflowInstance::where('document_id', $document->id)->latest()->first();
        if (!$instance || $instance->status !== 'running') {
            return back()->withErrors(['workflow' => 'No running workflow for this document.']);
        }
        $step = WorkflowStep::find($instance->current_step_id);
        if (!$step) {
            return back()->withErrors(['workflow' => 'Current step not found.']);
        }
        if (!$this->userCanActOnStep($request->user(), $document, $step)) {
            return back()->withErrors(['workflow' => 'You are not assigned to act on this step.']);
        }

        // Move to next step or complete
        $next = WorkflowStep::where('workflow_definition_id', $step->workflow_definition_id)
            ->where('position', '>', $step->position)
            ->orderBy('position')
            ->first();

        if ($next) {
            $instance->current_step_id = $next->id;
            $instance->save();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'workflow.step.approved',
                'subject_type' => Document::class,
                'subject_id' => $document->id,
                'properties' => ['from_step_id' => $step->id, 'to_step_id' => $next->id],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            $document->status = 'submitted';
            $document->save();
            // Prompt the next assignees via email
            WorkflowNotifier::notifyAssignees($document, $next);
            return back()->with('status', 'Step approved. Moved to next step.');
        }

        // No next step -> complete
        $instance->status = 'completed';
        $instance->current_step_id = null;
        $instance->save();
        $document->status = 'approved';
        $document->save();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'workflow.completed',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('status', 'Workflow completed and document approved.');
    }

    public function reject(Request $request, Document $document): RedirectResponse
    {
        $instance = WorkflowInstance::where('document_id', $document->id)->latest()->first();
        if (!$instance || $instance->status !== 'running') {
            return back()->withErrors(['workflow' => 'No running workflow for this document.']);
        }
        $step = WorkflowStep::find($instance->current_step_id);
        if (!$step) {
            return back()->withErrors(['workflow' => 'Current step not found.']);
        }
        if (!$this->userCanActOnStep($request->user(), $document, $step)) {
            return back()->withErrors(['workflow' => 'You are not assigned to act on this step.']);
        }

        $instance->status = 'canceled';
        $instance->save();
        $document->status = 'rejected';
        $document->save();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'workflow.rejected',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'properties' => ['step_id' => $step->id],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('status', 'Workflow rejected.');
    }

    protected function userCanActOnStep($user, Document $document, WorkflowStep $step): bool
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }
        switch ($step->assignee_type) {
            case 'role':
                $val = (string) $step->assignee_value;
                return (string) optional($user->role)->key === $val || (string) optional($user->role)->id === $val;
            case 'unit':
                return (string) $user->organization_unit_id === (string) $step->assignee_value;
            case 'user':
                return (string) $user->id === (string) $step->assignee_value;
            case 'registrar':
                return (string) $document->created_by === (string) $user->id;
            default:
                return false;
        }
    }
}
