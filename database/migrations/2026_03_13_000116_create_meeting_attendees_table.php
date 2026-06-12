<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE meeting_attendees (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    meeting_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    invited_by BIGINT UNSIGNED NOT NULL,
    invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    invitation_sent_at TIMESTAMP NULL,
    invitation_response ENUM('pending', 'confirmed', 'declined', 'tentative') DEFAULT 'pending',
    responded_at TIMESTAMP NULL,
    response_notes TEXT,
    attendance_status_id TINYINT UNSIGNED NULL,
    check_in_time TIMESTAMP NULL,
    check_in_method ENUM('manual', 'qr', 'face_recognition', 'self') NULL,
    check_out_time TIMESTAMP NULL,
    spoke_at TIMESTAMP NULL,
    duration_attended_minutes SMALLINT,
    feedback_rating TINYINT,
    feedback_comment TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_meeting_member (meeting_id, member_id),
    INDEX idx_attendees_meeting (meeting_id),
    INDEX idx_attendees_member (member_id),
    INDEX idx_attendees_response (invitation_response),
    INDEX idx_attendees_check_in (check_in_time),
    FOREIGN KEY (meeting_id) REFERENCES meetings(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (attendance_status_id) REFERENCES attendance_statuses(id),
    FOREIGN KEY (invited_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `meeting_attendees`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
