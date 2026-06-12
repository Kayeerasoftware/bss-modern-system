<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE dashboard_photos (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    photo_number VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('project', 'meeting', 'event', 'achievement', 'slider') NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255),
    title VARCHAR(191),
    description TEXT,
    link_url VARCHAR(255),
    display_order SMALLINT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    start_date DATE NULL,
    end_date DATE NULL,
    views_count INT DEFAULT 0,
    clicks_count INT DEFAULT 0,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_photos_type (type, is_active, display_order),
    INDEX idx_photos_dates (start_date, end_date),
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `dashboard_photos`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
