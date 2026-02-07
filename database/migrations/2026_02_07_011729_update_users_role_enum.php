<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role_temp')->nullable();
        });
        
        DB::statement("UPDATE users SET role_temp = CASE 
            WHEN role = 'admin' THEN 'admin'
            WHEN role = 'manager' THEN 'td'
            WHEN role = 'treasurer' THEN 'cashier'
            WHEN role = 'secretary' THEN 'client'
            WHEN role = 'member' THEN 'client'
            ELSE 'client'
        END");
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['client', 'shareholder', 'cashier', 'td', 'ceo', 'admin'])->default('client')->after('password');
        });
        
        DB::statement("UPDATE users SET role = role_temp");
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_temp');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'treasurer', 'secretary', 'member') DEFAULT 'member'");
    }
};
