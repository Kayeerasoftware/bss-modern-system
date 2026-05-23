<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE document_categories (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    parent_id TINYINT UNSIGNED NULL,
    is_mandatory TINYINT(1) DEFAULT 0,
    expiry_months SMALLINT NULL,
    max_size_mb INT DEFAULT 10,
    allowed_types VARCHAR(255) DEFAULT 'pdf,jpg,jpeg,png',
    icon VARCHAR(50),
    sort_order TINYINT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (parent_id) REFERENCES document_categories(id),
    INDEX idx_doc_categories_parent (parent_id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `document_categories`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
