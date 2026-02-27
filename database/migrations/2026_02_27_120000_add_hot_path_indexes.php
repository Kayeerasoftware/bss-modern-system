<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('loans')) {
            Schema::table('loans', function (Blueprint $table) {
                if (!$this->hasIndex('loans', 'loans_status_created_at_index')) {
                    $table->index(['status', 'created_at'], 'loans_status_created_at_index');
                }

                if (!$this->hasIndex('loans', 'loans_created_at_index')) {
                    $table->index(['created_at'], 'loans_created_at_index');
                }
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!$this->hasIndex('transactions', 'transactions_type_created_at_index')) {
                    $table->index(['type', 'created_at'], 'transactions_type_created_at_index');
                }

                if (!$this->hasIndex('transactions', 'transactions_created_at_index')) {
                    $table->index(['created_at'], 'transactions_created_at_index');
                }
            });
        }

        if (Schema::hasTable('projects') && Schema::hasColumn('projects', 'status')) {
            Schema::table('projects', function (Blueprint $table) {
                if (!$this->hasIndex('projects', 'projects_status_created_at_index')) {
                    $table->index(['status', 'created_at'], 'projects_status_created_at_index');
                }
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                if (!$this->hasIndex('audit_logs', 'audit_logs_timestamp_index')) {
                    $table->index(['timestamp'], 'audit_logs_timestamp_index');
                }

                if (!$this->hasIndex('audit_logs', 'audit_logs_action_timestamp_index')) {
                    $table->index(['action', 'timestamp'], 'audit_logs_action_timestamp_index');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('loans')) {
            Schema::table('loans', function (Blueprint $table) {
                if ($this->hasIndex('loans', 'loans_status_created_at_index')) {
                    $table->dropIndex('loans_status_created_at_index');
                }
                if ($this->hasIndex('loans', 'loans_created_at_index')) {
                    $table->dropIndex('loans_created_at_index');
                }
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if ($this->hasIndex('transactions', 'transactions_type_created_at_index')) {
                    $table->dropIndex('transactions_type_created_at_index');
                }
                if ($this->hasIndex('transactions', 'transactions_created_at_index')) {
                    $table->dropIndex('transactions_created_at_index');
                }
            });
        }

        if (Schema::hasTable('projects') && Schema::hasColumn('projects', 'status')) {
            Schema::table('projects', function (Blueprint $table) {
                if ($this->hasIndex('projects', 'projects_status_created_at_index')) {
                    $table->dropIndex('projects_status_created_at_index');
                }
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                if ($this->hasIndex('audit_logs', 'audit_logs_timestamp_index')) {
                    $table->dropIndex('audit_logs_timestamp_index');
                }
                if ($this->hasIndex('audit_logs', 'audit_logs_action_timestamp_index')) {
                    $table->dropIndex('audit_logs_action_timestamp_index');
                }
            });
        }
    }

    private function hasIndex(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $idx) {
            if (($idx->Key_name ?? null) === $index) {
                return true;
            }
        }

        return false;
    }
};
