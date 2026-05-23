<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS `after_user_insert`');

        DB::unprepared(<<<'SQL'
CREATE TRIGGER after_user_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    DECLARE next_number INT;
    DECLARE member_num VARCHAR(50);
    SELECT COALESCE(MAX(CAST(SUBSTRING(COALESCE(member_account_number, member_number), -4) AS UNSIGNED)), 0) + 1
    INTO next_number FROM members
    WHERE COALESCE(member_account_number, member_number) LIKE 'BSS-C15-%';
    SET member_num = CONCAT('BSS-C15-', LPAD(next_number, 4, '0'));
    INSERT INTO members (
        user_id,
        member_number,
        member_account_number,
        first_name,
        last_name,
        email,
        join_date,
        created_by,
        created_at
    ) VALUES (
        NEW.id,
        member_num,
        member_num,
        NEW.username,
        NEW.username,
        NEW.email,
        CURDATE(),
        NEW.id,
        NOW()
    );
END
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS `after_user_insert`');
    }
};
