<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
CREATE TABLE loan_guarantor_agreements (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    agreement_number VARCHAR(50) UNIQUE NOT NULL,
    agreed_amount DECIMAL(15,2) NOT NULL,
    agreed_date DATE NOT NULL,
    agreed_ip VARCHAR(45),
    agreement_document_id BIGINT UNSIGNED NULL,
    is_active TINYINT(1) DEFAULT 1,
    released_at TIMESTAMP NULL,
    released_reason TEXT,
    released_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_loan_guarantor (loan_id, member_id),
    INDEX idx_guarantor_loan (loan_id),
    INDEX idx_guarantor_member (member_id),
    FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (agreement_document_id) REFERENCES documents(id),
    FOREIGN KEY (released_by) REFERENCES users(id)
)
SQL);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `loan_guarantor_agreements`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
