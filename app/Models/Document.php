<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Document extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'doc_number',
        'title',
        'document_type_id',
        'classification_id',
        'origin_unit_id',
        'folder_id',
        'workflow_definition_id',
        'current_position',
        'status',
        'retention_policy_id',
        'retention_until',
        'legal_hold',
        'search_text',
        'created_by',
        'updated_by',
        'locked_by',
        'locked_at',
    ];

    protected $casts = [
        'legal_hold' => 'boolean',
        'retention_until' => 'date',
        'locked_at' => 'datetime',
        'archived_at' => 'datetime',
        'disposed_at' => 'datetime',
    ];

    public function type(): BelongsTo { return $this->belongsTo(DocumentType::class, 'document_type_id'); }
    public function classification(): BelongsTo { return $this->belongsTo(Classification::class); }
    public function originUnit(): BelongsTo { return $this->belongsTo(OrganizationUnit::class, 'origin_unit_id'); }
    public function folder(): BelongsTo { return $this->belongsTo(Folder::class); }
    public function files(): HasMany { return $this->hasMany(DocumentFile::class); }
    public function permissions(): HasMany { return $this->hasMany(DocumentPermission::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function locker(): BelongsTo { return $this->belongsTo(User::class, 'locked_by'); }

    public function nextVersion(): int
    {
        return (int) ($this->files()->max('version') ?? 0) + 1;
    }

    public function toSearchableArray(): array
    {
        $this->loadMissing(['classification', 'type', 'originUnit']);
        return [
            'id' => $this->id,
            'doc_number' => $this->doc_number,
            'title' => $this->title,
            'status' => $this->status,
            'classification_id' => $this->classification_id,
            'classification' => optional($this->classification)->name,
            'document_type_id' => $this->document_type_id,
            'type' => optional($this->type)->name,
            'origin_unit_id' => $this->origin_unit_id,
            'origin_unit' => optional($this->originUnit)->name,
            'search_text' => (string) $this->search_text,
            'created_by' => $this->created_by,
            'created_at' => optional($this->created_at)->toAtomString(),
        ];
    }
}
