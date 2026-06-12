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
    SELECT COALESCE(MAX(CAST(SUBSTRING(member_number, -4) AS UNSIGNED)), 0) + 1
    INTO next_number FROM members
    WHERE member_number LIKE 'BSS-C15-%';
    SET member_num = CONCAT('BSS-C15-', LPAD(next_number, 4, '0'));
    INSERT INTO members (
        user_id, 
        member_number, 
        first_name,
        last_name,
        email,
        join_date,
        created_by,
        created_at
    ) VALUES (
        NEW.id,
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

        DB::statement('DROP TRIGGER IF EXISTS `after_transaction_complete`');
        DB::unprepared(<<<'SQL'
CREATE TRIGGER after_transaction_complete
AFTER UPDATE ON transactions
FOR EACH ROW
BEGIN
    DECLARE completed_status_id TINYINT;
    SELECT id INTO completed_status_id FROM transaction_statuses WHERE name = 'completed';
    IF NEW.status_id = completed_status_id AND OLD.status_id != completed_status_id THEN
        INSERT INTO audit_logs (
            log_number,
            action_type_id,
            entity_type_id,
            entity_id,
            entity_identifier,
            description,
            details,
            created_at
        ) VALUES (
            CONCAT('AUD-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0')),
            (SELECT id FROM audit_action_types WHERE name = 'update'),
            (SELECT id FROM entity_types WHERE name = 'transaction'),
            NEW.id,
            NEW.transaction_number,
            'Transaction completed',
            JSON_OBJECT('old_status_id', OLD.status_id, 'new_status_id', NEW.status_id),
            NOW()
        );
    END IF;
END
SQL);

        DB::statement('DROP TRIGGER IF EXISTS `before_member_address_insert`');
        DB::unprepared(<<<'SQL'
CREATE TRIGGER before_member_address_insert
BEFORE INSERT ON member_addresses
FOR EACH ROW
BEGIN
    IF NEW.is_primary = 1 THEN
        UPDATE member_addresses 
        SET is_primary = 0 
        WHERE member_id = NEW.member_id 
        AND address_type = NEW.address_type;
    END IF;
END
SQL);

        DB::statement('DROP TRIGGER IF EXISTS `before_member_address_update`');
        DB::unprepared(<<<'SQL'
CREATE TRIGGER before_member_address_update
BEFORE UPDATE ON member_addresses
FOR EACH ROW
BEGIN
    IF NEW.is_primary = 1 AND OLD.is_primary = 0 THEN
        UPDATE member_addresses 
        SET is_primary = 0 
        WHERE member_id = NEW.member_id 
        AND address_type = NEW.address_type
        AND id != NEW.id;
    END IF;
END
SQL);

        DB::statement('DROP TRIGGER IF EXISTS `after_loan_repayment`');
        DB::unprepared(<<<'SQL'
CREATE TRIGGER after_loan_repayment
AFTER INSERT ON loan_repayments
FOR EACH ROW
BEGIN
    UPDATE loans 
    SET 
        amount_paid = amount_paid + NEW.amount,
        last_payment_date = NEW.payment_date,
        last_payment_amount = NEW.amount,
        payments_made = payments_made + 1
    WHERE id = NEW.loan_id;
    UPDATE loans 
    SET 
        status_id = (SELECT id FROM loan_statuses WHERE name = 'completed'),
        completed_date = NEW.payment_date
    WHERE id = NEW.loan_id 
    AND amount_paid >= total_amount;
END
SQL);

        DB::statement('DROP TRIGGER IF EXISTS `before_user_delete`');
        DB::unprepared(<<<'SQL'
CREATE TRIGGER before_user_delete
BEFORE DELETE ON users
FOR EACH ROW
BEGIN
    DECLARE loan_count INT;
    DECLARE transaction_count INT;
    SELECT COUNT(*) INTO loan_count FROM loans WHERE member_id = OLD.id;
    SELECT COUNT(*) INTO transaction_count FROM transactions WHERE processed_by = OLD.id;
    IF loan_count > 0 OR transaction_count > 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Cannot delete user with existing loans or transactions. Consider deactivating instead.';
    END IF;
END
SQL);

        DB::statement('DROP TRIGGER IF EXISTS `after_transaction_insert`');
        DB::unprepared(<<<'SQL'
CREATE TRIGGER after_transaction_insert
AFTER INSERT ON transactions
FOR EACH ROW
BEGIN
    DECLARE notification_type_id TINYINT;
    SELECT id INTO notification_type_id FROM notification_types WHERE name = 'transaction';
    INSERT INTO notifications (
        notification_number,
        type_id,
        member_id,
        title,
        message,
        action_url,
        created_by,
        created_at
    ) VALUES (
        CONCAT('NOT-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0')),
        notification_type_id,
        NEW.member_id,
        'Transaction Processed',
        CONCAT('Your transaction of ', NEW.amount, ' has been processed.'),
        CONCAT('/transactions/', NEW.id),
        NEW.processed_by,
        NOW()
    );
END
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS `after_user_insert`');
        DB::statement('DROP TRIGGER IF EXISTS `before_member_delete`');
        DB::statement('DROP TRIGGER IF EXISTS `after_transaction_complete`');
        DB::statement('DROP TRIGGER IF EXISTS `before_member_address_insert`');
        DB::statement('DROP TRIGGER IF EXISTS `before_member_address_update`');
        DB::statement('DROP TRIGGER IF EXISTS `after_loan_repayment`');
        DB::statement('DROP TRIGGER IF EXISTS `before_user_delete`');
        DB::statement('DROP TRIGGER IF EXISTS `after_transaction_insert`');
    }
};
