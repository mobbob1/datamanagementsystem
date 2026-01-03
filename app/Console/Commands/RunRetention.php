<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RunRetention extends Command
{
    protected $signature = 'retention:run {--archive} {--delete}';

    protected $description = 'Run retention automation: auto-archive and/or auto-delete expired documents (skips legal hold)';

    public function handle(): int
    {
        $archive = (bool) $this->option('archive');
        $delete = (bool) $this->option('delete');

        if (!$archive && !$delete) {
            $this->warn('Specify --archive and/or --delete');
            return self::INVALID;
        }

        if ($archive) {
            $count = 0;
            DB::transaction(function () use (&$count) {
                $docs = Document::whereNull('archived_at')
                    ->whereNotNull('retention_until')
                    ->where('retention_until', '<=', now())
                    ->where(function($q){ $q->where('legal_hold', false)->orWhereNull('legal_hold'); })
                    ->lockForUpdate()
                    ->get();
                foreach ($docs as $doc) {
                    $doc->archived_at = now();
                    $doc->status = 'archived';
                    $doc->save();
                    ActivityLog::create([
                        'user_id' => null,
                        'action' => 'document.auto_archived',
                        'subject_type' => Document::class,
                        'subject_id' => $doc->id,
                        'ip_address' => null,
                        'user_agent' => 'scheduler',
                    ]);
                    $count++;
                }
            });
            $this->info("Auto-archived {$count} documents.");
        }

        if ($delete) {
            $count = 0;
            DB::transaction(function () use (&$count) {
                $docs = Document::whereNull('disposed_at')
                    ->whereNotNull('retention_until')
                    ->where('retention_until', '<=', now())
                    ->where(function($q){ $q->where('legal_hold', false)->orWhereNull('legal_hold'); })
                    ->lockForUpdate()
                    ->get();
                foreach ($docs as $doc) {
                    // Physically delete files, mark disposed
                    Storage::disk('public')->deleteDirectory("documents/{$doc->id}");
                    $doc->disposed_at = now();
                    $doc->status = 'disposed';
                    $doc->save();
                    ActivityLog::create([
                        'user_id' => null,
                        'action' => 'document.auto_deleted',
                        'subject_type' => Document::class,
                        'subject_id' => $doc->id,
                        'ip_address' => null,
                        'user_agent' => 'scheduler',
                    ]);
                    $count++;
                }
            });
            $this->info("Auto-deleted {$count} documents.");
        }

        return self::SUCCESS;
    }
}
