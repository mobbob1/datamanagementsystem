<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use App\Models\Folder;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user;
    }

    public function view(User $user, Document $document): bool
    {
        if ($user->isAdmin()) return true;
        if (!$this->passesClearance($user, $document)) return false;
        if ($document->created_by === $user->id) return true;
        if ($document->origin_unit_id && $user->organization_unit_id && $document->origin_unit_id === $user->organization_unit_id) return true;
        if ($document->permissions()->where('user_id', $user->id)->where('can_view', true)->exists()) return true;
        if ($this->passesFolderPermission($user, $document, false)) return true;
        // Allow viewing if the user is assigned to act on the current workflow step
        $instance = WorkflowInstance::where('document_id', $document->id)->latest()->first();
        if ($instance && $instance->status === 'running' && $instance->current_step_id) {
            $step = WorkflowStep::find($instance->current_step_id);
            if ($step) {
                switch ($step->assignee_type) {
                    case 'role':
                        $val = (string) $step->assignee_value;
                        if ((string) optional($user->role)->key === $val || (string) optional($user->role)->id === $val) return true;
                        break;
                    case 'unit':
                        if ((string) $user->organization_unit_id === (string) $step->assignee_value) return true;
                        break;
                    case 'user':
                        if ((string) $user->id === (string) $step->assignee_value) return true;
                        break;
                    case 'registrar':
                        if ((string) $document->created_by === (string) $user->id) return true;
                        break;
                }
            }
        }
        // Allow viewing for users who were assignees of any completed or current steps in this running/finished instance
        if ($instance) {
            $steps = WorkflowStep::where('workflow_definition_id', $instance->workflow_definition_id)
                ->orderBy('position')
                ->get();
            $currentPos = null;
            if ($instance->current_step_id) {
                $cs = $steps->firstWhere('id', $instance->current_step_id);
                $currentPos = optional($cs)->position;
            }
            foreach ($steps as $s) {
                if ($instance->status === 'running' && $currentPos !== null && $s->position > $currentPos) {
                    continue;
                }
                switch ($s->assignee_type) {
                    case 'role':
                        $val = (string) $s->assignee_value;
                        if ((string) optional($user->role)->key === $val || (string) optional($user->role)->id === $val) return true;
                        break;
                    case 'unit':
                        if ((string) $user->organization_unit_id === (string) $s->assignee_value) return true;
                        break;
                    case 'user':
                        if ((string) $user->id === (string) $s->assignee_value) return true;
                        break;
                    case 'registrar':
                        if ((string) $document->created_by === (string) $user->id) return true;
                        break;
                }
            }
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->clearance_level > 0; // any active user with some clearance can create
    }

    public function update(User $user, Document $document): bool
    {
        if ($user->isAdmin()) return true;
        if (!$this->passesClearance($user, $document)) return false;
        if ($document->legal_hold) return false;
        if ($document->created_by === $user->id) return true;
        if ($document->permissions()->where('user_id', $user->id)->where('can_edit', true)->exists()) return true;
        if ($this->passesFolderPermission($user, $document, true)) return true;
        return false;
    }

    public function lock(User $user, Document $document): bool
    {
        return $this->update($user, $document);
    }

    public function unlock(User $user, Document $document): bool
    {
        if ($user->isAdmin()) return true;
        if ($document->locked_by === $user->id) return true;
        return false;
    }

    public function manageLegalHold(User $user, Document $document): bool
    {
        // Restrict legal hold management to admins for now
        return $user->isAdmin();
    }

    protected function passesClearance(User $user, Document $document): bool
    {
        $docLevel = optional($document->classification)->clearance_level ?? 0;
        return $user->clearance_level >= $docLevel;
    }

    protected function passesFolderPermission(User $user, Document $document, bool $edit = false): bool
    {
        if (!$document->folder_id) return false;
        $folderId = $document->folder_id;
        while ($folderId) {
            $folder = Folder::find($folderId);
            if (!$folder) break;
            // Unit-based access
            if ($folder->organization_unit_id && $user->organization_unit_id && $folder->organization_unit_id === $user->organization_unit_id) {
                return true;
            }
            // Explicit permission
            $perm = $folder->permissions()->where('user_id', $user->id)->first();
            if ($perm) {
                if (!$edit && $perm->can_view) return true;
                if ($edit && $perm->can_edit) return true;
            }
            $folderId = $folder->parent_id;
        }
        return false;
    }
}
