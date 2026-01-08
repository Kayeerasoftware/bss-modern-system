<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_id')->unique();
            $table->string('member_id');
            $table->decimal('amount', 15, 2);
            $table->text('purpose');
            $table->integer('repayment_months');
            $table->decimal('interest', 15, 2);
            $table->decimal('monthly_payment', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            
            $table->foreign('member_id')->references('member_id')->on('members');
        });
    }

    public function down()
    {
        Schema::dropIfExists('loans');
    }
};