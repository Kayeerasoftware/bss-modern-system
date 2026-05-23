<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'notification_number',
        'type_id',
        'member_id',
        'title',
        'message',
        'action_url',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function receipts()
    {
        return $this->hasMany(NotificationReceipt::class, 'notification_id');
    }
}
