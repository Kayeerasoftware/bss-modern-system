<?php

namespace App\Http\Controllers;

use App\Models\FundraisingContribution;
use App\Models\Fundraising;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use App\Models\Currency;
use Illuminate\Http\Request;

class FundraisingContributionController extends Controller
{
    public function index($fundraisingId = null)
    {
        $query = FundraisingContribution::with('fundraising');
        
        if ($fundraisingId) {
            $query->where('campaign_id', $fundraisingId);
        }
        
        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fundraising_id' => 'nullable|exists:fundraising_campaigns,id',
            'campaign_id' => 'nullable|exists:fundraising_campaigns,id',
            'member_id' => 'nullable|integer',
            'contributor_name' => 'required|string',
            'contributor_email' => 'nullable|email',
            'contributor_phone' => 'nullable|string',
            'contributor_address' => 'nullable|string',
            'is_anonymous' => 'nullable|boolean',
            'amount' => 'required|numeric|min:100',
            'contribution_date' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'receipt_number' => 'nullable|string',
            'receipt_issued' => 'nullable|boolean',
            'receipt_issued_at' => 'nullable|date',
            'receipt_issued_by' => 'nullable|exists:users,id',
            'thank_you_sent' => 'nullable|boolean',
            'thank_you_sent_at' => 'nullable|date',
            'message' => 'nullable|string',
            'is_public_message' => 'nullable|boolean',
            'notes' => 'nullable|string'
        ]);

        $campaignId = $validated['campaign_id'] ?? $validated['fundraising_id'] ?? null;
        if (!$campaignId) {
            return response()->json(['success' => false, 'message' => 'Campaign is required.'], 422);
        }

        $memberId = $validated['member_id'] ?? null;
        if (!$memberId && !empty($validated['contributor_email'])) {
            $memberId = Member::query()->where('email', $validated['contributor_email'])->value('id');
        }
        if (!$memberId && !empty($validated['contributor_phone'])) {
            $memberId = Member::query()->where('primary_phone', $validated['contributor_phone'])->value('id');
        }

        if (!$memberId) {
            $memberId = Member::query()->value('id');
        }

        $transactionTypeId = TransactionType::query()->where('name', 'deposit')->value('id')
            ?? TransactionType::query()->value('id');
        $categoryId = TransactionCategory::query()->where('name', 'fundraising_deposit')->value('id');
        if (!$categoryId) {
            return response()->json([
                'success' => false,
                'message' => 'Fundraising deposit category is not configured.',
            ], 422);
        }
        $statusId = TransactionStatus::query()->where('name', 'completed')->value('id')
            ?? TransactionStatus::query()->value('id');
        $currencyId = Currency::query()->where('code', 'UGX')->value('id') ?? Currency::query()->value('id');

        $member = $memberId ? Member::find($memberId) : null;
        $balanceBefore = (float) ($member?->balance ?? 0);
        $netAmount = (float) $validated['amount'];
        $impact = TransactionType::query()->whereKey($transactionTypeId)->value('impact');
        $balanceAfter = $impact === 'debit'
            ? $balanceBefore - $netAmount
            : $balanceBefore + $netAmount;

        $transaction = Transaction::create([
            'member_id' => $memberId,
            'transaction_type_id' => $transactionTypeId,
            'category_id' => $categoryId,
            'status_id' => $statusId,
            'amount' => $validated['amount'],
            'net_amount' => $netAmount,
            'currency_id' => $currencyId,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'payment_method_id' => $validated['payment_method_id'],
            'transaction_date' => $validated['contribution_date'],
            'value_date' => $validated['contribution_date'],
            'description' => 'Fundraising contribution',
            'notes' => $validated['notes'] ?? null,
            'processed_by' => auth()->id() ?? \App\Models\User::query()->value('id'),
            'processed_at' => now(),
            'processed_ip' => $request->ip(),
        ]);

        $lastContribution = FundraisingContribution::latest('id')->value('contribution_number');
        $nextNumber = 1;
        if ($lastContribution && preg_match('/(\d+)$/', $lastContribution, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }
        $validated['contribution_number'] = 'CTB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $validated['campaign_id'] = $campaignId;
        $validated['transaction_id'] = $transaction->id;
        $validated['member_id'] = $memberId;

        $contribution = FundraisingContribution::create([
            'contribution_number' => $validated['contribution_number'],
            'campaign_id' => $validated['campaign_id'],
            'transaction_id' => $validated['transaction_id'],
            'member_id' => $validated['member_id'],
            'contributor_name' => $validated['contributor_name'],
            'contributor_email' => $validated['contributor_email'] ?? null,
            'contributor_phone' => $validated['contributor_phone'] ?? null,
            'contributor_address' => $validated['contributor_address'] ?? null,
            'is_anonymous' => !empty($validated['is_anonymous']),
            'amount' => $validated['amount'],
            'contribution_date' => $validated['contribution_date'],
            'payment_method_id' => $validated['payment_method_id'],
            'receipt_number' => $validated['receipt_number'] ?? null,
            'receipt_issued' => !empty($validated['receipt_issued']),
            'receipt_issued_at' => $validated['receipt_issued_at'] ?? null,
            'receipt_issued_by' => $validated['receipt_issued_by'] ?? null,
            'thank_you_sent' => !empty($validated['thank_you_sent']),
            'thank_you_sent_at' => $validated['thank_you_sent_at'] ?? null,
            'message' => $validated['message'] ?? null,
            'is_public_message' => !empty($validated['is_public_message']),
            'notes' => $validated['notes'] ?? null,
        ]);

        $fundraising = Fundraising::find($campaignId);
        $fundraising->raised_amount += $validated['amount'];
        $fundraising->save();

        return response()->json(['success' => true, 'contribution' => $contribution]);
    }

    public function destroy($id)
    {
        $contribution = FundraisingContribution::findOrFail($id);
        $fundraising = $contribution->fundraising;
        $fundraising->raised_amount -= $contribution->amount;
        $fundraising->save();
        
        $transaction = $contribution->transaction;
        $contribution->delete();
        if ($transaction) {
            $transaction->delete();
        }
        return response()->json(['success' => true]);
    }
}
