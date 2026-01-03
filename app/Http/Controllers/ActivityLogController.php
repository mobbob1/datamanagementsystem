<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('admin');

        $q = $request->string('q')->toString();
        $from = $request->input('from');
        $to = $request->input('to');
        $userId = $request->input('user');
        $action = $request->string('action')->toString();
        $documentId = $request->input('document');

        $logs = ActivityLog::with('user')
            ->when($q, function ($w) use ($q) {
                $w->where('subject_type', 'like', "%$q%")
                  ->orWhere('subject_id', 'like', "%$q%")
                  ->orWhere('ip_address', 'like', "%$q%")
                  ->orWhere('user_agent', 'like', "%$q%")
                  ->orWhere('action', 'like', "%$q%");
            })
            ->when($userId, fn($w) => $w->where('user_id', $userId))
            ->when($action, fn($w) => $w->where('action', $action))
            ->when($documentId, function($w) use ($documentId) {
                $w->where('subject_type', \App\Models\Document::class)->where('subject_id', $documentId);
            })
            ->when($from, fn($w) => $w->whereDate('created_at', '>=', $from))
            ->when($to, fn($w) => $w->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $distinctActions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('activity_logs.index', [
            'logs' => $logs,
            'distinctActions' => $distinctActions,
        ]);
    }

    public function export(Request $request)
    {
        $this->authorize('admin');

        $q = $request->string('q')->toString();
        $from = $request->input('from');
        $to = $request->input('to');
        $userId = $request->input('user');
        $action = $request->string('action')->toString();
        $documentId = $request->input('document');

        $query = ActivityLog::with('user')
            ->when($q, function ($w) use ($q) {
                $w->where('subject_type', 'like', "%$q%")
                  ->orWhere('subject_id', 'like', "%$q%")
                  ->orWhere('ip_address', 'like', "%$q%")
                  ->orWhere('user_agent', 'like', "%$q%")
                  ->orWhere('action', 'like', "%$q%");
            })
            ->when($userId, fn($w) => $w->where('user_id', $userId))
            ->when($action, fn($w) => $w->where('action', $action))
            ->when($documentId, function($w) use ($documentId) {
                $w->where('subject_type', \App\Models\Document::class)->where('subject_id', $documentId);
            })
            ->when($from, fn($w) => $w->whereDate('created_at', '>=', $from))
            ->when($to, fn($w) => $w->whereDate('created_at', '<=', $to))
            ->latest();

        $filename = 'activity-logs-'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['time','user_id','user_name','action','subject_type','subject_id','ip_address','user_agent']);
            $query->chunk(100, function($rows) use ($out) {
                foreach ($rows as $log) {
                    fputcsv($out, [
                        $log->created_at,
                        $log->user_id,
                        optional($log->user)->name,
                        $log->action,
                        $log->subject_type,
                        $log->subject_id,
                        $log->ip_address,
                        $log->user_agent,
                    ]);
                }
            });
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
