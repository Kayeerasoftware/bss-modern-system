<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('fee');
            }
            if (!Schema::hasColumn('transactions', 'commission')) {
                $table->decimal('commission', 15, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('transactions', 'exchange_rate')) {
                $table->decimal('exchange_rate', 10, 4)->default(1)->after('commission');
            }
            if (!Schema::hasColumn('transactions', 'currency')) {
                $table->string('currency', 3)->default('UGX')->after('exchange_rate');
            }
            if (!Schema::hasColumn('transactions', 'channel')) {
                $table->string('channel')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('transactions', 'device_info')) {
                $table->text('device_info')->nullable()->after('channel');
            }
            if (!Schema::hasColumn('transactions', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('device_info');
            }
            if (!Schema::hasColumn('transactions', 'location')) {
                $table->string('location')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('transactions', 'reversal_reason')) {
                $table->text('reversal_reason')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('transactions', 'reversed_at')) {
                $table->timestamp('reversed_at')->nullable()->after('reversal_reason');
            }
            if (!Schema::hasColumn('transactions', 'reversed_by')) {
                $table->unsignedBigInteger('reversed_by')->nullable()->after('reversed_at');
            }
            if (!Schema::hasColumn('transactions', 'parent_transaction_id')) {
                $table->unsignedBigInteger('parent_transaction_id')->nullable()->after('reversed_by');
            }
            if (!Schema::hasColumn('transactions', 'batch_id')) {
                $table->string('batch_id')->nullable()->after('parent_transaction_id');
            }
            if (!Schema::hasColumn('transactions', 'reconciled')) {
                $table->boolean('reconciled')->default(false)->after('batch_id');
            }
            if (!Schema::hasColumn('transactions', 'reconciled_at')) {
                $table->timestamp('reconciled_at')->nullable()->after('reconciled');
            }
            if (!Schema::hasColumn('transactions', 'reconciled_by')) {
                $table->unsignedBigInteger('reconciled_by')->nullable()->after('reconciled_at');
            }
            if (!Schema::hasColumn('transactions', 'attachments')) {
                $table->json('attachments')->nullable()->after('reconciled_by');
            }
            if (!Schema::hasColumn('transactions', 'metadata')) {
                $table->json('metadata')->nullable()->after('attachments');
            }
            if (!Schema::hasColumn('transactions', 'priority')) {
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('metadata');
            }
            if (!Schema::hasColumn('transactions', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable()->after('transaction_date');
            }
            if (!Schema::hasColumn('transactions', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('scheduled_at');
            }
            if (!Schema::hasColumn('transactions', 'failed_at')) {
                $table->timestamp('failed_at')->nullable()->after('completed_at');
            }
            if (!Schema::hasColumn('transactions', 'failure_reason')) {
                $table->text('failure_reason')->nullable()->after('failed_at');
            }
            if (!Schema::hasColumn('transactions', 'retry_count')) {
                $table->integer('retry_count')->default(0)->after('failure_reason');
            }
            if (!Schema::hasColumn('transactions', 'notification_sent')) {
                $table->boolean('notification_sent')->default(false)->after('retry_count');
            }
            if (!Schema::hasColumn('transactions', 'notification_sent_at')) {
                $table->timestamp('notification_sent_at')->nullable()->after('notification_sent');
            }
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'tax_amount', 'commission', 'exchange_rate', 'currency', 'channel', 
                'device_info', 'ip_address', 'location', 'reversal_reason', 'reversed_at', 
                'reversed_by', 'parent_transaction_id', 'batch_id', 'reconciled', 
                'reconciled_at', 'reconciled_by', 'attachments', 'metadata', 'priority',
                'scheduled_at', 'completed_at', 'failed_at', 'failure_reason', 'retry_count',
                'notification_sent', 'notification_sent_at'
            ]);
        });
    }
};
