<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'sender_id', 'receiver_id', 'message', 'is_read', 'attachment'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime'
    ];

    public function sender()
    {
        return $this->belongsTo(Member::class, 'sender_id', 'member_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Member::class, 'receiver_id', 'member_id');
    }
}
