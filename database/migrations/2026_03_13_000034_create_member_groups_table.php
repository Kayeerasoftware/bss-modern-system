<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE member_groups (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(191) NOT NULL,
    description TEXT,
    group_type VARCHAR(50),
    parent_id BIGINT UNSIGNED NULL,
    leader_id BIGINT UNSIGNED NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_groups_parent (parent_id),
    INDEX idx_groups_leader (leader_id),
    FOREIGN KEY (parent_id) REFERENCES member_groups(id),
    FOREIGN KEY (leader_id) REFERENCES members(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `member_groups`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
