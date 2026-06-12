<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';

    protected $fillable = [
        'message_number',
        'conversation_id',
        'sender_id',
        'message',
        'message_type',
        'attachment_path',
        'attachment_name',
        'attachment_type',
        'attachment_size',
        'thumbnail_path',
        'latitude',
        'longitude',
        'location_name',
        'reply_to_id',
        'forwarded_from_id',
        'forwarded_count',
        'is_delivered',
        'delivered_at',
        'is_read',
        'read_at',
        'read_count',
        'is_edited',
        'edited_at',
        'is_deleted',
        'deleted_at',
        'deleted_by',
        'delete_reason',
    ];

    protected $casts = [
        'is_delivered' => 'boolean',
        'is_read' => 'boolean',
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ChatMessage $message): void {
            if (!empty($message->message_number)) {
                return;
            }

            do {
                $message->message_number = 'MSG-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            } while (self::where('message_number', $message->message_number)->exists());
        });
    }

    public function sender()
    {
        return $this->belongsTo(Member::class, 'sender_id');
    }
}
