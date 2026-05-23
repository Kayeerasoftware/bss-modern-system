<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::statement('DROP VIEW IF EXISTS `member_category_balances`');

        if (Schema::hasTable('member_category_balances')) {
            Schema::drop('member_category_balances');
        }

        DB::unprepared(<<<'SQL'
CREATE VIEW member_category_balances AS
SELECT
    t.member_id,
    t.category_id,
    COALESCE(SUM(CASE WHEN tt.impact = 'debit' THEN -amount_sql.amount_value ELSE amount_sql.amount_value END), 0) AS balance,
    COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN amount_sql.amount_value ELSE 0 END), 0) AS total_in,
    COALESCE(SUM(CASE WHEN tt.impact = 'debit' THEN amount_sql.amount_value ELSE 0 END), 0) AS total_out,
    MAX(t.id) AS last_transaction_id
FROM transactions t
JOIN transaction_types tt ON t.transaction_type_id = tt.id
JOIN transaction_statuses ts ON t.status_id = ts.id
JOIN (
    SELECT
        id,
        COALESCE(NULLIF(net_amount, 0), amount, 0) AS amount_value
    FROM transactions
) AS amount_sql ON amount_sql.id = t.id
WHERE ts.name = 'completed'
  AND t.deleted_at IS NULL
GROUP BY t.member_id, t.category_id
SQL);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::statement('DROP VIEW IF EXISTS `member_category_balances`');

        if (!Schema::hasTable('member_category_balances')) {
            Schema::create('member_category_balances', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('member_id');
                $table->unsignedTinyInteger('category_id');
                $table->decimal('balance', 15, 2)->default(0);
                $table->decimal('total_in', 15, 2)->default(0);
                $table->decimal('total_out', 15, 2)->default(0);
                $table->unsignedBigInteger('last_transaction_id')->nullable();
                $table->timestamps();

                $table->unique(['member_id', 'category_id'], 'uniq_member_category_balance');
                $table->index(['category_id', 'member_id'], 'idx_member_category_balance_category');
                $table->foreign('member_id')->references('id')->on('members');
                $table->foreign('category_id')->references('id')->on('transaction_categories');
                $table->foreign('last_transaction_id')->references('id')->on('transactions');
            });
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
