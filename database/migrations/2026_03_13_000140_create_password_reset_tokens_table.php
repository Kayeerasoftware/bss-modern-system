<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE password_reset_tokens (
    email VARCHAR(191) PRIMARY KEY,
    token VARCHAR(191) NOT NULL,
    created_at TIMESTAMP NULL,
    INDEX idx_tokens_token (token)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `password_reset_tokens`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
