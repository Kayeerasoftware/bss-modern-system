<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        if (!Schema::hasColumn('users', 'profile_picture')) {
            DB::statement('ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) NULL AFTER status_reason');
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        if (Schema::hasColumn('users', 'profile_picture')) {
            DB::statement('ALTER TABLE users DROP COLUMN profile_picture');
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
