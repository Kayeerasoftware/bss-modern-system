<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('portfolio_performances', function (Blueprint $table) {
            $table->id();
            $table->string('member_id');
            $table->date('period');
            $table->decimal('portfolio_value', 15, 2);
            $table->decimal('market_value', 15, 2);
            $table->decimal('performance_percentage', 5, 2);
            $table->decimal('benchmark_comparison', 5, 2);
            $table->timestamps();
            
            $table->foreign('member_id')->references('member_id')->on('members')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('portfolio_performances');
    }
};
