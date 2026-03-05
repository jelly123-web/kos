<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id','action','route_name','method','url','ip','user_agent','lat','lng'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
