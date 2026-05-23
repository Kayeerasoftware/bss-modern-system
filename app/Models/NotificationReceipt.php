<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationReceipt extends Model
{
    protected $table = 'notification_receipts';

    protected $fillable = [
        'notification_id',
        'member_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
