<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('location')->nullable()->after('phone');
            $table->string('profile_picture')->nullable()->after('location');
            $table->integer('total_logins')->default(0)->after('profile_picture');
            $table->integer('actions_taken')->default(0)->after('total_logins');
            $table->timestamp('last_login_at')->nullable()->after('actions_taken');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'location', 'profile_picture', 'total_logins', 'actions_taken', 'last_login_at']);
        });
    }
};
