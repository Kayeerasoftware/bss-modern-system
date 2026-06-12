<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE member_addresses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    member_id BIGINT UNSIGNED NOT NULL,
    address_type ENUM('present', 'permanent', 'business', 'postal', 'next_of_kin') NOT NULL,
    village_id MEDIUMINT UNSIGNED NOT NULL,
    street_address VARCHAR(255),
    landmark VARCHAR(255),
    building_name VARCHAR(191),
    floor_number VARCHAR(20),
    room_number VARCHAR(50),
    postal_code VARCHAR(20),
    postal_box VARCHAR(50),
    phone VARCHAR(50),
    alternative_phone VARCHAR(50),
    email VARCHAR(191),
    is_primary TINYINT(1) DEFAULT 0,
    is_verified TINYINT(1) DEFAULT 0,
    verified_at TIMESTAMP NULL,
    verified_by BIGINT UNSIGNED NULL,
    start_date DATE,
    end_date DATE,
    notes TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_member_address (member_id, address_type),
    INDEX idx_address_village (village_id),
    UNIQUE KEY unique_primary_address (member_id, address_type, is_primary),
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (village_id) REFERENCES villages(id),
    FOREIGN KEY (verified_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `member_addresses`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
