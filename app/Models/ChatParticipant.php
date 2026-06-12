<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    protected $table = 'chat_participants';

    protected $fillable = [
        'conversation_id',
        'member_id',
        'role',
        'joined_at',
        'left_at',
        'last_read_at',
        'is_muted',
        'muted_until',
        'nickname',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'last_read_at' => 'datetime',
        'muted_until' => 'datetime',
        'is_muted' => 'boolean',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
