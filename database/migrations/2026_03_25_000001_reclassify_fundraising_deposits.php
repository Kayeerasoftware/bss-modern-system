<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $fundraisingCategoryId = DB::table('transaction_categories')
            ->where('name', 'fundraising_deposit')
            ->value('id');
        $savingsCategoryId = DB::table('transaction_categories')
            ->where('name', 'savings_deposit')
            ->value('id');

        if (!$fundraisingCategoryId || !$savingsCategoryId) {
            return;
        }

        DB::statement(
            'UPDATE transactions t
                JOIN fundraising_contributions fc ON fc.transaction_id = t.id
             SET t.category_id = ?
             WHERE t.category_id = ?',
            [$fundraisingCategoryId, $savingsCategoryId]
        );
    }

    public function down(): void
    {
        $fundraisingCategoryId = DB::table('transaction_categories')
            ->where('name', 'fundraising_deposit')
            ->value('id');
        $savingsCategoryId = DB::table('transaction_categories')
            ->where('name', 'savings_deposit')
            ->value('id');

        if (!$fundraisingCategoryId || !$savingsCategoryId) {
            return;
        }

        DB::statement(
            'UPDATE transactions t
                JOIN fundraising_contributions fc ON fc.transaction_id = t.id
             SET t.category_id = ?
             WHERE t.category_id = ?',
            [$savingsCategoryId, $fundraisingCategoryId]
        );
    }
};
