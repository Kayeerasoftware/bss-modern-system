<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_id')->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('location');
            $table->string('occupation');
            $table->string('contact');
            $table->decimal('savings', 15, 2)->default(0);
            $table->decimal('loan', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('savings_balance', 15, 2)->default(0);
            $table->string('password');
            $table->enum('role', ['client', 'shareholder', 'cashier', 'td', 'ceo'])->default('client');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('members');
    }
};