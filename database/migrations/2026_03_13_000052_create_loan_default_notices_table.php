<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE loan_default_notices (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_id BIGINT UNSIGNED NOT NULL,
    notice_number VARCHAR(50) UNIQUE NOT NULL,
    notice_type ENUM('reminder', 'warning', 'final', 'legal') NOT NULL,
    days_overdue INT NOT NULL,
    amount_overdue DECIMAL(15,2) NOT NULL,
    notice_date DATE NOT NULL,
    response_deadline DATE,
    sent_via JSON,
    sent_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    responded_at TIMESTAMP NULL,
    response TEXT,
    response_by BIGINT UNSIGNED NULL,
    document_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_default_notices_loan (loan_id),
    INDEX idx_default_notices_date (notice_date),
    FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY (response_by) REFERENCES members(id),
    FOREIGN KEY (document_id) REFERENCES documents(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `loan_default_notices`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
