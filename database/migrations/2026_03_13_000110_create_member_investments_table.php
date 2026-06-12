<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE member_investments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    investment_number VARCHAR(50) UNIQUE NOT NULL,
    opportunity_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    amount_invested DECIMAL(15,2) NOT NULL,
    units_allocated INT UNSIGNED,
    unit_price DECIMAL(10,2),
    investment_date DATE NOT NULL,
    transaction_id BIGINT UNSIGNED NOT NULL,
    payment_method_id TINYINT UNSIGNED NOT NULL,
    returns_received DECIMAL(15,2) DEFAULT 0,
    roi_realized DECIMAL(5,2),
    last_dividend_paid DATE NULL,
    status_id TINYINT UNSIGNED NOT NULL,
    maturity_date DATE,
    is_reinvested TINYINT(1) DEFAULT 0,
    withdrawal_date DATE NULL,
    withdrawal_amount DECIMAL(15,2),
    withdrawal_transaction_id BIGINT UNSIGNED NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_investments_member (member_id),
    INDEX idx_investments_opportunity (opportunity_id),
    INDEX idx_investments_status (status_id),
    INDEX idx_investments_date (investment_date),
    FOREIGN KEY (opportunity_id) REFERENCES investment_opportunities(id),
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id),
    FOREIGN KEY (status_id) REFERENCES investment_statuses(id),
    FOREIGN KEY (withdrawal_transaction_id) REFERENCES transactions(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `member_investments`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
