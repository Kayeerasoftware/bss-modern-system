<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$amountSql = 'COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)';
$accountsBalancesSub = DB::table('savings_accounts')
    ->selectRaw('member_id, SUM(current_balance) as balance')
    ->groupBy('member_id');
$derivedBalancesSub = DB::table('transactions')
    ->join('transaction_types as tt','transactions.transaction_type_id','=','tt.id')
    ->join('transaction_statuses as ts','transactions.status_id','=','ts.id')
    ->join('transaction_categories as tc','transactions.category_id','=','tc.id')
    ->where('ts.name','completed')
    ->whereIn('tc.name',['savings_deposit','savings_withdrawal'])
    ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN $amountSql ELSE -$amountSql END), 0) as balance")
    ->groupBy('transactions.member_id');
$rows = DB::table('members')
    ->leftJoinSub($accountsBalancesSub,'acc_balances','members.id','=','acc_balances.member_id')
    ->leftJoinSub($derivedBalancesSub,'txn_balances','members.id','=','txn_balances.member_id')
    ->select('members.id','members.full_name',DB::raw('COALESCE(acc_balances.balance, txn_balances.balance, 0) as balance'))
    ->whereRaw('COALESCE(acc_balances.balance, txn_balances.balance, 0) > 0')
    ->orderByDesc('balance')
    ->get();
foreach($rows as $r){echo $r->id.":".$r->balance."\n";}
