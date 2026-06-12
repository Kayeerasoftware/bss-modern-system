<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE backups (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    backup_number VARCHAR(50) UNIQUE NOT NULL,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    file_size BIGINT UNSIGNED,
    type ENUM('manual', 'scheduled', 'automatic') DEFAULT 'manual',
    status ENUM('pending', 'in_progress', 'completed', 'failed') DEFAULT 'pending',
    includes ENUM('full', 'structure_only', 'data_only') DEFAULT 'full',
    compression ENUM('none', 'gzip', 'zip') DEFAULT 'gzip',
    encryption TINYINT(1) DEFAULT 0,
    checksum VARCHAR(255),
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    failed_at TIMESTAMP NULL,
    failure_reason TEXT,
    notes TEXT,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_backups_status (status),
    INDEX idx_backups_type (type),
    FOREIGN KEY (created_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `backups`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
