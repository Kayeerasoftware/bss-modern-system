<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE loan_applications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    application_number VARCHAR(50) UNIQUE NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    loan_type_id TINYINT UNSIGNED NOT NULL,
    requested_amount DECIMAL(15,2) NOT NULL,
    approved_amount DECIMAL(15,2) NULL,
    requested_tenure_months TINYINT NOT NULL,
    approved_tenure_months TINYINT NULL,
    purpose TEXT NOT NULL,
    applicant_comment TEXT,
    monthly_income DECIMAL(15,2),
    monthly_expenses DECIMAL(15,2),
    existing_loan_commitments DECIMAL(15,2),
    requested_installment DECIMAL(15,2) GENERATED ALWAYS AS (
        requested_amount / requested_tenure_months
    ) STORED,
    debt_to_income_ratio DECIMAL(5,2) GENERATED ALWAYS AS (
        (existing_loan_commitments + (requested_amount / requested_tenure_months)) / monthly_income * 100
    ) STORED,
    credit_score SMALLINT,
    risk_rating VARCHAR(20),
    assessment_notes TEXT,
    assessed_by BIGINT UNSIGNED NULL,
    assessed_at TIMESTAMP NULL,
    status_id TINYINT UNSIGNED NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    decision_date TIMESTAMP NULL,
    decision_by BIGINT UNSIGNED NULL,
    decision_notes TEXT,
    rejection_reason TEXT,
    requires_approval TINYINT(1) DEFAULT 1,
    approval_level TINYINT DEFAULT 1,
    current_approval_level TINYINT DEFAULT 0,
    converted_to_loan_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_loan_apps_member (member_id),
    INDEX idx_loan_apps_type (loan_type_id),
    INDEX idx_loan_apps_status (status_id),
    INDEX idx_loan_apps_submission (submission_date),
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (loan_type_id) REFERENCES loan_types(id),
    FOREIGN KEY (status_id) REFERENCES loan_statuses(id),
    FOREIGN KEY (assessed_by) REFERENCES users(id),
    FOREIGN KEY (decision_by) REFERENCES users(id),
    FOREIGN KEY (converted_to_loan_id) REFERENCES loans(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `loan_applications`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
