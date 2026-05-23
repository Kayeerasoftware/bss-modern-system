<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE fundraising_expenses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    expense_number VARCHAR(50) UNIQUE NOT NULL,
    campaign_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED UNIQUE NOT NULL,
    description VARCHAR(191) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    expense_date DATE NOT NULL,
    category VARCHAR(100),
    payee_name VARCHAR(191),
    payee_type ENUM('individual', 'company', 'other') DEFAULT 'individual',
    receipt_number VARCHAR(100),
    receipt_document_id BIGINT UNSIGNED NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    notes TEXT,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_expenses_campaign (campaign_id),
    INDEX idx_expenses_date (expense_date),
    INDEX idx_expenses_category (category),
    FOREIGN KEY (campaign_id) REFERENCES fundraising_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (receipt_document_id) REFERENCES documents(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `fundraising_expenses`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
