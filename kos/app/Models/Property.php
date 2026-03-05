<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteWithMeta;

class Property extends Model
{
    use HasFactory, SoftDeleteWithMeta;

    protected $fillable = [
        'name',
        'address',
        'default_room_price',
        'facilities',
        'deleted','deleted_by','deleted_ip',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
