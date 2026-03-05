<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteWithMeta;

class Tenant extends Model
{
    use HasFactory, SoftDeleteWithMeta;

    protected $fillable = [
        'name',
        'phone',
        'user_id',
        'room_id',
        'status',
        'start_date',
        'end_date',
        'deleted','deleted_by','deleted_ip',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
