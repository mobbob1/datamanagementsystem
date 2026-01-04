<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\DocumentType;
use App\Models\OrganizationUnit;
use App\Models\SavedSearch;
use App\Models\ActivityLog;
use App\Models\DocumentPermission;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\Role;
use App\Models\User;
use App\Services\WorkflowNotifier;
use App\Jobs\ExtractDocumentText;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $q = $request->string('q')->toString();
        $status = $request->string('status')->toString();
        $type = $request->input('type');
        $classification = $request->input('classification');
        $unit = $request->input('unit');
        $owner = $request->input('owner');
        $from = $request->input('from');
        $to = $request->input('to');
        $folder = $request->input('folder');

        if ($savedId = $request->input('saved')) {
            $saved = SavedSearch::where('id', $savedId)->where('user_id', $user->id)->where('scope', 'documents')->first();
            if ($saved) {
                $params = $saved->params ?? [];
                $q = $params['q'] ?? $q;
                $status = $params['status'] ?? $status;
                $type = $params['type'] ?? $type;
                $classification = $params['classification'] ?? $classification;
                $unit = $params['unit'] ?? $unit;
                $owner = $params['owner'] ?? $owner;
                $from = $params['from'] ?? $from;
                $to = $params['to'] ?? $to;
                $folder = $params['folder'] ?? $folder;
            }

        }

        $with = ['classification','type','originUnit','folder','files' => function($q){ $q->where('is_current', true); }];

        if (config('scout.driver') && $q) {
            try {
                $documents = Document::search($q)
                    ->query(function($qb) use ($user, $with, $status, $type, $classification, $unit, $owner, $from, $to, $folder) {
                        $qb->with($with);
                        if (!$user->isAdmin() && optional($user->role)->key !== 'registrar') {
                            $qb->whereHas('classification', function($w) use ($user) {
                                $w->where('clearance_level', '<=', $user->clearance_level);
                            });
                            $qb->where(function($w) use ($user) {
                                $w->where('created_by', $user->id);
                                if ($user->organization_unit_id) {
                                    $w->orWhere('origin_unit_id', $user->organization_unit_id);
                                }
                                $w->orWhereHas('permissions', function($p) use ($user) {
                                    $p->where('user_id', $user->id)->where('can_view', true);
                                });
                                $w->orWhereHas('folder', function($f) use ($user) {
                                    $f->where('organization_unit_id', $user->organization_unit_id);
                                });
                                $w->orWhereHas('folder.permissions', function($fp) use ($user) {
                                    $fp->where('user_id', $user->id)->where('can_view', true);
                                });
                                // Include documents where this user is the assignee of the current workflow step
                                $w->orWhereExists(function($sub) use ($user) {
                                    $sub->select(DB::raw(1))
                                        ->from('workflow_instances as wi')
                                        ->join('workflow_steps as ws', 'ws.id', '=', 'wi.current_step_id')
                                        ->whereColumn('wi.document_id', 'documents.id')
                                        ->where('wi.status', 'running')
                                        ->where(function($cc) use ($user) {
                                            $cc->where(function($c) use ($user) {
                                                $c->where('ws.assignee_type', 'role')
                                                  ->where(function($d) use ($user) {
                                                      $d->where('ws.assignee_value', (string) optional($user->role)->id)
                                                        ->orWhere('ws.assignee_value', (string) optional($user->role)->key);
                                                  });
                                            })
                                            ->orWhere(function($c) use ($user) {
                                                $c->where('ws.assignee_type', 'unit')
                                                  ->where('ws.assignee_value', (string) $user->organization_unit_id);
                                            })
                                            ->orWhere(function($c) use ($user) {
                                                $c->where('ws.assignee_type', 'user')
                                                  ->where('ws.assignee_value', (string) $user->id);
                                            })
                                            ->orWhere(function($c) use ($user) {
                                                $c->where('ws.assignee_type', 'registrar')
                                                  ->whereRaw('documents.created_by = ?', [(int) $user->id]);
                                            });
                                        });
                                });
                            });
                        }
                        if ($status) { $qb->where('status', $status); }
                        if ($type) { $qb->where('document_type_id', $type); }
                        if ($classification) { $qb->where('classification_id', $classification); }
                        if ($unit) { $qb->where('origin_unit_id', $unit); }
                        if ($folder) { $qb->where('folder_id', $folder); }
                        if ($owner) { $qb->where('created_by', $owner); }
                        if ($from) { $qb->whereDate('created_at', '>=', $from); }
                        if ($to) { $qb->whereDate('created_at', '<=', $to); }
                    })
                    ->paginate(15)
                    ->withQueryString();
            } catch (\Throwable $e) {
                // Fallback to DB query if Meilisearch is unreachable
                $query = Document::query()->with($with);

                if (!$user->isAdmin() && optional($user->role)->key !== 'registrar') {
                    $query->whereHas('classification', function($w) use ($user) {
                        $w->where('clearance_level', '<=', $user->clearance_level);
                    });
                    $query->where(function($w) use ($user) {
                        $w->where('created_by', $user->id);
                        if ($user->organization_unit_id) {
                            $w->orWhere('origin_unit_id', $user->organization_unit_id);
                        }
                        $w->orWhereHas('permissions', function($p) use ($user) {
                            $p->where('user_id', $user->id)->where('can_view', true);
                        });
                        $w->orWhereHas('folder', function($f) use ($user) {
                            $f->where('organization_unit_id', $user->organization_unit_id);
                        });
                        $w->orWhereHas('folder.permissions', function($fp) use ($user) {
                            $fp->where('user_id', $user->id)->where('can_view', true);
                        });
                    });
                }

                if ($q) {
                    try {
                        $query->whereFullText(['title','doc_number','search_text'], $q, ['mode' => 'boolean']);
                    } catch (\Throwable $e) {
                        $query->where(function($w) use ($q) {
                            $w->where('title', 'like', "%$q%")
                              ->orWhere('doc_number', 'like', "%$q%")
                              ->orWhere('search_text', 'like', "%$q%");
                        });
                    }
                }
                if ($status) { $query->where('status', $status); }
                if ($type) { $query->where('document_type_id', $type); }
                if ($classification) { $query->where('classification_id', $classification); }
                if ($unit) { $query->where('origin_unit_id', $unit); }
                if ($folder) { $query->where('folder_id', $folder); }
                if ($owner) { $query->where('created_by', $owner); }
                if ($from) { $query->whereDate('created_at', '>=', $from); }
                if ($to) { $query->whereDate('created_at', '<=', $to); }

                $documents = $query->latest()->paginate(15)->withQueryString();
            }
        } else {
            $query = Document::query()->with($with);

            if (!$user->isAdmin() && optional($user->role)->key !== 'registrar') {
                $query->whereHas('classification', function($w) use ($user) {
                    $w->where('clearance_level', '<=', $user->clearance_level);
                });
                $query->where(function($w) use ($user) {
                    $w->where('created_by', $user->id);
                    if ($user->organization_unit_id) {
                        $w->orWhere('origin_unit_id', $user->organization_unit_id);
                    }
                    $w->orWhereHas('permissions', function($p) use ($user) {
                        $p->where('user_id', $user->id)->where('can_view', true);
                    });
                    $w->orWhereHas('folder', function($f) use ($user) {
                        $f->where('organization_unit_id', $user->organization_unit_id);
                    });
                    $w->orWhereHas('folder.permissions', function($fp) use ($user) {
                        $fp->where('user_id', $user->id)->where('can_view', true);
                    });
                    // Include documents where this user is the assignee of the current workflow step
                    $w->orWhereExists(function($sub) use ($user) {
                        $sub->select(DB::raw(1))
                            ->from('workflow_instances as wi')
                            ->join('workflow_steps as ws', 'ws.id', '=', 'wi.current_step_id')
                            ->whereColumn('wi.document_id', 'documents.id')
                            ->where('wi.status', 'running')
                            ->where(function($cc) use ($user) {
                                $cc->where(function($c) use ($user) {
                                    $c->where('ws.assignee_type', 'role')
                                      ->where(function($d) use ($user) {
                                          $d->where('ws.assignee_value', (string) optional($user->role)->id)
                                            ->orWhere('ws.assignee_value', (string) optional($user->role)->key);
                                      });
                                })
                                ->orWhere(function($c) use ($user) {
                                    $c->where('ws.assignee_type', 'unit')
                                      ->where('ws.assignee_value', (string) $user->organization_unit_id);
                                })
                                ->orWhere(function($c) use ($user) {
                                    $c->where('ws.assignee_type', 'user')
                                      ->where('ws.assignee_value', (string) $user->id);
                                })
                                ->orWhere(function($c) use ($user) {
                                    $c->where('ws.assignee_type', 'registrar')
                                      ->whereRaw('documents.created_by = ?', [(int) $user->id]);
                                });
                            });
                    });
                });
            }

            if ($q) {
                try {
                    $query->whereFullText(['title','doc_number','search_text'], $q, ['mode' => 'boolean']);
                } catch (\Throwable $e) {
                    $query->where(function($w) use ($q) {
                        $w->where('title', 'like', "%$q%")
                          ->orWhere('doc_number', 'like', "%$q%")
                          ->orWhere('search_text', 'like', "%$q%");
                    });
                }
            }
            if ($status) { $query->where('status', $status); }
            if ($type) { $query->where('document_type_id', $type); }
            if ($classification) { $query->where('classification_id', $classification); }
            if ($unit) { $query->where('origin_unit_id', $unit); }
            if ($folder) { $query->where('folder_id', $folder); }
            if ($owner) { $query->where('created_by', $owner); }
            if ($from) { $query->whereDate('created_at', '>=', $from); }
            if ($to) { $query->whereDate('created_at', '<=', $to); }

            $documents = $query->latest()->paginate(15)->withQueryString();
        }

        return view('documents.index', [
            'documents' => $documents,
            'types' => DocumentType::orderBy('name')->get(),
            'classifications' => Classification::orderBy('clearance_level')->orderBy('name')->get(),
            'units' => OrganizationUnit::orderBy('name')->get(),
            'folders' => \App\Models\Folder::orderBy('name')->get(),
            'savedSearches' => SavedSearch::where('user_id', $user->id)->where('scope', 'documents')->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function pending(Request $request): View
    {
        $user = $request->user();
        $with = ['classification','type','originUnit','folder','files' => function($q){ $q->where('is_current', true); }];
        $query = Document::query()->with($with)
            ->whereExists(function($sub) use ($user) {
                $sub->select(DB::raw(1))
                    ->from('workflow_instances as wi')
                    ->join('workflow_steps as ws', 'ws.id', '=', 'wi.current_step_id')
                    ->whereColumn('wi.document_id', 'documents.id')
                    ->where('wi.status', 'running')
                    ->where(function($cc) use ($user) {
                        $cc->where(function($c) use ($user) {
                            $c->where('ws.assignee_type', 'role')
                              ->where(function($d) use ($user) {
                                  $d->where('ws.assignee_value', (string) optional($user->role)->id)
                                    ->orWhere('ws.assignee_value', (string) optional($user->role)->key);
                              });
                        })
                        ->orWhere(function($c) use ($user) {
                            $c->where('ws.assignee_type', 'unit')
                              ->where('ws.assignee_value', (string) $user->organization_unit_id);
                        })
                        ->orWhere(function($c) use ($user) {
                            $c->where('ws.assignee_type', 'user')
                              ->where('ws.assignee_value', (string) $user->id);
                        })
                        ->orWhere(function($c) use ($user) {
                            $c->where('ws.assignee_type', 'registrar')
                              ->whereRaw('documents.created_by = ?', [(int) $user->id]);
                        });
                    });
            });

        $documents = $query->latest()->paginate(15)->withQueryString();
        return view('documents.index', [
            'documents' => $documents,
            'types' => DocumentType::orderBy('name')->get(),
            'classifications' => Classification::orderBy('clearance_level')->orderBy('name')->get(),
            'units' => OrganizationUnit::orderBy('name')->get(),
            'folders' => \App\Models\Folder::orderBy('name')->get(),
            'savedSearches' => collect(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Document::class);
        return view('documents.create', [
            'types' => DocumentType::orderBy('name')->get(),
            'classifications' => Classification::orderBy('clearance_level')->orderBy('name')->get(),
            'units' => OrganizationUnit::orderBy('name')->get(),
            'prefillFolderId' => $request->input('folder_id'),
        ]);
    }

    public function archive(Request $request): View
    {
        $this->authorize('viewAny', Document::class);
        $user = $request->user();

        $query = Document::query()
            ->with(['classification','type','originUnit','files' => function($q){ $q->where('is_current', true); }]);

        // For non-admins, the Archive view lists only archived documents.
        // Admins can see all documents here to allow manual archiving.
        if (!$user->isAdmin()) {
            $query->whereNotNull('archived_at');
        }

        if (!$user->isAdmin()) {
            $query->whereHas('classification', function($w) use ($user) {
                $w->where('clearance_level', '<=', $user->clearance_level);
            });
            $query->where(function($w) use ($user) {
                $w->where('created_by', $user->id);
                if ($user->organization_unit_id) {
                    $w->orWhere('origin_unit_id', $user->organization_unit_id);
                }
                $w->orWhereHas('permissions', function($p) use ($user) {
                    $p->where('user_id', $user->id)->where('can_view', true);
                });
            });
        }

        $documents = $query->latest()->paginate(15)->withQueryString();

        return view('documents.index', [
            'documents' => $documents,
            'types' => DocumentType::orderBy('name')->get(),
            'classifications' => Classification::orderBy('clearance_level')->orderBy('name')->get(),
            'units' => OrganizationUnit::orderBy('name')->get(),
            'savedSearches' => collect(),
        ]);
    }

    public function markArchived(Document $document, Request $request): RedirectResponse
    {
        $this->authorize('admin');
        if ($document->disposed_at) {
            return back()->withErrors(['archive' => 'Document has been disposed and cannot be archived.']);
        }
        if ($document->legal_hold) {
            return back()->withErrors(['archive' => 'Document is on legal hold and cannot be archived.']);
        }
        if ($document->archived_at) {
            return back()->with('status', 'Document already archived.');
        }
        $document->archived_at = now();
        $document->status = 'archived';
        $document->save();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'document.archived',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return back()->with('status', 'Document archived.');
    }

    public function unarchive(Document $document, Request $request): RedirectResponse
    {
        $this->authorize('admin');
        if ($document->disposed_at) {
            return back()->withErrors(['archive' => 'Document has been disposed and cannot be unarchived.']);
        }
        if (!$document->archived_at) {
            return back()->with('status', 'Document is not archived.');
        }
        $document->archived_at = null;
        if ($document->status === 'archived') {
            $document->status = 'approved';
        }
        $document->save();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'document.unarchived',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return back()->with('status', 'Document unarchived.');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Document::class);
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'document_type_id' => ['required','exists:document_types,id'],
            'classification_id' => ['required','exists:classifications,id'],
            'origin_unit_id' => ['nullable','exists:organization_units,id'],
            'folder_id' => ['nullable','exists:folders,id'],
            'files' => ['required','array','min:1'],
            'files.*' => ['file','max:20480'], // 20MB each
        ]);

        $files = $request->file('files');

        $classification = Classification::find($data['classification_id']);
        if ($request->user()->clearance_level < optional($classification)->clearance_level) {
            return back()->withErrors(['classification_id' => 'Your clearance level is insufficient for this classification.'])->withInput();
        }

        DB::transaction(function() use ($data, $files) {
            $doc = new Document();
            $doc->fill($data);
            $doc->status = 'draft';
            $doc->created_by = auth()->id();
            $doc->doc_number = $this->generateDocNumber($data['origin_unit_id'] ?? null);
            $doc->save();

            $type = DocumentType::find($data['document_type_id']);
            if ($type && ($type->default_retention_months ?? 0) > 0) {
                $doc->retention_until = now()->addMonths($type->default_retention_months);
                $doc->save();
            }

            $version = 1;
            foreach ($files as $uploaded) {
                $path = $uploaded->store("documents/{$doc->id}/v{$version}", 'public');
                $fileRec = $doc->files()->create([
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $uploaded->getClientOriginalName(),
                    'mime' => $uploaded->getClientMimeType(),
                    'size' => $uploaded->getSize(),
                    'uploaded_by' => auth()->id(),
                    'version' => $version,
                    'is_current' => true,
                    'checksum' => hash_file('sha256', $uploaded->getRealPath()),
                ]);
                ExtractDocumentText::dispatch($fileRec->id);
            }
            // Start workflow if configured for this document type
            $this->startWorkflowIfConfigured($doc);
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'document.created',
                'subject_type' => Document::class,
                'subject_id' => $doc->id,
                'properties' => ['doc_number' => $doc->doc_number],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        return redirect()->route('documents.index')->with('status', 'Document created and files uploaded.');
    }

    public function show(Document $document): View
    {
        $this->authorize('view', $document);
        $document->load([
            'classification',
            'type',
            'originUnit',
            'files' => function($q){ $q->orderByDesc('version'); },
            'permissions.user',
        ]);
        $workflowInstance = \App\Models\WorkflowInstance::where('document_id', $document->id)->with('currentStep')->latest()->first();
        // Auto-heal: if the workflow instance is running but lost its current step (e.g., step was deleted),
        // rebind to the first step of the current definition so routing can continue.
        if ($workflowInstance && $workflowInstance->status === 'running' && !$workflowInstance->currentStep) {
            $firstStep = WorkflowStep::where('workflow_definition_id', $workflowInstance->workflow_definition_id)
                ->orderBy('position')
                ->first();
            if ($firstStep) {
                $workflowInstance->current_step_id = $firstStep->id;
                $workflowInstance->save();
                $workflowInstance->load('currentStep');
                // Notify assignees of the (re)initialized first step
                WorkflowNotifier::notifyAssignees($document, $firstStep);
            }
        }
        $canActOnStep = false;
        if ($workflowInstance && $workflowInstance->status === 'running') {
            $user = request()->user();
            $step = $workflowInstance->currentStep;
            if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
                $canActOnStep = true;
            } elseif ($step) {
                switch ($step->assignee_type) {
                    case 'role':
                        $canActOnStep = optional($user->role)->key === $step->assignee_value
                            || (string) optional($user->role)->id === (string) $step->assignee_value;
                        break;
                    case 'unit':
                        $canActOnStep = (string) $user->organization_unit_id === (string) $step->assignee_value;
                        break;
                    case 'user':
                        $canActOnStep = (string) $user->id === (string) $step->assignee_value;
                        break;
                    case 'registrar':
                        $canActOnStep = (string) $document->created_by === (string) $user->id;
                        break;
                }
            }
        }
        // Build workflow steps tracker for display
        $workflowSteps = collect();
        $currentPos = null;
        if ($workflowInstance) {
            $steps = WorkflowStep::where('workflow_definition_id', $workflowInstance->workflow_definition_id)
                ->orderBy('position')
                ->get();
            $currentPos = optional($workflowInstance->currentStep)->position;
            $workflowSteps = $steps->map(function($s) use ($workflowInstance, $currentPos) {
                // Resolve assignee label
                $assignee = '-';
                switch ($s->assignee_type) {
                    case 'role':
                        $role = Role::where('key', $s->assignee_value)->orWhere('id', $s->assignee_value)->first();
                        $assignee = $role ? $role->name : (string)$s->assignee_value;
                        break;
                    case 'unit':
                        $unit = OrganizationUnit::where('id', $s->assignee_value)
                            ->orWhere('code', $s->assignee_value)
                            ->orWhere('name', $s->assignee_value)
                            ->first();
                        if ($unit) {
                            $assignee = $unit->code ? ($unit->name.' ('.$unit->code.')') : $unit->name;
                        } else {
                            $assignee = 'Unit #'.(string)$s->assignee_value;
                        }
                        break;
                    case 'user':
                        $u = User::find($s->assignee_value);
                        $assignee = $u ? $u->name : ('User #'.(string)$s->assignee_value);
                        break;
                    case 'registrar':
                        $assignee = 'Document Registrar (Creator)';
                        break;
                    default:
                        $assignee = (string)$s->assignee_value;
                }
                // Determine status for each step
                $status = 'pending';
                if ($workflowInstance->status === 'completed') {
                    $status = 'done';
                } elseif ($workflowInstance->status === 'running') {
                    if (is_null($currentPos)) {
                        $status = 'pending';
                    } elseif ($s->position < $currentPos) {
                        $status = 'done';
                    } elseif ($s->position == $currentPos) {
                        $status = 'current';
                    }
                } elseif ($workflowInstance->status === 'canceled') {
                    if ($s->position < $currentPos) {
                        $status = 'done';
                    } elseif ($s->position == $currentPos) {
                        $status = 'canceled';
                    }
                }
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'position' => $s->position,
                    'assignee' => $assignee,
                    'requires_approval' => (bool)$s->requires_approval,
                    'allow_edit' => (bool)$s->allow_edit,
                    'status' => $status,
                ];
            });
        }
        return view('documents.show', [
            'document' => $document,
            'workflowInstance' => $workflowInstance,
            'canActOnStep' => $canActOnStep,
            'workflowSteps' => $workflowSteps,
            'types' => DocumentType::orderBy('name')->get(),
            'classifications' => Classification::orderBy('clearance_level')->orderBy('name')->get(),
            'units' => OrganizationUnit::orderBy('name')->get(),
            'folders' => \App\Models\Folder::orderBy('name')->get(),
        ]);
    }

    public function bulkCreate(): View
    {
        $this->authorize('create', Document::class);
        return view('documents.bulk', [
            'types' => DocumentType::orderBy('name')->get(),
            'classifications' => Classification::orderBy('clearance_level')->orderBy('name')->get(),
            'units' => OrganizationUnit::orderBy('name')->get(),
        ]);
    }

    public function bulkStore(Request $request): RedirectResponse
    {
        $this->authorize('create', Document::class);
        $data = $request->validate([
            'title' => ['nullable','string','max:255'],
            'document_type_id' => ['required','exists:document_types,id'],
            'classification_id' => ['required','exists:classifications,id'],
            'origin_unit_id' => ['nullable','exists:organization_units,id'],
            'folder_id' => ['nullable','exists:folders,id'],
            'files' => ['required','array','min:1'],
            'files.*' => ['file','max:20480'],
        ]);

        $files = $request->file('files');

        $classification = Classification::find($data['classification_id']);
        if ($request->user()->clearance_level < optional($classification)->clearance_level) {
            return back()->withErrors(['classification_id' => 'Your clearance level is insufficient for this classification.'])->withInput();
        }

        DB::transaction(function() use ($data, $files) {
            foreach ($files as $uploaded) {
                $doc = new Document();
                $doc->title = $data['title'] ?: pathinfo($uploaded->getClientOriginalName(), PATHINFO_FILENAME);
                $doc->document_type_id = $data['document_type_id'];
                $doc->classification_id = $data['classification_id'];
                $doc->origin_unit_id = $data['origin_unit_id'] ?? null;
                $doc->folder_id = $data['folder_id'] ?? null;
                $doc->status = 'draft';
                $doc->created_by = auth()->id();
                $doc->doc_number = $this->generateDocNumber($data['origin_unit_id'] ?? null);
                $doc->save();

                $type = DocumentType::find($data['document_type_id']);
                if ($type && ($type->default_retention_months ?? 0) > 0) {
                    $doc->retention_until = now()->addMonths($type->default_retention_months);
                    $doc->save();
                }

                $path = $uploaded->store("documents/{$doc->id}/v1", 'public');
                $fileRec = $doc->files()->create([
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $uploaded->getClientOriginalName(),
                    'mime' => $uploaded->getClientMimeType(),
                    'size' => $uploaded->getSize(),
                    'uploaded_by' => auth()->id(),
                    'version' => 1,
                    'is_current' => true,
                    'checksum' => hash_file('sha256', $uploaded->getRealPath()),
                ]);
                ExtractDocumentText::dispatch($fileRec->id);

                // Start workflow if configured for this document type
                $this->startWorkflowIfConfigured($doc);

                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'document.created',
                    'subject_type' => Document::class,
                    'subject_id' => $doc->id,
                    'properties' => ['doc_number' => $doc->doc_number, 'bulk' => true],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });

        return redirect()->route('documents.index')->with('status', 'Bulk upload completed.');
    }

    public function uploadVersion(Document $document, Request $request): RedirectResponse
    {
        $this->authorize('update', $document);
        if ($document->locked_by && $document->locked_by !== auth()->id()) {
            return back()->withErrors(['file' => 'Document is locked for editing.']);
        }

        $request->validate([
            'file' => ['required','file','max:20480'],
        ]);

        $currentMax = $document->files()->max('version') ?? 0;
        $version = $currentMax + 1;
        $uploaded = $request->file('file');
        $path = $uploaded->store("documents/{$document->id}/v{$version}", 'public');

        $document->files()->update(['is_current' => false]);

        $fileRec = $document->files()->create([
            'disk' => 'public',
            'path' => $path,
            'original_name' => $uploaded->getClientOriginalName(),
            'mime' => $uploaded->getClientMimeType(),
            'size' => $uploaded->getSize(),
            'uploaded_by' => auth()->id(),
            'version' => $version,
            'is_current' => true,
            'checksum' => hash_file('sha256', $uploaded->getRealPath()),
        ]);
        ExtractDocumentText::dispatch($fileRec->id);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'document.version_uploaded',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'properties' => ['version' => $version],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('status', 'New version uploaded.');
    }

    public function preview(DocumentFile $file)
    {
        $this->authorize('view', $file->document);
        $path = $file->path;
        if (!\Storage::disk($file->disk)->exists($path)) {
            abort(404);
        }
        $stream = \Storage::disk($file->disk)->readStream($path);
        return response()->stream(function() use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $file->mime ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.basename($file->original_name).'"',
        ]);
    }

    public function download(DocumentFile $file)
    {
        $this->authorize('view', $file->document);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'document.downloaded',
            'subject_type' => Document::class,
            'subject_id' => $file->document_id,
            'properties' => ['file_id' => $file->id, 'version' => $file->version],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return \Storage::disk($file->disk)->download($file->path, $file->original_name);
    }

    public function lock(Document $document): RedirectResponse
    {
        $this->authorize('lock', $document);
        $document->update(['locked_by' => auth()->id(), 'locked_at' => now()]);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'document.locked',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return back()->with('status', 'Document locked.');
    }

    public function unlock(Document $document): RedirectResponse
    {
        $this->authorize('unlock', $document);
        $document->update(['locked_by' => null, 'locked_at' => null]);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'document.unlocked',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return back()->with('status', 'Document unlocked.');
    }

    public function toggleLegalHold(Document $document, Request $request): RedirectResponse
    {
        $this->authorize('manageLegalHold', $document);
        $document->update(['legal_hold' => ! $document->legal_hold]);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $document->legal_hold ? 'document.legal_hold.enabled' : 'document.legal_hold.disabled',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return back()->with('status', 'Legal hold '.($document->legal_hold ? 'enabled' : 'disabled').'.');
    }

    public function saveSearch(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
        ]);
        $params = $request->only(['q','status','type','classification','unit','folder','owner','from','to']);
        SavedSearch::create([
            'user_id' => $request->user()->id,
            'name' => $data['name'],
            'scope' => 'documents',
            'params' => $params,
        ]);
        return back()->with('status', 'Search saved.');
    }

    public function addPermission(Document $document, Request $request): RedirectResponse
    {
        $this->authorize('update', $document);
        $data = $request->validate([
            'user_id' => ['required','exists:users,id'],
            'can_view' => ['sometimes','boolean'],
            'can_edit' => ['sometimes','boolean'],
        ]);
        $data['can_view'] = (bool)($data['can_view'] ?? true);
        $data['can_edit'] = (bool)($data['can_edit'] ?? false);
        $perm = $document->permissions()->updateOrCreate(
            ['user_id' => $data['user_id']],
            ['can_view' => $data['can_view'], 'can_edit' => $data['can_edit']]
        );
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'document.permission_added',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'properties' => ['permission_id' => $perm->id, 'user_id' => $perm->user_id, 'can_view' => $perm->can_view, 'can_edit' => $perm->can_edit],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return back()->with('status', 'Permission updated.');
    }

    public function removePermission(Document $document, DocumentPermission $permission): RedirectResponse
    {
        $this->authorize('update', $document);
        if ($permission->document_id !== $document->id) {
            abort(404);
        }
        $permission->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'document.permission_removed',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'properties' => ['permission_id' => $permission->id],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return back()->with('status', 'Permission removed.');
    }

    protected function generateDocNumber(?int $unitId): string
    {
        $year = date('Y');
        $unitCode = $unitId ? optional(OrganizationUnit::find($unitId))->code : 'GEN';
        $seq = (int) (Document::whereYear('created_at', $year)->max(DB::raw('RIGHT(doc_number, 6)')) ?? 0) + 1;
        return sprintf('%s/%s/%06d', $year, $unitCode ?: 'GEN', $seq);
    }

    protected function startWorkflowIfConfigured(Document $doc): void
    {
        $def = WorkflowDefinition::where('document_type_id', $doc->document_type_id)
            ->where('is_active', true)
            ->first();
        if (!$def) { return; }

        $firstStep = WorkflowStep::where('workflow_definition_id', $def->id)->orderBy('position')->first();
        if (!$firstStep) { return; }

        $instance = new WorkflowInstance();
        $instance->document_id = $doc->id;
        $instance->workflow_definition_id = $def->id;
        $instance->status = 'running';
        $instance->current_step_id = $firstStep->id;
        $instance->save();

        // Set document to submitted when workflow starts
        $doc->status = 'submitted';
        $doc->save();

        // Notify assignees of the first step
        WorkflowNotifier::notifyAssignees($doc, $firstStep);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'workflow.started',
            'subject_type' => Document::class,
            'subject_id' => $doc->id,
            'properties' => ['workflow_definition_id' => $def->id, 'current_step_id' => $firstStep->id],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function copy(Document $document, Request $request): RedirectResponse
    {
        $this->authorize('update', $document);
        if ($document->legal_hold) {
            return back()->withErrors(['document' => 'Document is on legal hold and cannot be copied.']);
        }
        $new = $document->replicate(['doc_number','locked_by','locked_at','archived_at','retention_until','legal_hold','status']);
        $new->title = $document->title.' (Copy)';
        $new->status = 'draft';
        $new->doc_number = $this->generateDocNumber($document->origin_unit_id);
        $new->created_by = auth()->id();
        $new->push();

        // Copy files
        $currentFiles = $document->files()->orderBy('version')->get();
        foreach ($currentFiles as $f) {
            $src = $f->path;
            $dest = str_replace("documents/{$document->id}/", "documents/{$new->id}/", $src);
            Storage::disk($f->disk)->makeDirectory(dirname($dest));
            Storage::disk($f->disk)->copy($src, $dest);
            $new->files()->create([
                'disk' => $f->disk,
                'path' => $dest,
                'original_name' => $f->original_name,
                'mime' => $f->mime,
                'size' => $f->size,
                'uploaded_by' => auth()->id(),
                'version' => $f->version,
                'is_current' => $f->is_current,
                'checksum' => $f->checksum,
            ]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'document.copied',
            'subject_type' => Document::class,
            'subject_id' => $new->id,
            'properties' => ['source_id' => $document->id],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('documents.show', $new)->with('status', 'Document copied.');
    }

    public function move(Document $document, Request $request): RedirectResponse
    {
        $this->authorize('update', $document);
        if ($document->locked_by && $document->locked_by !== auth()->id()) {
            return back()->withErrors(['document' => 'Document is locked for editing.']);
        }
        $data = $request->validate([
            'document_type_id' => ['nullable','exists:document_types,id'],
            'origin_unit_id' => ['nullable','exists:organization_units,id'],
            'classification_id' => ['nullable','exists:classifications,id'],
            'folder_id' => ['nullable','exists:folders,id'],
        ]);
        $document->fill(array_filter($data, fn($v) => !is_null($v)));
        $document->save();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'document.moved',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'properties' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return back()->with('status', 'Document updated.');
    }

    public function rename(Document $document, Request $request): RedirectResponse
    {
        $this->authorize('update', $document);
        if ($document->locked_by && $document->locked_by !== auth()->id()) {
            return back()->withErrors(['title' => 'Document is locked for editing.']);
        }
        $data = $request->validate([
            'title' => ['required','string','max:255'],
        ]);
        $document->update(['title' => $data['title']]);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'document.renamed',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'properties' => ['title' => $data['title']],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return back()->with('status', 'Document renamed.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);
        if ($document->legal_hold) {
            return back()->withErrors(['document' => 'Document is on legal hold and cannot be deleted.']);
        }
        if ($document->retention_until && now()->lt($document->retention_until) && !auth()->user()->isAdmin()) {
            return back()->withErrors(['document' => 'Retention policy prevents deletion until '.$document->retention_until]);
        }

        Storage::disk('public')->deleteDirectory("documents/{$document->id}");
        $id = $document->id;
        $document->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'document.deleted',
            'subject_type' => Document::class,
            'subject_id' => $id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('documents.index')->with('status', 'Document deleted.');
    }

    public function suggest(Request $request)
    {
        $this->authorize('viewAny', Document::class);
        $q = $request->string('q')->toString();
        $results = collect();
        if (config('scout.driver') && $q) {
            try {
                $results = Document::search($q)->take(10)->get()->map(function($d){
                    return ['id' => $d->id, 'doc_number' => $d->doc_number, 'title' => $d->title];
                });
            } catch (\Throwable $e) {}
        }
        if ($results->isEmpty() && $q) {
            $results = Document::query()
                ->select(['id','doc_number','title'])
                ->where(function($w) use ($q){
                    $w->where('doc_number', 'like', $q.'%')
                      ->orWhere('title', 'like', '%'.$q.'%');
                })
                ->orderBy('doc_number')
                ->limit(10)
                ->get();
        }
        return response()->json($results);
    }

    public function scan(Request $request)
    {
        $this->authorize('viewAny', Document::class);
        $code = $request->string('code')->toString();
        if ($code !== '') {
            $doc = Document::where('doc_number', $code)->first();
            if ($doc) return redirect()->route('documents.show', $doc);
            return redirect()->route('documents.index', ['q' => $code]);
        }
        return view('documents.scan');
    }
}
