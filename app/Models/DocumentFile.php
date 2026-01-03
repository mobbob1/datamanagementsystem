<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'disk',
        'path',
        'original_name',
        'mime',
        'size',
        'uploaded_by',
        'version',
        'is_current',
        'checksum',
    ];

    protected $casts = [
        'is_current' => 'boolean',
    ];

    public function document(): BelongsTo { return $this->belongsTo(Document::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
}
