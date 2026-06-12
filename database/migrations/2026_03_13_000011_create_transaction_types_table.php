<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE transaction_types (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    impact ENUM('debit', 'credit') NOT NULL,
    requires_approval TINYINT(1) DEFAULT 0,
    affects_savings TINYINT(1) DEFAULT 1,
    affects_loan TINYINT(1) DEFAULT 0,
    affects_share TINYINT(1) DEFAULT 0,
    is_fee TINYINT(1) DEFAULT 0,
    is_taxable TINYINT(1) DEFAULT 0,
    color VARCHAR(20),
    icon VARCHAR(50),
    sort_order TINYINT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `transaction_types`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
