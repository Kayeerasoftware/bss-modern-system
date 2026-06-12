<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE shares (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    share_number VARCHAR(50) UNIQUE NOT NULL,
    certificate_number VARCHAR(100) NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    share_class_id TINYINT UNSIGNED NOT NULL,
    purchase_id BIGINT UNSIGNED NOT NULL,
    shares_count INT UNSIGNED NOT NULL,
    purchase_price DECIMAL(10,2) NOT NULL,
    current_value DECIMAL(10,2) NOT NULL,
    total_value DECIMAL(15,2) GENERATED ALWAYS AS (shares_count * current_value) STORED,
    purchase_date DATE NOT NULL,
    vesting_date DATE NULL,
    expiry_date DATE NULL,
    status_id TINYINT UNSIGNED NOT NULL,
    sold_date DATE NULL,
    sold_price DECIMAL(10,2) NULL,
    sold_to_member_id BIGINT UNSIGNED NULL,
    sale_transaction_id BIGINT UNSIGNED NULL,
    dividend_eligible TINYINT(1) DEFAULT 1,
    last_dividend_paid DATE NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_shares_member (member_id),
    INDEX idx_shares_certificate (certificate_number),
    INDEX idx_shares_class (share_class_id),
    INDEX idx_shares_purchase (purchase_id),
    INDEX idx_shares_status (status_id),
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (share_class_id) REFERENCES share_classes(id),
    FOREIGN KEY (purchase_id) REFERENCES share_purchases(id),
    FOREIGN KEY (status_id) REFERENCES share_statuses(id),
    FOREIGN KEY (sold_to_member_id) REFERENCES members(id),
    FOREIGN KEY (sale_transaction_id) REFERENCES transactions(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `shares`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
