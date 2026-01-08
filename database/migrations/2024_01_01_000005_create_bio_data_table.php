<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bio_data', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('nin_no');
            $table->json('present_address');
            $table->json('permanent_address');
            $table->json('telephone');
            $table->string('email')->nullable();
            $table->string('dob');
            $table->string('nationality')->nullable();
            $table->json('birth_place');
            $table->string('marital_status');
            $table->string('spouse_name')->nullable();
            $table->string('spouse_nin')->nullable();
            $table->string('next_of_kin')->nullable();
            $table->string('next_of_kin_nin')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->json('children')->nullable();
            $table->text('occupation')->nullable();
            $table->string('signature');
            $table->date('declaration_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bio_data');
    }
};