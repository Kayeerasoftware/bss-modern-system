<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('uganda_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['region', 'district', 'county', 'subcounty', 'parish', 'village']);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('uganda_locations')->onDelete('cascade');
            $table->index(['type', 'parent_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('uganda_locations');
    }
};
