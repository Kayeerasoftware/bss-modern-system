<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS `before_member_delete`');
        DB::unprepared(<<<'SQL'
CREATE TRIGGER before_member_delete
BEFORE DELETE ON members
FOR EACH ROW
BEGIN
    IF OLD.deleted_at IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Members cannot be deleted directly. Move the member to trash first.';
    END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS `before_member_delete`');
        DB::unprepared(<<<'SQL'
CREATE TRIGGER before_member_delete
BEFORE DELETE ON members
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Members cannot be deleted directly. Delete the associated user instead.';
END
SQL);
    }
};
