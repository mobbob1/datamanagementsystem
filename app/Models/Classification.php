<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'clearance_level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'clearance_level' => 'integer',
    ];
}
