<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteWithMeta;

class ExitRequest extends Model
{
    use SoftDeleteWithMeta;
    protected $fillable = [
        'tenant_id',
        'reason',
        'status',
        'approved_at',
        'rejected_at',
        'deleted','deleted_by','deleted_ip',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
