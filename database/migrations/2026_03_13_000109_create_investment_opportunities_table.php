<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE investment_opportunities (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    opportunity_number VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(191) NOT NULL,
    description TEXT,
    target_amount DECIMAL(15,2) NOT NULL,
    minimum_investment DECIMAL(15,2) NOT NULL,
    maximum_investment DECIMAL(15,2),
    expected_roi DECIMAL(5,2),
    projected_returns JSON,
    risk_level_id TINYINT UNSIGNED,
    launch_date DATE NOT NULL,
    deadline_date DATE,
    close_date DATE NULL,
    raised_amount DECIMAL(15,2) DEFAULT 0,
    investor_count INT DEFAULT 0,
    status_id TINYINT UNSIGNED NOT NULL,
    prospectus_document_id BIGINT UNSIGNED NULL,
    fund_manager_id BIGINT UNSIGNED NULL,
    lock_in_period_months SMALLINT,
    dividend_frequency ENUM('monthly', 'quarterly', 'annually', 'maturity') DEFAULT 'annually',
    notes TEXT,
    metadata JSON,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_opportunities_number (opportunity_number),
    INDEX idx_opportunities_status (status_id),
    INDEX idx_opportunities_risk (risk_level_id),
    INDEX idx_opportunities_dates (launch_date, deadline_date),
    FOREIGN KEY (risk_level_id) REFERENCES investment_risk_levels(id),
    FOREIGN KEY (status_id) REFERENCES investment_statuses(id),
    FOREIGN KEY (prospectus_document_id) REFERENCES documents(id),
    FOREIGN KEY (fund_manager_id) REFERENCES members(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `investment_opportunities`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
