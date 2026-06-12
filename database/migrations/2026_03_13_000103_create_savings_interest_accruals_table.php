<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE savings_interest_accruals (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    savings_account_id BIGINT UNSIGNED NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    average_balance DECIMAL(15,2) NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,
    interest_amount DECIMAL(15,2) NOT NULL,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    net_amount DECIMAL(15,2) GENERATED ALWAYS AS (interest_amount - tax_amount) STORED,
    paid_transaction_id BIGINT UNSIGNED NULL,
    paid_at TIMESTAMP NULL,
    status ENUM('accrued', 'paid', 'cancelled') DEFAULT 'accrued',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_interest_account (savings_account_id),
    INDEX idx_interest_period (period_start, period_end),
    INDEX idx_interest_status (status),
    FOREIGN KEY (savings_account_id) REFERENCES savings_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (paid_transaction_id) REFERENCES transactions(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `savings_interest_accruals`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
