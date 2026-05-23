<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE savings_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    savings_account_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED UNIQUE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    running_balance DECIMAL(15,2) NOT NULL,
    transaction_type ENUM('deposit', 'withdrawal', 'interest', 'fee', 'transfer') NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_savings_trans_account (savings_account_id),
    INDEX idx_savings_trans_type (transaction_type),
    FOREIGN KEY (savings_account_id) REFERENCES savings_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `savings_transactions`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
