<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Member;

class Transaction extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['transaction_id', 'member_id', 'amount', 'type', 'description', 'category', 'fee', 'tax_amount', 'commission', 'currency', 'payment_method', 'channel', 'priority', 'reference', 'receipt_number', 'batch_id', 'location', 'transaction_date', 'scheduled_at', 'notes', 'status', 'processed_by', 'completed_at', 'balance_before', 'balance_after', 'net_amount', 'notification_sent', 'notification_sent_at', 'ip_address', 'device_info'];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'processed_by');
    }
}
