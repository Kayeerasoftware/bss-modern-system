<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE fundraising_contributions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    contribution_number VARCHAR(50) UNIQUE NOT NULL,
    campaign_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED UNIQUE NOT NULL,
    member_id BIGINT UNSIGNED NULL,
    contributor_name VARCHAR(191) NOT NULL,
    contributor_email VARCHAR(191),
    contributor_phone VARCHAR(50),
    contributor_address TEXT,
    is_anonymous TINYINT(1) DEFAULT 0,
    amount DECIMAL(15,2) NOT NULL,
    contribution_date DATE NOT NULL,
    payment_method_id TINYINT UNSIGNED NOT NULL,
    receipt_number VARCHAR(100),
    receipt_issued TINYINT(1) DEFAULT 0,
    receipt_issued_at TIMESTAMP NULL,
    receipt_issued_by BIGINT UNSIGNED NULL,
    thank_you_sent TINYINT(1) DEFAULT 0,
    thank_you_sent_at TIMESTAMP NULL,
    message TEXT,
    is_public_message TINYINT(1) DEFAULT 1,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contributions_campaign (campaign_id),
    INDEX idx_contributions_member (member_id),
    INDEX idx_contributions_date (contribution_date),
    INDEX idx_contributions_receipt (receipt_number),
    FOREIGN KEY (campaign_id) REFERENCES fundraising_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id),
    FOREIGN KEY (receipt_issued_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `fundraising_contributions`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
