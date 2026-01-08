<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->string('member_id');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['deposit', 'withdrawal', 'loanDisbursement', 'condolenceContribution']);
            $table->timestamps();
            
            $table->foreign('member_id')->references('member_id')->on('members');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};