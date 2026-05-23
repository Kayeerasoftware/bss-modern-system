<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE meeting_guests (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    meeting_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(191) NOT NULL,
    email VARCHAR(191),
    phone VARCHAR(50),
    organization VARCHAR(191),
    title VARCHAR(191),
    invited_by BIGINT UNSIGNED NOT NULL,
    invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    invitation_sent_at TIMESTAMP NULL,
    invitation_response ENUM('pending', 'confirmed', 'declined', 'tentative') DEFAULT 'pending',
    responded_at TIMESTAMP NULL,
    checked_in_at TIMESTAMP NULL,
    checked_in_by BIGINT UNSIGNED NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_guests_meeting (meeting_id),
    INDEX idx_guests_response (invitation_response),
    FOREIGN KEY (meeting_id) REFERENCES meetings(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES users(id),
    FOREIGN KEY (checked_in_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `meeting_guests`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
