<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('category')->nullable()->after('name');
            $table->string('status')->default('planning')->after('progress');
            $table->date('start_date')->nullable()->after('timeline');
            $table->string('manager')->nullable()->after('risk_score');
            $table->string('location')->nullable()->after('manager');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['category', 'status', 'start_date', 'manager', 'location']);
        });
    }
};
