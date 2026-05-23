<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(191) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id TINYINT UNSIGNED NOT NULL,
    status ENUM('active', 'inactive', 'suspended', 'locked') DEFAULT 'active',
    status_reason TEXT,
    email_verified_at TIMESTAMP NULL,
    email_verification_token VARCHAR(100) NULL,
    password_reset_token VARCHAR(100) NULL,
    password_reset_expires TIMESTAMP NULL,
    two_factor_secret VARCHAR(255) NULL,
    two_factor_enabled TINYINT(1) DEFAULT 0,
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45),
    last_login_user_agent TEXT,
    login_count INT UNSIGNED DEFAULT 0,
    failed_login_attempts TINYINT DEFAULT 0,
    last_failed_login TIMESTAMP NULL,
    locked_until TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    api_token VARCHAR(100) NULL,
    api_token_expires TIMESTAMP NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_users_email (email),
    INDEX idx_users_username (username),
    INDEX idx_users_role (role_id),
    INDEX idx_users_status (status),
    INDEX idx_users_token (remember_token),
    INDEX idx_users_api_token (api_token),
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `users`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
