<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteWithMeta;

class Room extends Model
{
    use HasFactory, SoftDeleteWithMeta;

    protected $fillable = [
        'number',
        'name',
        'price',
        'status',
        'condition',
        'electricity_status',
        'water_status',
        'facilities',
        'property_id',
        'deleted','deleted_by','deleted_ip',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }
}
