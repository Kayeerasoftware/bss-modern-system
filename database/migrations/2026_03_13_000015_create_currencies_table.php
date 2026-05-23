<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE currencies (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    code CHAR(3) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    symbol VARCHAR(10),
    decimal_places TINYINT DEFAULT 2,
    is_base TINYINT(1) DEFAULT 0,
    exchange_rate DECIMAL(10,4) DEFAULT 1.0000,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `currencies`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
