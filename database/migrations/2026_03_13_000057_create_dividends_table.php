<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE dividends (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dividend_number VARCHAR(50) UNIQUE NOT NULL,
    share_class_id TINYINT UNSIGNED NOT NULL,
    amount_per_share DECIMAL(10,2) NOT NULL,
    total_shares_eligible INT UNSIGNED NOT NULL,
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (amount_per_share * total_shares_eligible) STORED,
    year SMALLINT NOT NULL,
    quarter TINYINT,
    period_start DATE,
    period_end DATE,
    declaration_date DATE NOT NULL,
    record_date DATE NOT NULL,
    payment_date DATE,
    total_paid DECIMAL(15,2) DEFAULT 0,
    total_withheld DECIMAL(15,2) DEFAULT 0,
    withholding_tax_rate DECIMAL(5,2) DEFAULT 0,
    status_id TINYINT UNSIGNED NOT NULL,
    declared_by BIGINT UNSIGNED NOT NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_dividends_class (share_class_id),
    INDEX idx_dividends_status (status_id),
    INDEX idx_dividends_period (year, quarter),
    FOREIGN KEY (share_class_id) REFERENCES share_classes(id),
    FOREIGN KEY (status_id) REFERENCES dividend_statuses(id),
    FOREIGN KEY (declared_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `dividends`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
