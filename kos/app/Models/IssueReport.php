<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteWithMeta;

class IssueReport extends Model
{
    use SoftDeleteWithMeta;
    protected $fillable = [
        'tenant_id',
        'room_id',
        'title',
        'description',
        'status',
        'assigned_to',
        'reported_at',
        'deleted','deleted_by','deleted_ip',
    ];

    protected function casts(): array
    {
        return [
            'reported_at' => 'datetime',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
