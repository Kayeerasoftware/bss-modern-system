<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE investment_returns (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    return_number VARCHAR(50) UNIQUE NOT NULL,
    investment_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    return_type ENUM('dividend', 'interest', 'profit_share', 'capital_gain') NOT NULL,
    return_date DATE NOT NULL,
    transaction_id BIGINT UNSIGNED NOT NULL,
    paid_at TIMESTAMP NOT NULL,
    period_start DATE,
    period_end DATE,
    tax_withheld DECIMAL(15,2) DEFAULT 0,
    net_amount DECIMAL(15,2) GENERATED ALWAYS AS (amount - tax_withheld) STORED,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_investment_returns_investment (investment_id),
    INDEX idx_investment_returns_date (return_date),
    FOREIGN KEY (investment_id) REFERENCES member_investments(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `investment_returns`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
