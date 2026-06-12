<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE share_issues (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    issue_number VARCHAR(50) UNIQUE NOT NULL,
    share_class_id TINYINT UNSIGNED NOT NULL,
    issue_date DATE NOT NULL,
    total_shares INT UNSIGNED NOT NULL,
    price_per_share DECIMAL(10,2) NOT NULL,
    total_value DECIMAL(15,2) GENERATED ALWAYS AS (total_shares * price_per_share) STORED,
    available_shares INT UNSIGNED NOT NULL,
    reserved_shares INT UNSIGNED DEFAULT 0,
    min_purchase INT UNSIGNED,
    max_purchase INT UNSIGNED,
    opening_date DATE NOT NULL,
    closing_date DATE NULL,
    status ENUM('planned', 'open', 'closed', 'cancelled') DEFAULT 'planned',
    description TEXT,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_share_issues_class (share_class_id),
    INDEX idx_share_issues_status (status),
    FOREIGN KEY (share_class_id) REFERENCES share_classes(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `share_issues`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
