<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE savings_plans (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    plan_type_id TINYINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    minimum_balance DECIMAL(15,2) DEFAULT 0,
    interest_rate DECIMAL(5,2) DEFAULT 0,
    interest_calculation ENUM('daily', 'monthly', 'quarterly', 'annually') DEFAULT 'monthly',
    interest_payout ENUM('compound', 'withdrawable') DEFAULT 'compound',
    monthly_fee DECIMAL(15,2) DEFAULT 0,
    withdrawal_fee_percentage DECIMAL(5,2) DEFAULT 0,
    withdrawal_fee_fixed DECIMAL(15,2) DEFAULT 0,
    early_withdrawal_penalty DECIMAL(5,2) DEFAULT 0,
    min_deposit DECIMAL(15,2),
    max_deposit DECIMAL(15,2),
    min_withdrawal DECIMAL(15,2),
    max_withdrawal DECIMAL(15,2),
    withdrawal_limit_period ENUM('day', 'week', 'month') NULL,
    withdrawal_limit_count TINYINT NULL,
    min_duration_months SMALLINT,
    max_duration_months SMALLINT,
    is_taxable TINYINT(1) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    allows_overdraft TINYINT(1) DEFAULT 0,
    overdraft_limit DECIMAL(15,2),
    overdraft_interest_rate DECIMAL(5,2),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_savings_plans_type (plan_type_id),
    FOREIGN KEY (plan_type_id) REFERENCES savings_plan_types(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `savings_plans`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
