<?php

namespace App\Http\Controllers;

use App\Models\WorkflowDefinition;
use App\Models\Role;
use App\Models\OrganizationUnit;
use App\Models\User;
use App\Models\Classification;
use Illuminate\View\View;

class TrainingController extends Controller
{
    public function index(): View
    {
        $workflows = WorkflowDefinition::with(['steps' => function($q){ $q->orderBy('position'); }])->orderBy('name')->get();
        // Resolve human-friendly assignee labels for each step
        foreach ($workflows as $wf) {
            $wf->steps->transform(function($s){
                $label = '-';
                switch ($s->assignee_type) {
                    case 'role':
                        $role = Role::where('key', $s->assignee_value)->orWhere('id', $s->assignee_value)->first();
                        $label = $role ? ($role->name.' ('.$role->key.')') : (string)$s->assignee_value;
                        break;
                    case 'unit':
                        $unit = OrganizationUnit::where('id', $s->assignee_value)
                            ->orWhere('code', $s->assignee_value)
                            ->orWhere('name', $s->assignee_value)
                            ->first();
                        $label = $unit ? ($unit->code ? ($unit->name.' ('.$unit->code.')') : $unit->name) : ('Unit #'.(string)$s->assignee_value);
                        break;
                    case 'user':
                        $u = User::find($s->assignee_value);
                        $label = $u ? ($u->name.($u->email ? ' · '.$u->email : '')) : ('User #'.(string)$s->assignee_value);
                        break;
                    case 'registrar':
                        $label = 'Document Registrar (Creator)';
                        break;
                }
                $s->assignee_display = $label;
                return $s;
            });
        }

        $roles = Role::orderBy('name')->get();
        $units = OrganizationUnit::orderBy('name')->get();
        $public = Classification::where('key', 'public')->first();

        return view('training.index', [
            'workflows' => $workflows,
            'roles' => $roles,
            'units' => $units,
            'publicClassificationId' => optional($public)->id,
        ]);
    }
}
