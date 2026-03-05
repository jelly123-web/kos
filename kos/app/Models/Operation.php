<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteWithMeta;

class Operation extends Model
{
    use HasFactory, SoftDeleteWithMeta;

    protected $fillable = [
        'title',
        'description',
        'scheduled_at',
        'status',
        'reported',
        'created_by',
        'deleted','deleted_by','deleted_ip',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'reported' => 'boolean',
        ];
    }
}
