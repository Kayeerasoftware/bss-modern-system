<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('type');
            }
            if (!Schema::hasColumn('transactions', 'reference')) {
                $table->string('reference')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('transactions', 'status')) {
                $table->string('status')->default('completed')->after('reference');
            }
            if (!Schema::hasColumn('transactions', 'processed_by')) {
                $table->unsignedBigInteger('processed_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('transactions', 'transaction_date')) {
                $table->timestamp('transaction_date')->nullable()->after('processed_by');
            }
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'reference', 'status', 'processed_by', 'transaction_date']);
        });
    }
};
