<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE meetings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    meeting_number VARCHAR(50) UNIQUE NOT NULL,
    type_id TINYINT UNSIGNED NOT NULL,
    title VARCHAR(191) NOT NULL,
    description TEXT,
    agenda JSON,
    scheduled_at DATETIME NOT NULL,
    duration_minutes SMALLINT,
    end_time DATETIME GENERATED ALWAYS AS (scheduled_at + INTERVAL duration_minutes MINUTE) VIRTUAL,
    location VARCHAR(255),
    meeting_link VARCHAR(255),
    is_virtual TINYINT(1) DEFAULT 0,
    virtual_platform VARCHAR(100),
    access_code VARCHAR(100),
    status_id TINYINT UNSIGNED NOT NULL,
    max_attendees INT,
    current_attendees INT DEFAULT 0,
    waitlist_enabled TINYINT(1) DEFAULT 0,
    materials JSON,
    minutes TEXT,
    minutes_document_id BIGINT UNSIGNED NULL,
    recording_link VARCHAR(255),
    recording_password VARCHAR(255),
    decisions JSON,
    action_items JSON,
    is_recurring TINYINT(1) DEFAULT 0,
    recurrence_rule VARCHAR(255),
    parent_meeting_id BIGINT UNSIGNED NULL,
    reminders_sent JSON,
    notes TEXT,
    metadata JSON,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_meetings_number (meeting_number),
    INDEX idx_meetings_status (status_id),
    INDEX idx_meetings_type (type_id),
    INDEX idx_meetings_scheduled (scheduled_at),
    INDEX idx_meetings_virtual (is_virtual),
    FOREIGN KEY (type_id) REFERENCES meeting_types(id),
    FOREIGN KEY (status_id) REFERENCES meeting_statuses(id),
    FOREIGN KEY (minutes_document_id) REFERENCES documents(id),
    FOREIGN KEY (parent_meeting_id) REFERENCES meetings(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `meetings`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
