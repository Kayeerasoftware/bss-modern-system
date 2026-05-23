<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE savings_accounts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    account_number VARCHAR(50) UNIQUE NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    plan_id TINYINT UNSIGNED NOT NULL,
    account_name VARCHAR(191) NOT NULL,
    opening_balance DECIMAL(15,2) DEFAULT 0.00,
    current_balance DECIMAL(15,2) DEFAULT 0.00,
    available_balance DECIMAL(15,2) DEFAULT 0.00,
    opening_date DATE NOT NULL,
    maturity_date DATE NULL,
    closing_date DATE NULL,
    last_interest_calculation DATE NULL,
    accrued_interest DECIMAL(15,2) DEFAULT 0,
    overdraft_limit DECIMAL(15,2) DEFAULT 0,
    overdraft_used DECIMAL(15,2) DEFAULT 0,
    is_joint TINYINT(1) DEFAULT 0,
    joint_holders JSON,
    status ENUM('active', 'dormant', 'frozen', 'closed') DEFAULT 'active',
    status_reason TEXT,
    frozen_by BIGINT UNSIGNED NULL,
    frozen_at TIMESTAMP NULL,
    standing_instructions JSON,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    closed_by BIGINT UNSIGNED NULL,
    closed_reason TEXT,
    INDEX idx_savings_member (member_id),
    INDEX idx_savings_plan (plan_id),
    INDEX idx_savings_status (status),
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (plan_id) REFERENCES savings_plans(id),
    FOREIGN KEY (frozen_by) REFERENCES users(id),
    FOREIGN KEY (closed_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `savings_accounts`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
