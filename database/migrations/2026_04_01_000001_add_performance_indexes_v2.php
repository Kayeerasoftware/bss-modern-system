<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!$this->indexExists('transactions', 'transactions_category_id_index')) {
                $table->index('category_id');
            }
            if (!$this->indexExists('transactions', 'transactions_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->indexExists('transactions', 'transactions_transaction_date_index')) {
                $table->index('transaction_date');
            }
            if (!$this->indexExists('transactions', 'transactions_status_id_category_id_index')) {
                $table->index(['status_id', 'category_id']);
            }
            if (!$this->indexExists('transactions', 'transactions_member_id_category_id_index')) {
                $table->index(['member_id', 'category_id']);
            }
            if (!$this->indexExists('transactions', 'transactions_status_id_transaction_type_id_index')) {
                $table->index(['status_id', 'transaction_type_id']);
            }
        });

        Schema::table('savings_accounts', function (Blueprint $table) {
            if (!$this->indexExists('savings_accounts', 'savings_accounts_member_id_index')) {
                $table->index('member_id');
            }
            if (!$this->indexExists('savings_accounts', 'savings_accounts_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('savings_accounts', 'savings_accounts_is_joint_index')) {
                $table->index('is_joint');
            }
            if (!$this->indexExists('savings_accounts', 'savings_accounts_maturity_date_index')) {
                $table->index('maturity_date');
            }
            if (!$this->indexExists('savings_accounts', 'savings_accounts_current_balance_index')) {
                $table->index('current_balance');
            }
            if (!$this->indexExists('savings_accounts', 'savings_accounts_opening_date_index')) {
                $table->index('opening_date');
            }
        });

        Schema::table('transaction_categories', function (Blueprint $table) {
            if (!$this->indexExists('transaction_categories', 'transaction_categories_name_index')) {
                $table->index('name');
            }
        });

        Schema::table('transaction_statuses', function (Blueprint $table) {
            if (!$this->indexExists('transaction_statuses', 'transaction_statuses_name_index')) {
                $table->index('name');
            }
        });

        Schema::table('transaction_types', function (Blueprint $table) {
            if (!$this->indexExists('transaction_types', 'transaction_types_name_index')) {
                $table->index('name');
            }
            if (!$this->indexExists('transaction_types', 'transaction_types_affects_savings_index')) {
                $table->index('affects_savings');
            }
        });

        Schema::table('fundraising_contributions', function (Blueprint $table) {
            if (!$this->indexExists('fundraising_contributions', 'fundraising_contributions_transaction_id_index')) {
                $table->index('transaction_id');
            }
        });

        Schema::table('entity_types', function (Blueprint $table) {
            if (!$this->indexExists('entity_types', 'entity_types_name_index')) {
                $table->index('name');
            }
        });

        Schema::table('savings_interest_accruals', function (Blueprint $table) {
            if (!$this->indexExists('savings_interest_accruals', 'savings_interest_accruals_status_index')) {
                $table->index('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['transaction_date']);
            $table->dropIndex(['status_id', 'category_id']);
            $table->dropIndex(['member_id', 'category_id']);
            $table->dropIndex(['status_id', 'transaction_type_id']);
        });

        Schema::table('savings_accounts', function (Blueprint $table) {
            $table->dropIndex(['member_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['is_joint']);
            $table->dropIndex(['maturity_date']);
            $table->dropIndex(['current_balance']);
            $table->dropIndex(['opening_date']);
        });

        Schema::table('transaction_categories', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('transaction_statuses', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('transaction_types', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['affects_savings']);
        });

        Schema::table('fundraising_contributions', function (Blueprint $table) {
            $table->dropIndex(['transaction_id']);
        });

        Schema::table('entity_types', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('savings_interest_accruals', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        return !empty($indexes);
    }
};
