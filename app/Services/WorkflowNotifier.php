<?php

namespace App\Services;

use App\Models\Document;
use App\Models\OrganizationUnit;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkflowStep;
use App\Notifications\WorkflowStepAssigned;
use App\Models\ActivityLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class WorkflowNotifier
{
    /**
     * Notify all users responsible for the given step.
     */
    public static function notifyAssignees(Document $document, WorkflowStep $step): void
    {
        $recipients = self::resolveRecipients($document, $step);
        if ($recipients->isEmpty()) {
            return;
        }
        try {
            Notification::send($recipients, new WorkflowStepAssigned($document, $step));
        } catch (\Throwable $e) {
            Log::warning('Workflow email notification failed', [
                'document_id' => $document->id,
                'step_id' => $step->id,
                'error' => $e->getMessage(),
            ]);
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'notification.failed',
                'subject_type' => Document::class,
                'subject_id' => $document->id,
                'properties' => [
                    'step_id' => $step->id,
                    'channel' => 'mail',
                    'error' => substr($e->getMessage(), 0, 500),
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            // Do not rethrow; allow the primary action to succeed even if email fails
        }
    }

    /**
     * Resolve users to notify from the step assignee.
     */
    public static function resolveRecipients(Document $document, WorkflowStep $step): Collection
    {
        switch ($step->assignee_type) {
            case 'role':
                $role = Role::where('id', $step->assignee_value)
                    ->orWhere('key', $step->assignee_value)
                    ->first();
                if (!$role) { return collect(); }
                return User::where('role_id', $role->id)
                    ->whereNotNull('email')
                    ->get();
            case 'unit':
                $unit = OrganizationUnit::where('id', $step->assignee_value)
                    ->orWhere('code', $step->assignee_value)
                    ->orWhere('name', $step->assignee_value)
                    ->first();
                if (!$unit) { return collect(); }
                return User::where('organization_unit_id', $unit->id)
                    ->whereNotNull('email')
                    ->get();
            case 'user':
                $u = User::where('id', $step->assignee_value)
                    ->whereNotNull('email')
                    ->first();
                return $u ? collect([$u]) : collect();
            case 'registrar':
                $creator = $document->creator()->whereNotNull('email')->first();
                return $creator ? collect([$creator]) : collect();
            default:
                return collect();
        }
    }
}
