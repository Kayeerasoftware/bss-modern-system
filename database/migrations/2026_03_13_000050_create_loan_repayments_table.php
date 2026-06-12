<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE loan_repayments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    repayment_number VARCHAR(50) UNIQUE NOT NULL,
    loan_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED UNIQUE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    principal_applied DECIMAL(15,2) NOT NULL,
    interest_applied DECIMAL(15,2) NOT NULL,
    fee_applied DECIMAL(15,2) DEFAULT 0,
    penalty_applied DECIMAL(15,2) DEFAULT 0,
    payment_date DATE NOT NULL,
    applied_to_installments JSON,
    is_early TINYINT(1) DEFAULT 0,
    is_late TINYINT(1) DEFAULT 0,
    days_late SMALLINT DEFAULT 0,
    receipt_number VARCHAR(100),
    receipt_issued_by BIGINT UNSIGNED NULL,
    receipt_issued_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_repayments_loan (loan_id),
    INDEX idx_repayments_date (payment_date),
    INDEX idx_repayments_transaction (transaction_id),
    FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (receipt_issued_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `loan_repayments`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
