<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE loan_types (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    min_amount DECIMAL(15,2),
    max_amount DECIMAL(15,2),
    default_interest_rate DECIMAL(5,2),
    min_repayment_months TINYINT,
    max_repayment_months TINYINT,
    requires_guarantors TINYINT(1) DEFAULT 0,
    guarantors_required TINYINT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `loan_types`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
