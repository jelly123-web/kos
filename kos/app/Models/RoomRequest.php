<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteWithMeta;

class RoomRequest extends Model
{
    use SoftDeleteWithMeta;
    protected $fillable = [
        'tenant_id',
        'room_id',
        'status',
        'deleted','deleted_by','deleted_ip',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
