<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE member_dividends (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dividend_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    shares_eligible INT UNSIGNED NOT NULL,
    amount_per_share DECIMAL(10,2) NOT NULL,
    gross_amount DECIMAL(15,2) GENERATED ALWAYS AS (shares_eligible * amount_per_share) STORED,
    withholding_tax DECIMAL(15,2) DEFAULT 0,
    net_amount DECIMAL(15,2) GENERATED ALWAYS AS (gross_amount - withholding_tax) STORED,
    transaction_id BIGINT UNSIGNED NULL,
    paid_at TIMESTAMP NULL,
    paid_by BIGINT UNSIGNED NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    payment_method_id TINYINT UNSIGNED NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_dividend_member (dividend_id, member_id),
    INDEX idx_member_dividends_member (member_id),
    INDEX idx_member_dividends_status (status),
    FOREIGN KEY (dividend_id) REFERENCES dividends(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (paid_by) REFERENCES users(id),
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `member_dividends`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
