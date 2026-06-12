<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    log_number VARCHAR(50) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    member_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    session_id VARCHAR(191),
    action_type_id TINYINT UNSIGNED NOT NULL,
    entity_type_id TINYINT UNSIGNED NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    entity_identifier VARCHAR(191),
    description TEXT,
    details JSON,
    old_values JSON,
    new_values JSON,
    request_method VARCHAR(10),
    request_url TEXT,
    request_headers JSON,
    response_status SMALLINT,
    execution_time_ms INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_user (user_id),
    INDEX idx_audit_member (member_id),
    INDEX idx_audit_action (action_type_id),
    INDEX idx_audit_entity (entity_type_id, entity_id),
    INDEX idx_audit_created (created_at),
    INDEX idx_audit_ip (ip_address),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL,
    FOREIGN KEY (action_type_id) REFERENCES audit_action_types(id),
    FOREIGN KEY (entity_type_id) REFERENCES entity_types(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `audit_logs`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
