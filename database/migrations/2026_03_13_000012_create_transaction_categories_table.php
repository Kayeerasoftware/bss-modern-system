<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE transaction_categories (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    transaction_type_id TINYINT UNSIGNED NOT NULL,
    description VARCHAR(255),
    is_system TINYINT(1) DEFAULT 0,
    requires_reference TINYINT(1) DEFAULT 0,
    requires_approval TINYINT(1) DEFAULT 0,
    fee_percentage DECIMAL(5,2) DEFAULT 0,
    fee_fixed DECIMAL(15,2) DEFAULT 0,
    color VARCHAR(20),
    icon VARCHAR(50),
    sort_order TINYINT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_type_id) REFERENCES transaction_types(id),
    INDEX idx_categories_type (transaction_type_id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `transaction_categories`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
