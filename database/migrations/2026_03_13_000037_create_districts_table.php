<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE districts (
    id MEDIUMINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    region_id TINYINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    is_active TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_district (region_id, name),
    INDEX idx_districts_region (region_id),
    FOREIGN KEY (region_id) REFERENCES regions(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `districts`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
