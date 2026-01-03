<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Classification;
use App\Models\DocumentType;
use App\Models\OrganizationUnit;
use App\Models\ActivityLog;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(Request $request): View
    {
        // Allow non-admins to access, but scope their data
        $user = $request->user();

        $type = $request->string('type')->toString() ?: 'documents';
        $from = $request->string('from')->toString();
        $to = $request->string('to')->toString();

        $stats = [
            'documents_total' => $this->documentsBaseQuery($user, null, null)->count(),
            'documents_archived' => $this->documentsBaseQuery($user, null, null)->whereNotNull('archived_at')->count(),
            'documents_disposed' => $this->documentsBaseQuery($user, null, null)->whereNotNull('disposed_at')->count(),
            'documents_legal_hold' => $this->documentsBaseQuery($user, null, null)->where('legal_hold', true)->count(),
            'documents_last_7_days' => $this->documentsBaseQuery($user, now()->subDays(7)->toDateString(), null)->count(),
            'workflows_running' => $this->workflowsBaseQuery($user)->where('status', 'running')->count(),
            'workflows_completed' => $this->workflowsBaseQuery($user)->where('status', 'completed')->count(),
        ];

        [$columns, $rows] = $this->buildDataset($type, $from, $to, $user);

        return view('reports.index', [
            'stats' => $stats,
            'type' => $type,
            'from' => $from,
            'to' => $to,
            'columns' => $columns,
            'rows' => $rows,
        ]);
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $type = $request->string('type')->toString() ?: 'documents';
        $from = $request->string('from')->toString();
        $to = $request->string('to')->toString();

        [$columns, $rows] = $this->buildDataset($type, $from, $to, $user, export: true);

        $filename = 'report_'.$type.'_'.now()->format('Ymd_His').'.csv';
        return response()->streamDownload(function() use ($columns, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($rows as $r) {
                fputcsv($out, array_map(function($v){ return is_array($v) || is_object($v) ? json_encode($v) : $v; }, $r));
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function buildDataset(string $type, ?string $from, ?string $to, $user, bool $export = false): array
    {
        switch ($type) {
            case 'activity_logs':
                $query = ActivityLog::query()->with('user')->latest();
                // Non-admins see only their own activity
                if (!$user->isAdmin()) {
                    $query->where('user_id', $user->id);
                }
                if ($from) { $query->whereDate('created_at', '>=', $from); }
                if ($to) { $query->whereDate('created_at', '<=', $to); }
                $logs = $export ? $query->get() : $query->limit(500)->get();
                $columns = ['Date', 'User', 'Action', 'Subject Type', 'Subject ID', 'IP Address'];
                $rows = $logs->map(function($l){
                    return [
                        optional($l->created_at)->toDateTimeString(),
                        optional($l->user)->name,
                        $l->action,
                        $l->subject_type,
                        $l->subject_id,
                        $l->ip_address,
                    ];
                })->all();
                return [$columns, $rows];

            case 'workflows':
                $query = $this->workflowsBaseQuery($user)->with('currentStep');
                if ($from) { $query->whereDate('updated_at', '>=', $from); }
                if ($to) { $query->whereDate('updated_at', '<=', $to); }
                $items = $export ? $query->get() : $query->limit(500)->get();
                $columns = ['Document ID', 'Doc Number', 'Workflow', 'Status', 'Current Step', 'Updated At'];
                $docIds = $items->pluck('document_id')->unique()->values();
                $docs = Document::whereIn('id', $docIds)->get(['id','doc_number']);
                $docsMap = $docs->keyBy('id');
                $rows = $items->map(function($w) use ($docsMap) {
                    return [
                        $w->document_id,
                        optional($docsMap->get($w->document_id))->doc_number,
                        $w->workflow_definition_id,
                        $w->status,
                        optional($w->currentStep)->name,
                        optional($w->updated_at)->toDateTimeString(),
                    ];
                })->all();
                return [$columns, $rows];

            case 'documents':
            default:
                $query = $this->documentsBaseQuery($user, $from, $to)
                    ->with(['type','classification','originUnit'])
                    ->latest();
                $docs = $export ? $query->get() : $query->limit(500)->get();
                $columns = ['Doc Number','Title','Type','Classification','Unit','Status','Created','Retention Until','Archived At'];
                $rows = $docs->map(function($d){
                    return [
                        $d->doc_number,
                        $d->title,
                        optional($d->type)->name,
                        optional($d->classification)->name,
                        optional($d->originUnit)->name,
                        $d->status,
                        optional($d->created_at)->toDateTimeString(),
                        optional($d->retention_until)?->toDateString(),
                        optional($d->archived_at)?->toDateString(),
                    ];
                })->all();
                return [$columns, $rows];
        }
    }

    private function documentsBaseQuery($user, ?string $from, ?string $to)
    {
        $q = Document::query();
        // Clearance + ACL scoping similar to listing
        if (!$user->isAdmin() && optional($user->role)->key !== 'registrar') {
            $q->whereHas('classification', function($w) use ($user) {
                $w->where('clearance_level', '<=', $user->clearance_level);
            });
            $q->where(function($w) use ($user) {
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
        if ($from) { $q->whereDate('created_at', '>=', $from); }
        if ($to) { $q->whereDate('created_at', '<=', $to); }
        return $q;
    }

    private function workflowsBaseQuery($user)
    {
        $q = WorkflowInstance::query();
        if (!$user->isAdmin() && optional($user->role)->key !== 'registrar') {
            // Limit to workflows on documents the user can likely see (created_by or same unit)
            $q->whereHas('document', function($d) use ($user) {
                $d->where('created_by', $user->id);
                if ($user->organization_unit_id) {
                    $d->orWhere('origin_unit_id', $user->organization_unit_id);
                }
                $d->orWhereHas('permissions', function($p) use ($user) {
                    $p->where('user_id', $user->id)->where('can_view', true);
                });
            });
        }
        return $q;
    }
}
