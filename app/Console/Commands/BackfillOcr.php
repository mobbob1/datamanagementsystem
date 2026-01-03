<?php

namespace App\Console\Commands;

use App\Jobs\ExtractDocumentText;
use App\Models\DocumentFile;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BackfillOcr extends Command
{
    protected $signature = 'ocr:backfill {--all : Process all files} {--since= : Process files uploaded since date (Y-m-d)} {--only-current : Only process current versions}';

    protected $description = 'Queue OCR extraction for existing files to backfill search_text';

    public function handle(): int
    {
        $query = DocumentFile::query();

        if ($this->option('only-current')) {
            $query->where('is_current', true);
        }

        if (!$this->option('all')) {
            // Default: only files where document has empty or null search_text
            $query->whereHas('document', function($w) {
                $w->whereNull('search_text')->orWhere('search_text', '');
            });
        }

        if ($since = $this->option('since')) {
            try {
                $dt = Carbon::createFromFormat('Y-m-d', $since)->startOfDay();
                $query->where('created_at', '>=', $dt);
            } catch (\Throwable $e) {
                $this->error('Invalid --since date, expected Y-m-d');
                return self::INVALID;
            }
        }

        $count = $query->count();
        if ($count === 0) {
            $this->info('No files matched criteria.');
            return self::SUCCESS;
        }

        $this->info("Dispatching OCR jobs for {$count} files...");
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->orderBy('id')->chunk(200, function($chunk) use ($bar) {
            foreach ($chunk as $file) {
                ExtractDocumentText::dispatch($file->id);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info('Done. Jobs queued.');
        return self::SUCCESS;
    }
}
