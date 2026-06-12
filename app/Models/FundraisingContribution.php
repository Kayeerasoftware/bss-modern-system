<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\User;

class FundraisingContribution extends Model
{
    const UPDATED_AT = null;
    protected $fillable = [
        'contribution_number',
        'campaign_id',
        'transaction_id',
        'member_id',
        'contributor_name',
        'contributor_email',
        'contributor_phone',
        'contributor_address',
        'is_anonymous',
        'amount',
        'contribution_date',
        'payment_method_id',
        'receipt_number',
        'receipt_issued',
        'receipt_issued_at',
        'receipt_issued_by',
        'thank_you_sent',
        'thank_you_sent_at',
        'message',
        'is_public_message',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'contribution_date' => 'date',
        'receipt_issued' => 'boolean',
        'receipt_issued_at' => 'datetime',
        'thank_you_sent' => 'boolean',
        'thank_you_sent_at' => 'datetime',
        'is_anonymous' => 'boolean',
        'is_public_message' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function fundraising()
    {
        return $this->belongsTo(Fundraising::class, 'campaign_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function receiptIssuer()
    {
        return $this->belongsTo(User::class, 'receipt_issued_by');
    }
}
