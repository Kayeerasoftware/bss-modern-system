<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE documents (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    document_number VARCHAR(100) UNIQUE NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    category_id TINYINT UNSIGNED NOT NULL,
    name VARCHAR(191) NOT NULL,
    description TEXT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    file_size BIGINT UNSIGNED,
    mime_type VARCHAR(100),
    file_hash VARCHAR(255),
    issue_date DATE,
    expiry_date DATE,
    issuing_authority VARCHAR(191),
    document_number_id VARCHAR(100),
    country_of_issue_id TINYINT UNSIGNED,
    status_id TINYINT UNSIGNED NOT NULL DEFAULT 1,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    verification_notes TEXT,
    rejection_reason TEXT,
    access_level ENUM('public', 'private', 'confidential') DEFAULT 'private',
    encryption_key VARCHAR(255),
    is_encrypted TINYINT(1) DEFAULT 0,
    version INT DEFAULT 1,
    previous_version_id BIGINT UNSIGNED NULL,
    tags JSON,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    uploaded_ip VARCHAR(45),
    uploaded_user_agent TEXT,
    downloaded_count INT DEFAULT 0,
    last_downloaded_at TIMESTAMP NULL,
    notes TEXT,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    deleted_by BIGINT UNSIGNED NULL,
    deleted_reason TEXT,
    INDEX idx_documents_number (document_number),
    INDEX idx_documents_member (member_id),
    INDEX idx_documents_category (category_id),
    INDEX idx_documents_status (status_id),
    INDEX idx_documents_expiry (expiry_date),
    INDEX idx_documents_type (file_type),
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES document_categories(id),
    FOREIGN KEY (status_id) REFERENCES document_statuses(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    FOREIGN KEY (country_of_issue_id) REFERENCES nationalities(id),
    FOREIGN KEY (previous_version_id) REFERENCES documents(id),
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `documents`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
