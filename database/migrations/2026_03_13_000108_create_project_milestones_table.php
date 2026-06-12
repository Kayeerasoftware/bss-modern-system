<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE project_milestones (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(191) NOT NULL,
    description TEXT,
    due_date DATE NOT NULL,
    completion_date DATE NULL,
    completion_percentage TINYINT DEFAULT 0,
    deliverables TEXT,
    is_critical TINYINT(1) DEFAULT 0,
    status ENUM('pending', 'in_progress', 'completed', 'delayed') DEFAULT 'pending',
    completed_by BIGINT UNSIGNED NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_project_milestones_project (project_id),
    INDEX idx_project_milestones_due_date (due_date),
    INDEX idx_project_milestones_status (status),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (completed_by) REFERENCES members(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `project_milestones`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
