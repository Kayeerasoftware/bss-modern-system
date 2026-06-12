<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    transaction_number VARCHAR(50) UNIQUE NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    transaction_type_id TINYINT UNSIGNED NOT NULL,
    category_id TINYINT UNSIGNED NOT NULL,
    status_id TINYINT UNSIGNED NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    fee DECIMAL(15,2) DEFAULT 0.00,
    tax_amount DECIMAL(15,2) DEFAULT 0.00,
    commission DECIMAL(15,2) DEFAULT 0.00,
    exchange_rate DECIMAL(10,4) DEFAULT 1.0000,
    currency_id TINYINT UNSIGNED NOT NULL,
    net_amount DECIMAL(15,2) GENERATED ALWAYS AS (
        amount - fee - tax_amount - commission
    ) STORED,
    balance_before DECIMAL(15,2) NOT NULL,
    balance_after DECIMAL(15,2) NOT NULL,
    payment_method_id TINYINT UNSIGNED NOT NULL,
    reference_number VARCHAR(100),
    receipt_number VARCHAR(100),
    channel VARCHAR(50),
    terminal_id VARCHAR(50),
    related_loan_id BIGINT UNSIGNED NULL,
    related_share_id BIGINT UNSIGNED NULL,
    related_dividend_id BIGINT UNSIGNED NULL,
    related_investment_id BIGINT UNSIGNED NULL,
    related_transfer_to_member_id BIGINT UNSIGNED NULL,
    description TEXT,
    notes TEXT,
    metadata JSON NULL COMMENT 'Additional flexible data',
    requires_approval TINYINT(1) DEFAULT 0,
    approval_level TINYINT DEFAULT 0,
    current_approval_level TINYINT DEFAULT 0,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    approval_notes TEXT,
    processed_by BIGINT UNSIGNED NOT NULL,
    processed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processed_ip VARCHAR(45),
    processed_location VARCHAR(255),
    is_reversal TINYINT(1) DEFAULT 0,
    parent_transaction_id BIGINT UNSIGNED NULL,
    reversed_at TIMESTAMP NULL,
    reversed_by BIGINT UNSIGNED NULL,
    reversal_reason TEXT,
    reconciled TINYINT(1) DEFAULT 0,
    reconciled_at TIMESTAMP NULL,
    reconciled_by BIGINT UNSIGNED NULL,
    reconciliation_notes TEXT,
    is_scheduled TINYINT(1) DEFAULT 0,
    scheduled_at TIMESTAMP NULL,
    schedule_rule VARCHAR(255),
    transaction_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    value_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    deleted_by BIGINT UNSIGNED NULL,
    deleted_reason TEXT,
    INDEX idx_transactions_member (member_id),
    INDEX idx_transactions_type (transaction_type_id),
    INDEX idx_transactions_category (category_id),
    INDEX idx_transactions_status (status_id),
    INDEX idx_transactions_date (transaction_date),
    INDEX idx_transactions_reference (reference_number),
    INDEX idx_transactions_receipt (receipt_number),
    INDEX idx_transactions_related_loan (related_loan_id),
    INDEX idx_transactions_related_share (related_share_id),
    INDEX idx_transactions_related_dividend (related_dividend_id),
    INDEX idx_transactions_transfer (related_transfer_to_member_id),
    INDEX idx_transactions_reconciled (reconciled),
    INDEX idx_transactions_parent (parent_transaction_id),
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (transaction_type_id) REFERENCES transaction_types(id),
    FOREIGN KEY (category_id) REFERENCES transaction_categories(id),
    FOREIGN KEY (status_id) REFERENCES transaction_statuses(id),
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id),
    FOREIGN KEY (currency_id) REFERENCES currencies(id),
    FOREIGN KEY (related_loan_id) REFERENCES loans(id),
    FOREIGN KEY (related_share_id) REFERENCES shares(id),
    FOREIGN KEY (related_dividend_id) REFERENCES member_dividends(id),
    FOREIGN KEY (related_investment_id) REFERENCES member_investments(id),
    FOREIGN KEY (related_transfer_to_member_id) REFERENCES members(id),
    FOREIGN KEY (processed_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (reversed_by) REFERENCES users(id),
    FOREIGN KEY (reconciled_by) REFERENCES users(id),
    FOREIGN KEY (parent_transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `transactions`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
