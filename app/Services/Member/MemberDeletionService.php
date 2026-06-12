<?php

namespace App\Services\Member;

use Illuminate\Support\Facades\DB;

class MemberDeletionService
{
    public function purgeDependencies(int $memberId): void
    {
        if ($memberId <= 0) {
            return;
        }

        $loanIds = DB::table('loans')->where('member_id', $memberId)->pluck('id')->all();
        $applicationIds = DB::table('loan_applications')->where('member_id', $memberId)->pluck('id')->all();
        $shareIds = DB::table('shares')->where('member_id', $memberId)->pluck('id')->all();
        $dividendIds = DB::table('member_dividends')->where('member_id', $memberId)->pluck('id')->all();
        $investmentIds = DB::table('member_investments')->where('member_id', $memberId)->pluck('id')->all();
        $transactionIds = DB::table('transactions')->where('member_id', $memberId)->pluck('id')->all();
        $chatMessageIds = DB::table('chat_messages')->where('sender_id', $memberId)->pluck('id')->all();
        $documentIds = DB::table('documents')->where('member_id', $memberId)->pluck('id')->all();

        // Break references that point to rows we are about to remove.
        DB::table('members')->where('referred_by', $memberId)->update(['referred_by' => null]);
        DB::table('projects')->where('project_manager_id', $memberId)->update(['project_manager_id' => null]);
        DB::table('projects')->where('supervisor_id', $memberId)->update(['supervisor_id' => null]);
        DB::table('project_risks')->where('owner_id', $memberId)->update(['owner_id' => null]);
        DB::table('project_milestones')->where('completed_by', $memberId)->update(['completed_by' => null]);
        DB::table('member_groups')->where('leader_id', $memberId)->update(['leader_id' => null]);
        DB::table('investment_opportunities')->where('fund_manager_id', $memberId)->update(['fund_manager_id' => null]);
        DB::table('loan_default_notices')->where('response_by', $memberId)->update(['response_by' => null]);
        DB::table('meeting_action_items')->where('assigned_to', $memberId)->update(['assigned_to' => null]);

        DB::table('transactions')->where('related_transfer_to_member_id', $memberId)->update(['related_transfer_to_member_id' => null]);
        DB::table('transactions')->whereIn('related_loan_id', $loanIds)->update(['related_loan_id' => null]);
        DB::table('transactions')->whereIn('related_share_id', $shareIds)->update(['related_share_id' => null]);
        DB::table('transactions')->whereIn('related_dividend_id', $dividendIds)->update(['related_dividend_id' => null]);
        DB::table('transactions')->whereIn('related_investment_id', $investmentIds)->update(['related_investment_id' => null]);
        DB::table('transactions')->whereIn('parent_transaction_id', $transactionIds)->update(['parent_transaction_id' => null]);
        DB::table('loan_applications')->whereIn('converted_to_loan_id', $loanIds)->update(['converted_to_loan_id' => null]);
        DB::table('loans')->where('guarantor1_id', $memberId)->update(['guarantor1_id' => null]);
        DB::table('loans')->where('guarantor2_id', $memberId)->update(['guarantor2_id' => null]);
        DB::table('documents')->whereIn('previous_version_id', $documentIds)->update(['previous_version_id' => null]);
        DB::table('fundraising_expenses')->whereIn('receipt_document_id', $documentIds)->update(['receipt_document_id' => null]);
        DB::table('investment_opportunities')->whereIn('prospectus_document_id', $documentIds)->update(['prospectus_document_id' => null]);
        DB::table('loan_default_notices')->whereIn('document_id', $documentIds)->update(['document_id' => null]);
        DB::table('loan_guarantor_agreements')->whereIn('agreement_document_id', $documentIds)->update(['agreement_document_id' => null]);
        DB::table('meetings')->whereIn('minutes_document_id', $documentIds)->update(['minutes_document_id' => null]);
        DB::table('projects')->whereIn('proposal_document_id', $documentIds)->update(['proposal_document_id' => null]);
        DB::table('projects')->whereIn('contract_document_id', $documentIds)->update(['contract_document_id' => null]);
        DB::table('share_transfers')->whereIn('transfer_document_id', $documentIds)->update(['transfer_document_id' => null]);

        DB::table('chat_messages')->whereIn('reply_to_id', $chatMessageIds)->update(['reply_to_id' => null]);
        DB::table('chat_messages')->whereIn('forwarded_from_id', $chatMessageIds)->update(['forwarded_from_id' => null]);

        // Remove rows that cannot survive without the member.
        DB::table('share_transfers')->where('from_member_id', $memberId)->orWhere('to_member_id', $memberId)->delete();
        DB::table('loan_guarantor_agreements')->where('member_id', $memberId)->delete();
        DB::table('project_team')->where('member_id', $memberId)->delete();
        DB::table('fundraising_contributions')->where('member_id', $memberId)->delete();
        DB::table('member_dividends')->where('member_id', $memberId)->delete();
        DB::table('member_investments')->where('member_id', $memberId)->delete();
        DB::table('share_purchases')->where('member_id', $memberId)->delete();
        DB::table('shares')->where('sold_to_member_id', $memberId)->update(['sold_to_member_id' => null]);
        DB::table('shares')->where('member_id', $memberId)->delete();
        DB::table('fundraising_campaigns')->where('organizer_id', $memberId)->delete();

        DB::table('loans')->where('member_id', $memberId)->delete();
        DB::table('loan_applications')->where('member_id', $memberId)->delete();

        DB::table('savings_accounts')->where('member_id', $memberId)->delete();
        DB::table('transactions')->where('member_id', $memberId)->delete();
        DB::table('chat_messages')->where('sender_id', $memberId)->delete();
    }
}
