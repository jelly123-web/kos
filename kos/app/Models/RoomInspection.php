<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomInspection extends Model
{
    protected $fillable = [
        'room_id',
        'inspector_id',
        'type',
        'notes',
        'issue_report_id',
        'inspected_at',
    ];

    protected function casts(): array
    {
        return [
            'inspected_at' => 'datetime',
        ];
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function issue()
    {
        return $this->belongsTo(IssueReport::class, 'issue_report_id');
    }
}
