<?php

return [
    'enabled' => env('OCR_ENABLED', false),

    // local | api
    'driver' => env('OCR_DRIVER', 'local'),

    // Languages for OCR (comma-separated for tesseract)
    'languages' => env('OCR_LANGS', 'eng'),

    // Binaries for local mode
    'pdftotext' => env('OCR_PDFTOTEXT_BIN', 'pdftotext'),
    'tesseract' => env('OCR_TESSERACT_BIN', 'tesseract'),

    // Generic OCR API contract: POST multipart file under key "file", returns { text: "..." }
    'api' => [
        'endpoint' => env('OCR_API_ENDPOINT'),
        'key' => env('OCR_API_KEY'),
        'timeout' => env('OCR_API_TIMEOUT', 60),
    ],
];
