<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE document_shares (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    document_id BIGINT UNSIGNED NOT NULL,
    shared_with_member_id BIGINT UNSIGNED NOT NULL,
    shared_by BIGINT UNSIGNED NOT NULL,
    shared_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    permission ENUM('view', 'download', 'edit') DEFAULT 'view',
    is_active TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_document_share (document_id, shared_with_member_id),
    INDEX idx_document_shares_member (shared_with_member_id),
    FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_with_member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `document_shares`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
