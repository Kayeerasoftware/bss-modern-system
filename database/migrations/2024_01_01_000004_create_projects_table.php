<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_id')->unique();
            $table->string('name');
            $table->decimal('budget', 15, 2);
            $table->date('timeline');
            $table->text('description');
            $table->integer('progress')->default(0);
            $table->decimal('roi', 5, 2)->default(10);
            $table->integer('risk_score')->default(50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};