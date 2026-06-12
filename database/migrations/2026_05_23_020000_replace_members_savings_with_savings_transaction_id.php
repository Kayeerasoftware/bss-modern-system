<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('members', 'savings_transaction_id')) {
            Schema::table('members', function (Blueprint $table): void {
                $table->unsignedBigInteger('savings_transaction_id')->nullable()->after('membership_status');
            });
        }

        if (Schema::hasTable('savings_transactions')) {
            DB::statement(<<<'SQL'
UPDATE members m
SET savings_transaction_id = (
    SELECT st.id
    FROM savings_accounts sa
    INNER JOIN savings_transactions st ON st.savings_account_id = sa.id
    WHERE sa.member_id = m.id
    ORDER BY st.id DESC
    LIMIT 1
)
WHERE EXISTS (
    SELECT 1
    FROM savings_accounts sa
    INNER JOIN savings_transactions st ON st.savings_account_id = sa.id
    WHERE sa.member_id = m.id
)
SQL);
        }

        if (Schema::hasTable('savings_transactions') && Schema::hasColumn('members', 'savings_transaction_id')) {
            Schema::table('members', function (Blueprint $table): void {
                $table->foreign('savings_transaction_id')
                    ->references('id')
                    ->on('savings_transactions')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasColumn('members', 'savings')) {
            Schema::table('members', function (Blueprint $table): void {
                $table->dropColumn('savings');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('members', 'savings_transaction_id')) {
            Schema::table('members', function (Blueprint $table): void {
                try {
                    $table->dropForeign(['savings_transaction_id']);
                } catch (\Throwable $e) {
                    // Ignore if the foreign key was never created.
                }

                $table->dropColumn('savings_transaction_id');
            });
        }

        if (!Schema::hasColumn('members', 'savings')) {
            Schema::table('members', function (Blueprint $table): void {
                $table->decimal('savings', 15, 2)->default(0)->after('membership_status');
            });
        }
    }
};
