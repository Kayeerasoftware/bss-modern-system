<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE loans (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_number VARCHAR(50) UNIQUE NOT NULL,
    application_id BIGINT UNSIGNED NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    loan_type_id TINYINT UNSIGNED NOT NULL,
    principal_amount DECIMAL(15,2) NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,
    interest_type ENUM('fixed', 'declining', 'reducing') DEFAULT 'fixed',
    interest_amount DECIMAL(15,2) GENERATED ALWAYS AS (
        CASE interest_type
            WHEN 'fixed' THEN principal_amount * interest_rate / 100
            ELSE 0
        END
    ) STORED,
    total_interest DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (
        principal_amount + total_interest
    ) STORED,
    repayment_months TINYINT NOT NULL,
    repayment_frequency ENUM('daily', 'weekly', 'monthly', 'quarterly') DEFAULT 'monthly',
    monthly_payment DECIMAL(15,2) GENERATED ALWAYS AS (
        (principal_amount + total_interest) / repayment_months
    ) STORED,
    installment_amount DECIMAL(15,2) GENERATED ALWAYS AS (
        (principal_amount + total_interest) / 
        CASE repayment_frequency
            WHEN 'daily' THEN repayment_months * 30
            WHEN 'weekly' THEN repayment_months * 4
            WHEN 'monthly' THEN repayment_months
            WHEN 'quarterly' THEN repayment_months / 3
        END
    ) STORED,
    processing_fee DECIMAL(15,2) DEFAULT 0,
    processing_fee_transaction_id BIGINT UNSIGNED NULL,
    insurance_fee DECIMAL(15,2) DEFAULT 0,
    legal_fee DECIMAL(15,2) DEFAULT 0,
    other_fees DECIMAL(15,2) DEFAULT 0,
    total_fees DECIMAL(15,2) GENERATED ALWAYS AS (
        processing_fee + insurance_fee + legal_fee + other_fees
    ) STORED,
    guarantor1_id BIGINT UNSIGNED NULL,
    guarantor1_agreed_at TIMESTAMP NULL,
    guarantor1_ip VARCHAR(45),
    guarantor2_id BIGINT UNSIGNED NULL,
    guarantor2_agreed_at TIMESTAMP NULL,
    guarantor2_ip VARCHAR(45),
    has_collateral TINYINT(1) DEFAULT 0,
    collateral_details JSON,
    application_date DATE NOT NULL,
    approval_date DATE NULL,
    disbursement_date DATE NULL,
    first_payment_date DATE NULL,
    maturity_date DATE GENERATED ALWAYS AS (
        disbursement_date + INTERVAL repayment_months MONTH
    ) STORED,
    completed_date DATE NULL,
    disbursement_transaction_id BIGINT UNSIGNED NULL,
    disbursement_method_id TINYINT UNSIGNED NULL,
    amount_paid DECIMAL(15,2) DEFAULT 0.00,
    last_payment_date DATE NULL,
    last_payment_amount DECIMAL(15,2) NULL,
    payments_made TINYINT DEFAULT 0,
    payments_remaining TINYINT GENERATED ALWAYS AS (repayment_months - payments_made) STORED,
    balance_due DECIMAL(15,2) GENERATED ALWAYS AS (total_amount - amount_paid) STORED,
    status_id TINYINT UNSIGNED NOT NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    approved_ip VARCHAR(45),
    disbursed_by BIGINT UNSIGNED NULL,
    disbursed_at TIMESTAMP NULL,
    closed_by BIGINT UNSIGNED NULL,
    closed_at TIMESTAMP NULL,
    closed_reason VARCHAR(50),
    is_defaulted TINYINT(1) DEFAULT 0,
    defaulted_date DATE NULL,
    default_amount DECIMAL(15,2) NULL,
    days_overdue INT DEFAULT 0,
    last_reminder_sent TIMESTAMP NULL,
    is_restructured TINYINT(1) DEFAULT 0,
    original_loan_id BIGINT UNSIGNED NULL,
    restructure_date DATE NULL,
    restructure_reason TEXT,
    notes TEXT,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    deleted_by BIGINT UNSIGNED NULL,
    deleted_reason TEXT,
    INDEX idx_loans_member (member_id),
    INDEX idx_loans_number (loan_number),
    INDEX idx_loans_type (loan_type_id),
    INDEX idx_loans_status (status_id),
    INDEX idx_loans_dates (application_date, disbursement_date, maturity_date),
    INDEX idx_loans_guarantor1 (guarantor1_id),
    INDEX idx_loans_guarantor2 (guarantor2_id),
    INDEX idx_loans_disbursement (disbursement_transaction_id),
    INDEX idx_loans_default (is_defaulted),
    FOREIGN KEY (application_id) REFERENCES loan_applications(id),
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (loan_type_id) REFERENCES loan_types(id),
    FOREIGN KEY (guarantor1_id) REFERENCES members(id),
    FOREIGN KEY (guarantor2_id) REFERENCES members(id),
    FOREIGN KEY (status_id) REFERENCES loan_statuses(id),
    FOREIGN KEY (disbursement_transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (disbursement_method_id) REFERENCES payment_methods(id),
    FOREIGN KEY (processing_fee_transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (disbursed_by) REFERENCES users(id),
    FOREIGN KEY (closed_by) REFERENCES users(id),
    FOREIGN KEY (original_loan_id) REFERENCES loans(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `loans`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
