<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE share_transfers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    transfer_number VARCHAR(50) UNIQUE NOT NULL,
    share_id BIGINT UNSIGNED NOT NULL,
    from_member_id BIGINT UNSIGNED NOT NULL,
    to_member_id BIGINT UNSIGNED NOT NULL,
    shares_count INT UNSIGNED NOT NULL,
    transfer_price DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (shares_count * transfer_price) STORED,
    transfer_date DATE NOT NULL,
    requires_approval TINYINT(1) DEFAULT 1,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    from_transaction_id BIGINT UNSIGNED NOT NULL,
    to_transaction_id BIGINT UNSIGNED NOT NULL,
    transfer_document_id BIGINT UNSIGNED NULL,
    status ENUM('pending', 'approved', 'completed', 'rejected') DEFAULT 'pending',
    rejection_reason TEXT,
    notes TEXT,
    requested_by BIGINT UNSIGNED NOT NULL,
    processed_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_share_transfers_share (share_id),
    INDEX idx_share_transfers_from (from_member_id),
    INDEX idx_share_transfers_to (to_member_id),
    INDEX idx_share_transfers_status (status),
    FOREIGN KEY (share_id) REFERENCES shares(id),
    FOREIGN KEY (from_member_id) REFERENCES members(id),
    FOREIGN KEY (to_member_id) REFERENCES members(id),
    FOREIGN KEY (from_transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (to_transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (transfer_document_id) REFERENCES documents(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (requested_by) REFERENCES users(id),
    FOREIGN KEY (processed_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `share_transfers`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
