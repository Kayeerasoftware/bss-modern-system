<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_category_balances', function (Blueprint $table) {
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

    public function down(): void
    {
        Schema::dropIfExists('member_category_balances');
    }
};
