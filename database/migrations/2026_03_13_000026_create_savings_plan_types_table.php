<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE savings_plan_types (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    min_balance DECIMAL(15,2) DEFAULT 0,
    interest_rate DECIMAL(5,2) DEFAULT 0,
    interest_calculation ENUM('daily', 'monthly', 'quarterly', 'annually') DEFAULT 'monthly',
    withdrawal_fee_percentage DECIMAL(5,2) DEFAULT 0,
    withdrawal_fee_fixed DECIMAL(15,2) DEFAULT 0,
    min_withdrawal DECIMAL(15,2),
    max_withdrawal DECIMAL(15,2),
    withdrawal_limit_period ENUM('day', 'week', 'month') NULL,
    withdrawal_limit_count TINYINT NULL,
    is_taxable TINYINT(1) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `savings_plan_types`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
