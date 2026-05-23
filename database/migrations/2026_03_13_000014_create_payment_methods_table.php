<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE payment_methods (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    processing_time VARCHAR(50),
    fee_percentage DECIMAL(5,2) DEFAULT 0,
    fee_fixed DECIMAL(15,2) DEFAULT 0,
    min_amount DECIMAL(15,2),
    max_amount DECIMAL(15,2),
    requires_reference TINYINT(1) DEFAULT 1,
    requires_approval TINYINT(1) DEFAULT 0,
    icon VARCHAR(50),
    is_active TINYINT(1) DEFAULT 1,
    sort_order TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `payment_methods`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
