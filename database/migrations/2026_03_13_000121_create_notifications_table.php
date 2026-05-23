<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE notifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    notification_number VARCHAR(50) UNIQUE NOT NULL,
    type_id TINYINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NULL,
    role_id TINYINT UNSIGNED NULL,
    title VARCHAR(191) NOT NULL,
    message TEXT NOT NULL,
    short_message VARCHAR(255),
    action_url VARCHAR(255),
    action_text VARCHAR(100),
    image_url VARCHAR(255),
    icon VARCHAR(50),
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    scheduled_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    send_email TINYINT(1) DEFAULT 0,
    send_sms TINYINT(1) DEFAULT 0,
    send_push TINYINT(1) DEFAULT 1,
    send_in_app TINYINT(1) DEFAULT 1,
    email_sent TINYINT(1) DEFAULT 0,
    email_sent_at TIMESTAMP NULL,
    sms_sent TINYINT(1) DEFAULT 0,
    sms_sent_at TIMESTAMP NULL,
    push_sent TINYINT(1) DEFAULT 0,
    push_sent_at TIMESTAMP NULL,
    read_count INT DEFAULT 0,
    first_read_at TIMESTAMP NULL,
    last_read_at TIMESTAMP NULL,
    metadata JSON,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_notifications_member (member_id),
    INDEX idx_notifications_role (role_id),
    INDEX idx_notifications_type (type_id),
    INDEX idx_notifications_priority (priority),
    INDEX idx_notifications_scheduled (scheduled_at),
    INDEX idx_notifications_created (created_at),
    FOREIGN KEY (type_id) REFERENCES notification_types(id),
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `notifications`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
