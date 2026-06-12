<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE projects (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    project_number VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(191) NOT NULL,
    description TEXT,
    category_id TINYINT UNSIGNED,
    budget_amount DECIMAL(15,2) NOT NULL,
    committed_amount DECIMAL(15,2) DEFAULT 0,
    spent_amount DECIMAL(15,2) DEFAULT 0,
    remaining_budget DECIMAL(15,2) GENERATED ALWAYS AS (budget_amount - spent_amount) STORED,
    start_date DATE,
    expected_end_date DATE,
    actual_end_date DATE NULL,
    status_id TINYINT UNSIGNED NOT NULL,
    progress_percentage TINYINT DEFAULT 0,
    milestones JSON,
    expected_revenue DECIMAL(15,2),
    actual_revenue DECIMAL(15,2) DEFAULT 0,
    expected_roi DECIMAL(5,2),
    actual_roi DECIMAL(5,2),
    potential_roi DECIMAL(8,2),
    risk_level_id TINYINT UNSIGNED,
    risk_score TINYINT DEFAULT 50,
    project_manager_id BIGINT UNSIGNED NULL,
    supervisor_id BIGINT UNSIGNED NULL,
    location_text TEXT,
    village_id MEDIUMINT UNSIGNED NULL,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    proposal_document_id BIGINT UNSIGNED NULL,
    contract_document_id BIGINT UNSIGNED NULL,
    is_featured TINYINT(1) DEFAULT 0,
    is_public TINYINT(1) DEFAULT 1,
    notes TEXT,
    metadata JSON,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    deleted_by BIGINT UNSIGNED NULL,
    deleted_reason TEXT,
    INDEX idx_projects_number (project_number),
    INDEX idx_projects_status (status_id),
    INDEX idx_projects_category (category_id),
    INDEX idx_projects_manager (project_manager_id),
    INDEX idx_projects_dates (start_date, expected_end_date),
    INDEX idx_projects_location (village_id),
    FOREIGN KEY (category_id) REFERENCES project_categories(id),
    FOREIGN KEY (status_id) REFERENCES project_statuses(id),
    FOREIGN KEY (risk_level_id) REFERENCES investment_risk_levels(id),
    FOREIGN KEY (project_manager_id) REFERENCES members(id),
    FOREIGN KEY (supervisor_id) REFERENCES members(id),
    FOREIGN KEY (village_id) REFERENCES villages(id),
    FOREIGN KEY (proposal_document_id) REFERENCES documents(id),
    FOREIGN KEY (contract_document_id) REFERENCES documents(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `projects`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
