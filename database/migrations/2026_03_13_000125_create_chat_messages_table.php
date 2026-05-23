<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE chat_messages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    message_number VARCHAR(50) UNIQUE NOT NULL,
    conversation_id BIGINT UNSIGNED NOT NULL,
    sender_id BIGINT UNSIGNED NOT NULL,
    message TEXT,
    message_type ENUM('text', 'image', 'file', 'audio', 'video', 'location', 'contact') DEFAULT 'text',
    attachment_path VARCHAR(255),
    attachment_name VARCHAR(255),
    attachment_type VARCHAR(50),
    attachment_size INT,
    thumbnail_path VARCHAR(255),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    location_name VARCHAR(255),
    reply_to_id BIGINT UNSIGNED NULL,
    forwarded_from_id BIGINT UNSIGNED NULL,
    forwarded_count INT DEFAULT 0,
    is_delivered TINYINT(1) DEFAULT 0,
    delivered_at TIMESTAMP NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at TIMESTAMP NULL,
    read_count INT DEFAULT 0,
    is_edited TINYINT(1) DEFAULT 0,
    edited_at TIMESTAMP NULL,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    deleted_by BIGINT UNSIGNED NULL,
    reactions JSON,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_messages_conversation (conversation_id, created_at),
    INDEX idx_messages_sender (sender_id),
    INDEX idx_messages_type (message_type),
    INDEX idx_messages_reply (reply_to_id),
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES members(id),
    FOREIGN KEY (reply_to_id) REFERENCES chat_messages(id),
    FOREIGN KEY (forwarded_from_id) REFERENCES chat_messages(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `chat_messages`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
