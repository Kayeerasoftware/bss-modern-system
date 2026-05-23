<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add indexes for audit_logs
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!$this->indexExists('audit_logs', 'audit_logs_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->indexExists('audit_logs', 'audit_logs_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->indexExists('audit_logs', 'audit_logs_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at']);
            }
        });

        // Add indexes for members
        Schema::table('members', function (Blueprint $table) {
            if (!$this->indexExists('members', 'members_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->indexExists('members', 'members_membership_status_index')) {
                $table->index('membership_status');
            }
        });

        // Add indexes for transactions
        Schema::table('transactions', function (Blueprint $table) {
            if (!$this->indexExists('transactions', 'transactions_member_id_index')) {
                $table->index('member_id');
            }
            if (!$this->indexExists('transactions', 'transactions_status_id_index')) {
                $table->index('status_id');
            }
            if (!$this->indexExists('transactions', 'transactions_transaction_type_id_index')) {
                $table->index('transaction_type_id');
            }
        });

        // Add indexes for loans
        Schema::table('loans', function (Blueprint $table) {
            if (!$this->indexExists('loans', 'loans_member_id_index')) {
                $table->index('member_id');
            }
            if (!$this->indexExists('loans', 'loans_status_id_index')) {
                $table->index('status_id');
            }
        });

        // Add indexes for users
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_role_id_index')) {
                $table->index('role_id');
            }
            if (!$this->indexExists('users', 'users_status_index')) {
                $table->index('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['membership_status']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['member_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['transaction_type_id']);
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropIndex(['member_id']);
            $table->dropIndex(['status_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role_id']);
            $table->dropIndex(['status']);
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        return !empty($indexes);
    }
};
