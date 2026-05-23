<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE subcounties (
    id MEDIUMINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    county_id MEDIUMINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    is_active TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_subcounty (county_id, name),
    INDEX idx_subcounties_county (county_id),
    FOREIGN KEY (county_id) REFERENCES counties(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `subcounties`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
