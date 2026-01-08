<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('savings_history', function (Blueprint $table) {
            $table->id();
            $table->string('member_id');
            $table->decimal('amount', 15, 2);
            $table->date('month');
            $table->timestamps();
            
            $table->foreign('member_id')->references('member_id')->on('members');
            $table->index(['member_id', 'month']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('savings_history');
    }
};