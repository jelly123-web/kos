<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExitRequest extends Model
{
    protected $fillable = [
        'tenant_id',
        'reason',
        'status',
        'approved_at',
        'rejected_at',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}

