<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'category')) {
                $table->string('category')->nullable()->after('type');
            }
            if (!Schema::hasColumn('transactions', 'fee')) {
                $table->decimal('fee', 15, 2)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('transactions', 'net_amount')) {
                $table->decimal('net_amount', 15, 2)->nullable()->after('fee');
            }
            if (!Schema::hasColumn('transactions', 'balance_before')) {
                $table->decimal('balance_before', 15, 2)->nullable()->after('net_amount');
            }
            if (!Schema::hasColumn('transactions', 'balance_after')) {
                $table->decimal('balance_after', 15, 2)->nullable()->after('balance_before');
            }
            if (!Schema::hasColumn('transactions', 'receipt_number')) {
                $table->string('receipt_number')->nullable()->after('reference');
            }
            if (!Schema::hasColumn('transactions', 'notes')) {
                $table->text('notes')->nullable()->after('description');
            }
            if (!Schema::hasColumn('transactions', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('processed_by');
            }
            if (!Schema::hasColumn('transactions', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['category', 'fee', 'net_amount', 'balance_before', 'balance_after', 'receipt_number', 'notes', 'approved_by', 'approved_at']);
        });
    }
};
