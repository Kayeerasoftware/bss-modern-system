<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chat_messages')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                if (!$this->hasIndex('chat_messages', 'chat_receiver_read_created_index')) {
                    $table->index(['receiver_id', 'is_read', 'created_at'], 'chat_receiver_read_created_index');
                }

                if (!$this->hasIndex('chat_messages', 'chat_sender_receiver_created_index')) {
                    $table->index(['sender_id', 'receiver_id', 'created_at'], 'chat_sender_receiver_created_index');
                }

                if (!$this->hasIndex('chat_messages', 'chat_receiver_sender_created_index')) {
                    $table->index(['receiver_id', 'sender_id', 'created_at'], 'chat_receiver_sender_created_index');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!$this->hasIndex('users', 'users_role_phone_index')) {
                    $table->index(['role', 'phone'], 'users_role_phone_index');
                }

                if (!$this->hasIndex('users', 'users_is_active_index')) {
                    $table->index(['is_active'], 'users_is_active_index');
                }
            });
        }

        if (Schema::hasTable('loan_applications')) {
            Schema::table('loan_applications', function (Blueprint $table) {
                if (!$this->hasIndex('loan_applications', 'loan_applications_status_created_index')) {
                    $table->index(['status', 'created_at'], 'loan_applications_status_created_index');
                }
            });
        }

        if (Schema::hasTable('fundraisings')) {
            Schema::table('fundraisings', function (Blueprint $table) {
                if (!$this->hasIndex('fundraisings', 'fundraisings_status_created_index')) {
                    $table->index(['status', 'created_at'], 'fundraisings_status_created_index');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chat_messages')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                if ($this->hasIndex('chat_messages', 'chat_receiver_read_created_index')) {
                    $table->dropIndex('chat_receiver_read_created_index');
                }
                if ($this->hasIndex('chat_messages', 'chat_sender_receiver_created_index')) {
                    $table->dropIndex('chat_sender_receiver_created_index');
                }
                if ($this->hasIndex('chat_messages', 'chat_receiver_sender_created_index')) {
                    $table->dropIndex('chat_receiver_sender_created_index');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if ($this->hasIndex('users', 'users_role_phone_index')) {
                    $table->dropIndex('users_role_phone_index');
                }
                if ($this->hasIndex('users', 'users_is_active_index')) {
                    $table->dropIndex('users_is_active_index');
                }
            });
        }

        if (Schema::hasTable('loan_applications')) {
            Schema::table('loan_applications', function (Blueprint $table) {
                if ($this->hasIndex('loan_applications', 'loan_applications_status_created_index')) {
                    $table->dropIndex('loan_applications_status_created_index');
                }
            });
        }

        if (Schema::hasTable('fundraisings')) {
            Schema::table('fundraisings', function (Blueprint $table) {
                if ($this->hasIndex('fundraisings', 'fundraisings_status_created_index')) {
                    $table->dropIndex('fundraisings_status_created_index');
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
