<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE share_classes (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    par_value DECIMAL(10,2) NOT NULL,
    min_purchase INT UNSIGNED,
    max_purchase INT UNSIGNED,
    voting_rights TINYINT(1) DEFAULT 1,
    dividend_priority TINYINT DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `share_classes`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
