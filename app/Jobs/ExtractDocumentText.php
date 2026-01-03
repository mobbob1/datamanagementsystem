<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ExtractDocumentText implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $fileId;

    public function __construct(int $fileId)
    {
        $this->fileId = $fileId;
    }

    public function handle(): void
    {
        if (!config('ocr.enabled')) {
            return;
        }

        $file = DocumentFile::find($this->fileId);
        if (!$file) return;
        $document = $file->document;
        if (!$document) return;

        $path = Storage::disk($file->disk)->path($file->path);
        if (!is_file($path)) return;

        $mime = $file->mime ?: mime_content_type($path);
        $text = '';

        $driver = config('ocr.driver', 'local');
        try {
            if ($driver === 'api') {
                $endpoint = config('ocr.api.endpoint');
                if ($endpoint) {
                    $headers = [];
                    if ($key = config('ocr.api.key')) {
                        $headers['Authorization'] = 'Bearer ' . $key;
                    }
                    $resp = Http::timeout((int) config('ocr.api.timeout', 60))
                        ->withHeaders($headers)
                        ->attach('file', fopen($path, 'r'), basename($path))
                        ->post($endpoint);
                    if ($resp->successful()) {
                        $json = $resp->json();
                        if (is_array($json) && isset($json['text'])) {
                            $text = (string) $json['text'];
                        }
                    }
                }
            } else {
                $langs = escapeshellarg((string) config('ocr.languages', 'eng'));
                if (str_contains(strtolower((string)$mime), 'pdf') || str_ends_with(strtolower($path), '.pdf')) {
                    $bin = (string) config('ocr.pdftotext', 'pdftotext');
                    $cmd = escapeshellcmd($bin) . ' -layout -nopgbrk ' . escapeshellarg($path) . ' -';
                    $out = @shell_exec($cmd);
                    if (is_string($out)) {
                        $text = $out;
                    }
                } else {
                    $bin = (string) config('ocr.tesseract', 'tesseract');
                    $cmd = escapeshellcmd($bin) . ' ' . escapeshellarg($path) . ' stdout -l ' . trim($langs, "'");
                    $out = @shell_exec($cmd);
                    if (is_string($out)) {
                        $text = $out;
                    }
                }
            }
        } catch (\Throwable $e) {
            // swallow errors; environment may not have tools installed yet
        }

        if ($text) {
            $document->search_text = trim((string)$document->search_text . "\n" . $text);
            $document->save();
            try {
                // Update search index if Scout is configured
                if (method_exists($document, 'searchable')) {
                    $document->searchable();
                }
            } catch (\Throwable $e) {
                // ignore indexing errors
            }
        }
    }
}
