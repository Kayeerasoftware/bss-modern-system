<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $member = App\Models\Member::first();
    if ($member) {
        echo "Testing delete for: {$member->member_id}\n";
        $mid = $member->member_id;
        
        DB::table('loans')->where('member_id', $mid)->delete();
        DB::table('transactions')->where('member_id', $mid)->delete();
        DB::table('shares')->where('member_id', $mid)->delete();
        DB::table('savings_history')->where('member_id', $mid)->delete();
        DB::table('dividends')->where('member_id', $mid)->delete();
        DB::table('notifications')->where('member_id', $mid)->delete();
        DB::table('chat_messages')->where('sender_id', $mid)->orWhere('receiver_id', $mid)->delete();
        
        $member->delete();
        echo "Success!\n";
    } else {
        echo "No members found\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
