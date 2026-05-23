<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE pending_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    savings_account_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED NULL,
    amount DECIMAL(15,2) NOT NULL,
    transaction_type ENUM('debit', 'credit') NOT NULL DEFAULT 'debit',
    description VARCHAR(255),
    expires_at TIMESTAMP NULL,
    status ENUM('pending', 'cleared', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pending_trans_account (savings_account_id),
    INDEX idx_pending_trans_status (status),
    FOREIGN KEY (savings_account_id) REFERENCES savings_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE SET NULL
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `pending_transactions`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
