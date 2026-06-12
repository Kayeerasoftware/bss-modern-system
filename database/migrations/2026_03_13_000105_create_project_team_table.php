<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE project_team (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    role VARCHAR(100) NOT NULL,
    responsibilities TEXT,
    assigned_at DATE NOT NULL,
    assigned_by BIGINT UNSIGNED NOT NULL,
    released_at DATE NULL,
    released_by BIGINT UNSIGNED NULL,
    is_active TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_project_member (project_id, member_id, released_at),
    INDEX idx_project_team_project (project_id),
    INDEX idx_project_team_member (member_id),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (assigned_by) REFERENCES users(id),
    FOREIGN KEY (released_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `project_team`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
