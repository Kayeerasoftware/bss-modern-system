<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE parishes (
    id MEDIUMINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    subcounty_id MEDIUMINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    is_active TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_parish (subcounty_id, name),
    INDEX idx_parishes_subcounty (subcounty_id),
    FOREIGN KEY (subcounty_id) REFERENCES subcounties(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `parishes`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
