<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteWithMeta;

class Payment extends Model
{
    use HasFactory, SoftDeleteWithMeta;

    protected $fillable = [
        'tenant_id',
        'room_id',
        'amount',
        'category',
        'due_date',
        'paid_at',
        'status',
        'proof_path',
        'deleted','deleted_by','deleted_ip',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'paid_at' => 'datetime',
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
}
