<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE fundraising_campaigns (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    campaign_number VARCHAR(50) UNIQUE NOT NULL,
    category_id TINYINT UNSIGNED,
    title VARCHAR(191) NOT NULL,
    description TEXT,
    target_amount DECIMAL(15,2) NOT NULL,
    raised_amount DECIMAL(15,2) DEFAULT 0.00,
    min_contribution DECIMAL(15,2),
    max_contribution DECIMAL(15,2),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status_id TINYINT UNSIGNED NOT NULL,
    organizer_id BIGINT UNSIGNED NOT NULL,
    contact_person VARCHAR(191),
    contact_phone VARCHAR(50),
    contact_email VARCHAR(191),
    location_text TEXT,
    village_id MEDIUMINT UNSIGNED NULL,
    cover_image VARCHAR(255),
    gallery JSON,
    video_url VARCHAR(255),
    bank_account_details JSON,
    mobile_money_details JSON,
    is_tax_deductible TINYINT(1) DEFAULT 0,
    tax_receipts_issued TINYINT(1) DEFAULT 0,
    updates JSON,
    notes TEXT,
    metadata JSON,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_campaigns_number (campaign_number),
    INDEX idx_campaigns_status (status_id),
    INDEX idx_campaigns_category (category_id),
    INDEX idx_campaigns_dates (start_date, end_date),
    INDEX idx_campaigns_organizer (organizer_id),
    FOREIGN KEY (category_id) REFERENCES fundraising_categories(id),
    FOREIGN KEY (status_id) REFERENCES fundraising_statuses(id),
    FOREIGN KEY (organizer_id) REFERENCES members(id),
    FOREIGN KEY (village_id) REFERENCES villages(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `fundraising_campaigns`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
