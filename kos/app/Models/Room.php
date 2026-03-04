<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

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
