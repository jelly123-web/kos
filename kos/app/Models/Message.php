<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SoftDeleteWithMeta;

class Message extends Model
{
    use SoftDeleteWithMeta;
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
        'deleted','deleted_by','deleted_ip',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
