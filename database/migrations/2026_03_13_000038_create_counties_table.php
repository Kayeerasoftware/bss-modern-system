<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE counties (
    id MEDIUMINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    district_id MEDIUMINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    is_active TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_county (district_id, name),
    INDEX idx_counties_district (district_id),
    FOREIGN KEY (district_id) REFERENCES districts(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `counties`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
