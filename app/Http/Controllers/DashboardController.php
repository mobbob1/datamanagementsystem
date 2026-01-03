<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Contracts\Database\Query\Builder as BaseBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $applyVisibility = function (Builder $q) use ($user) {
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return $q;
            }
            $q->whereHas('classification', function ($w) use ($user) {
                $w->where('clearance_level', '<=', $user->clearance_level);
            });
            $q->where(function ($w) use ($user) {
                $w->where('created_by', $user->id);
                if ($user->organization_unit_id) {
                    $w->orWhere('origin_unit_id', $user->organization_unit_id);
                }
                $w->orWhereHas('permissions', function ($p) use ($user) {
                    $p->where('user_id', $user->id)->where('can_view', true);
                });
                $w->orWhereHas('folder', function ($f) use ($user) {
                    $f->where('organization_unit_id', $user->organization_unit_id);
                });
                $w->orWhereHas('folder.permissions', function ($fp) use ($user) {
                    $fp->where('user_id', $user->id)->where('can_view', true);
                });
                $w->orWhereExists(function ($sub) use ($user) {
                    $sub->select(DB::raw(1))
                        ->from('workflow_instances as wi')
                        ->join('workflow_steps as ws', 'ws.id', '=', 'wi.current_step_id')
                        ->whereColumn('wi.document_id', 'documents.id')
                        ->where('wi.status', 'running')
                        ->where(function ($cc) use ($user) {
                            $cc->where(function ($c) use ($user) {
                                $c->where('ws.assignee_type', 'role')
                                    ->where(function ($d) use ($user) {
                                        $d->where('ws.assignee_value', (string) optional($user->role)->id)
                                            ->orWhere('ws.assignee_value', (string) optional($user->role)->key);
                                    });
                            })
                                ->orWhere(function ($c) use ($user) {
                                    $c->where('ws.assignee_type', 'unit')
                                        ->where('ws.assignee_value', (string) $user->organization_unit_id);
                                })
                                ->orWhere(function ($c) use ($user) {
                                    $c->where('ws.assignee_type', 'user')
                                        ->where('ws.assignee_value', (string) $user->id);
                                })
                                ->orWhere(function ($c) use ($user) {
                                    $c->where('ws.assignee_type', 'registrar')
                                        ->whereRaw('documents.created_by = ?', [(int) $user->id]);
                                });
                        });
                });
            });
            return $q;
        };

        $intakeCount = Document::query()
            ->when(!(method_exists($user, 'isAdmin') && $user->isAdmin()), function (Builder $q) use ($applyVisibility) {
                $applyVisibility($q);
            })
            ->where('status', 'draft')
            ->count();

        $awaitingCount = Document::query()
            ->when(!(method_exists($user, 'isAdmin') && $user->isAdmin()), function (Builder $q) use ($user) {
                $q->whereHas('classification', function ($w) use ($user) {
                    $w->where('clearance_level', '<=', $user->clearance_level);
                });
            })
            ->whereExists(function ($sub) use ($user) {
                $sub->select(DB::raw(1))
                    ->from('workflow_instances as wi')
                    ->join('workflow_steps as ws', 'ws.id', '=', 'wi.current_step_id')
                    ->whereColumn('wi.document_id', 'documents.id')
                    ->where('wi.status', 'running')
                    ->where(function ($cc) use ($user) {
                        $cc->where(function ($c) use ($user) {
                            $c->where('ws.assignee_type', 'role')
                                ->where(function ($d) use ($user) {
                                    $d->where('ws.assignee_value', (string) optional($user->role)->id)
                                        ->orWhere('ws.assignee_value', (string) optional($user->role)->key);
                                });
                        })
                            ->orWhere(function ($c) use ($user) {
                                $c->where('ws.assignee_type', 'unit')
                                    ->where('ws.assignee_value', (string) $user->organization_unit_id);
                            })
                            ->orWhere(function ($c) use ($user) {
                                $c->where('ws.assignee_type', 'user')
                                    ->where('ws.assignee_value', (string) $user->id);
                            })
                            ->orWhere(function ($c) use ($user) {
                                $c->where('ws.assignee_type', 'registrar')
                                    ->whereRaw('documents.created_by = ?', [(int) $user->id]);
                            });
                    });
            })
            ->count();

        $archivedCount = Document::query()
            ->when(!(method_exists($user, 'isAdmin') && $user->isAdmin()), function (Builder $q) use ($applyVisibility) {
                $applyVisibility($q);
            })
            ->whereNotNull('archived_at')
            ->count();

        $stats = [
            'intake' => $intakeCount,
            'awaiting' => $awaitingCount,
            'archived' => $archivedCount,
            'overdue' => 0,
        ];

        return view('dashboard', compact('stats'));
    }
}
