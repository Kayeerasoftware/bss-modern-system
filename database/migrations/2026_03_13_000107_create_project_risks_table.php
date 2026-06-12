<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE project_risks (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT UNSIGNED NOT NULL,
    risk_description TEXT NOT NULL,
    risk_category VARCHAR(100),
    probability ENUM('low', 'medium', 'high') DEFAULT 'medium',
    impact ENUM('low', 'medium', 'high') DEFAULT 'medium',
    risk_score TINYINT GENERATED ALWAYS AS (
        CASE 
            WHEN probability = 'high' AND impact = 'high' THEN 9
            WHEN probability = 'high' AND impact = 'medium' THEN 6
            WHEN probability = 'high' AND impact = 'low' THEN 3
            WHEN probability = 'medium' AND impact = 'high' THEN 6
            WHEN probability = 'medium' AND impact = 'medium' THEN 4
            WHEN probability = 'medium' AND impact = 'low' THEN 2
            WHEN probability = 'low' AND impact = 'high' THEN 3
            WHEN probability = 'low' AND impact = 'medium' THEN 2
            ELSE 1
        END
    ) STORED,
    mitigation_strategy TEXT,
    contingency_plan TEXT,
    owner_id BIGINT UNSIGNED NULL,
    status ENUM('identified', 'monitoring', 'mitigated', 'occurred') DEFAULT 'identified',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_project_risks_project (project_id),
    INDEX idx_project_risks_score (risk_score),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES members(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `project_risks`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
