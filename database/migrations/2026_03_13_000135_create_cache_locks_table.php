<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE cache_locks (
    `key` VARCHAR(191) PRIMARY KEY,
    owner VARCHAR(191) NOT NULL,
    expiration INT NOT NULL
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `cache_locks`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
