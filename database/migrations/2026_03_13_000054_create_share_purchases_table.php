<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE share_purchases (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    purchase_number VARCHAR(50) UNIQUE NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    share_issue_id BIGINT UNSIGNED NULL,
    share_class_id TINYINT UNSIGNED NOT NULL,
    shares_count INT UNSIGNED NOT NULL,
    price_per_share DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (shares_count * price_per_share) STORED,
    purchase_date DATE NOT NULL,
    transaction_id BIGINT UNSIGNED NOT NULL,
    payment_method_id TINYINT UNSIGNED NOT NULL,
    is_fully_paid TINYINT(1) DEFAULT 1,
    payment_plan JSON NULL,
    status_id TINYINT UNSIGNED NOT NULL,
    certificate_number VARCHAR(100) UNIQUE NOT NULL,
    certificate_issued_date DATE,
    certificate_issued_by BIGINT UNSIGNED NULL,
    notes TEXT,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_share_purchases_member (member_id),
    INDEX idx_share_purchases_issue (share_issue_id),
    INDEX idx_share_purchases_class (share_class_id),
    INDEX idx_share_purchases_certificate (certificate_number),
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (share_issue_id) REFERENCES share_issues(id),
    FOREIGN KEY (share_class_id) REFERENCES share_classes(id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id),
    FOREIGN KEY (status_id) REFERENCES share_statuses(id),
    FOREIGN KEY (certificate_issued_by) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `share_purchases`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
