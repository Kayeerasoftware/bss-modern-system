<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('filename');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->enum('category', ['financial', 'legal', 'meeting', 'project', 'member', 'other']);
            $table->text('description')->nullable();
            $table->string('uploaded_by');
            $table->json('access_roles')->nullable(); // Which roles can access
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};