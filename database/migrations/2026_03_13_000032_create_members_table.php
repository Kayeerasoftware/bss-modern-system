<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE members (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED UNIQUE NOT NULL,
    member_number VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(20),
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    full_name VARCHAR(300) GENERATED ALWAYS AS (
        CONCAT_WS(' ', 
            title,
            first_name,
            middle_name,
            last_name
        )
    ) STORED,
    primary_phone VARCHAR(50),
    primary_phone_country_id TINYINT UNSIGNED,
    alternative_phone VARCHAR(50),
    alternative_phone_country_id TINYINT UNSIGNED,
    whatsapp_phone VARCHAR(50),
    email VARCHAR(191),
    alternative_email VARCHAR(191),
    profile_picture VARCHAR(255),
    date_of_birth DATE,
    gender_id TINYINT UNSIGNED,
    nationality_id TINYINT UNSIGNED,
    place_of_birth VARCHAR(255),
    occupation VARCHAR(191),
    employer VARCHAR(191),
    employment_status_id TINYINT UNSIGNED,
    membership_status ENUM('active', 'inactive', 'suspended', 'terminated') DEFAULT 'active',
    status_reason TEXT,
    join_date DATE NOT NULL,
    exit_date DATE NULL,
    exit_reason TEXT,
    referred_by BIGINT UNSIGNED NULL,
    referral_code VARCHAR(50),
    preferred_language VARCHAR(10) DEFAULT 'en',
    notification_preferences JSON DEFAULT NULL,
    communication_preferences JSON DEFAULT NULL,
    emergency_contact_name VARCHAR(191),
    emergency_contact_relationship VARCHAR(100),
    emergency_contact_phone VARCHAR(50),
    notes TEXT,
    tags JSON,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    deleted_by BIGINT UNSIGNED NULL,
    deleted_reason TEXT,
    INDEX idx_members_user (user_id),
    INDEX idx_members_number (member_number),
    INDEX idx_members_name (last_name, first_name),
    INDEX idx_members_email (email),
    INDEX idx_members_phone (primary_phone),
    INDEX idx_members_status (membership_status),
    INDEX idx_members_referral (referred_by),
    INDEX idx_members_dob (date_of_birth),
    INDEX idx_members_join_date (join_date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (gender_id) REFERENCES genders(id),
    FOREIGN KEY (nationality_id) REFERENCES nationalities(id),
    FOREIGN KEY (employment_status_id) REFERENCES employment_statuses(id),
    FOREIGN KEY (referred_by) REFERENCES members(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id),
    FOREIGN KEY (primary_phone_country_id) REFERENCES nationalities(id),
    FOREIGN KEY (alternative_phone_country_id) REFERENCES nationalities(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `members`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
