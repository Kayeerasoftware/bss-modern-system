<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE loan_repayment_schedule (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_id BIGINT UNSIGNED NOT NULL,
    installment_number TINYINT NOT NULL,
    due_date DATE NOT NULL,
    principal_due DECIMAL(15,2) NOT NULL,
    interest_due DECIMAL(15,2) NOT NULL,
    fee_due DECIMAL(15,2) DEFAULT 0,
    total_due DECIMAL(15,2) GENERATED ALWAYS AS (
        principal_due + interest_due + fee_due
    ) STORED,
    principal_paid DECIMAL(15,2) DEFAULT 0,
    interest_paid DECIMAL(15,2) DEFAULT 0,
    fee_paid DECIMAL(15,2) DEFAULT 0,
    total_paid DECIMAL(15,2) GENERATED ALWAYS AS (
        principal_paid + interest_paid + fee_paid
    ) STORED,
    is_paid TINYINT(1) DEFAULT 0,
    paid_date DATE NULL,
    paid_transaction_id BIGINT UNSIGNED NULL,
    is_late TINYINT(1) DEFAULT 0,
    days_late INT DEFAULT 0,
    penalty_amount DECIMAL(15,2) DEFAULT 0,
    penalty_paid DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_loan_installment (loan_id, installment_number),
    INDEX idx_schedule_loan (loan_id),
    INDEX idx_schedule_due_date (due_date),
    INDEX idx_schedule_paid (is_paid),
    FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY (paid_transaction_id) REFERENCES transactions(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `loan_repayment_schedule`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
