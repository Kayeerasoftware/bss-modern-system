<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE generated_reports (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    report_number VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(191) NOT NULL,
    type VARCHAR(100) NOT NULL,
    from_date DATE NOT NULL,
    to_date DATE NOT NULL,
    format ENUM('pdf', 'excel', 'csv', 'html', 'json') NOT NULL,
    file_path VARCHAR(255),
    file_size BIGINT UNSIGNED,
    parameters JSON,
    filters JSON,
    columns JSON,
    row_count INT,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    generated_by BIGINT UNSIGNED NOT NULL,
    downloaded_count INT DEFAULT 0,
    last_downloaded_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    notes TEXT,
    INDEX idx_reports_type (type),
    INDEX idx_reports_dates (from_date, to_date),
    INDEX idx_reports_generated (generated_at),
    FOREIGN KEY (generated_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `generated_reports`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
