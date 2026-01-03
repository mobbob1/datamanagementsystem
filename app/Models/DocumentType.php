<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'default_classification_id',
        'default_retention_months',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_retention_months' => 'integer',
    ];
}
