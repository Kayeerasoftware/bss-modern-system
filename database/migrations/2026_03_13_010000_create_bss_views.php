<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_member_summary AS
SELECT 
    m.id,
    m.member_number,
    m.full_name,
    m.primary_phone,
    m.email,
    m.membership_status,
    m.join_date,
    r.name as primary_role,
    COALESCE((
        SELECT SUM(
            CASE WHEN tt.impact = 'credit' THEN t.amount 
                 ELSE -t.amount 
            END
        ) 
        FROM transactions t
        JOIN transaction_types tt ON t.transaction_type_id = tt.id
        JOIN transaction_statuses ts ON t.status_id = ts.id
        WHERE t.member_id = m.id 
        AND ts.name = 'completed'
    ), 0) as current_balance,
    COALESCE((
        SELECT COUNT(*) 
        FROM loans l 
        WHERE l.member_id = m.id 
        AND l.status_id = (SELECT id FROM loan_statuses WHERE name = 'disbursed')
    ), 0) as active_loans_count,
    COALESCE((
        SELECT SUM(balance_due) 
        FROM loans l 
        WHERE l.member_id = m.id 
        AND l.status_id = (SELECT id FROM loan_statuses WHERE name = 'disbursed')
    ), 0) as total_loan_balance,
    COALESCE((
        SELECT SUM(shares_count) 
        FROM shares s 
        WHERE s.member_id = m.id 
        AND s.status_id = (SELECT id FROM share_statuses WHERE name = 'active')
    ), 0) as total_shares,
    COALESCE((
        SELECT SUM(total_value) 
        FROM shares s 
        WHERE s.member_id = m.id 
        AND s.status_id = (SELECT id FROM share_statuses WHERE name = 'active')
    ), 0) as share_value,
    (
        SELECT MAX(created_at) 
        FROM transactions 
        WHERE member_id = m.id
    ) as last_transaction_date,
    (
        SELECT COUNT(*) 
        FROM notifications n
        JOIN notification_receipts nr ON n.id = nr.notification_id
        WHERE nr.member_id = m.id 
        AND nr.is_read = 0
    ) as unread_notifications
FROM members m
LEFT JOIN users u ON m.user_id = u.id
LEFT JOIN roles r ON u.role_id = r.id
SQL);

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_loan_details AS
SELECT 
    l.id,
    l.loan_number,
    m.member_number,
    m.full_name as member_name,
    m.primary_phone,
    lt.name as loan_type,
    l.principal_amount,
    l.interest_rate,
    l.total_interest,
    l.total_amount,
    l.repayment_months,
    l.monthly_payment,
    l.processing_fee,
    la.purpose as loan_purpose,
    ls.name as status,
    ls.color as status_color,
    l.application_date,
    l.approval_date,
    l.disbursement_date,
    l.maturity_date,
    l.amount_paid,
    l.balance_due,
    l.payments_made,
    l.payments_remaining,
    ROUND((l.amount_paid / l.total_amount * 100), 2) as repayment_percentage,
    l.is_defaulted,
    l.days_overdue,
    g1.full_name as guarantor1_name,
    g1.primary_phone as guarantor1_phone,
    g2.full_name as guarantor2_name,
    g2.primary_phone as guarantor2_phone,
    l.created_at,
    l.updated_at
FROM loans l
JOIN members m ON l.member_id = m.id
JOIN loan_types lt ON l.loan_type_id = lt.id
JOIN loan_statuses ls ON l.status_id = ls.id
LEFT JOIN loan_applications la ON l.application_id = la.id
LEFT JOIN members g1 ON l.guarantor1_id = g1.id
LEFT JOIN members g2 ON l.guarantor2_id = g2.id
SQL);

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_transaction_summary AS
SELECT 
    t.id,
    t.transaction_number,
    m.member_number,
    m.full_name as member_name,
    tt.name as transaction_type,
    tt.display_name as transaction_type_display,
    tc.name as category,
    tc.display_name as category_display,
    ts.name as status,
    t.amount,
    t.fee,
    t.tax_amount,
    t.net_amount,
    t.balance_before,
    t.balance_after,
    pm.name as payment_method,
    t.reference_number,
    t.receipt_number,
    t.description,
    t.transaction_date,
    t.value_date,
    t.is_reversal,
    t.reversal_reason,
    t.reconciled,
    u.username as processed_by_name,
    t.created_at
FROM transactions t
JOIN members m ON t.member_id = m.id
JOIN transaction_types tt ON t.transaction_type_id = tt.id
JOIN transaction_categories tc ON t.category_id = tc.id
JOIN transaction_statuses ts ON t.status_id = ts.id
LEFT JOIN payment_methods pm ON t.payment_method_id = pm.id
LEFT JOIN users u ON t.processed_by = u.id
SQL);

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_dashboard_stats AS
SELECT
    (SELECT COUNT(*) FROM members WHERE deleted_at IS NULL) as total_members,
    (SELECT COUNT(*) FROM members WHERE membership_status = 'active') as active_members,
    (SELECT COUNT(*) FROM members WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_members_30d,
    COALESCE((
        SELECT SUM(
            CASE WHEN tt.impact = 'credit' THEN t.amount 
                 ELSE -t.amount 
            END
        ) 
        FROM transactions t
        JOIN transaction_types tt ON t.transaction_type_id = tt.id
        JOIN transaction_statuses ts_dash ON t.status_id = ts_dash.id
        WHERE ts_dash.name = 'completed'
    ), 0) as total_system_balance,
    COALESCE((
        SELECT SUM(t.amount) 
        FROM transactions t
        JOIN transaction_statuses ts_vol30 ON t.status_id = ts_vol30.id
        WHERE ts_vol30.name = 'completed'
        AND t.transaction_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ), 0) as transaction_volume_30d,
    (SELECT COUNT(*) FROM transactions WHERE transaction_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as transaction_count_30d,
    COALESCE((
        SELECT SUM(principal_amount) 
        FROM loans 
        WHERE status_id = (SELECT id FROM loan_statuses WHERE name = 'disbursed')
    ), 0) as total_active_loans,
    (SELECT COUNT(*) FROM loans WHERE status_id = (SELECT id FROM loan_statuses WHERE name = 'pending')) as pending_loans_count,
    (SELECT COUNT(*) FROM loans WHERE status_id = (SELECT id FROM loan_statuses WHERE name = 'disbursed')) as active_loans_count,
    (SELECT COUNT(*) FROM loans WHERE is_defaulted = 1) as defaulted_loans_count,
    COALESCE((
        SELECT SUM(shares_count) 
        FROM shares 
        WHERE status_id = (SELECT id FROM share_statuses WHERE name = 'active')
    ), 0) as total_shares_issued,
    COALESCE((
        SELECT SUM(total_value) 
        FROM shares 
        WHERE status_id = (SELECT id FROM share_statuses WHERE name = 'active')
    ), 0) as total_share_value,
    (SELECT COUNT(*) FROM projects WHERE status_id = (SELECT id FROM project_statuses WHERE name = 'active')) as active_projects,
    (SELECT COUNT(*) FROM meetings WHERE scheduled_at > NOW() AND scheduled_at < DATE_ADD(NOW(), INTERVAL 7 DAY)) as upcoming_meetings_7d,
    (SELECT COUNT(*) FROM users WHERE last_login_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as active_users_7d,
    (SELECT COUNT(*) FROM sessions) as active_sessions,
    NOW() as as_of
SQL);

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_member_financial_report AS
SELECT 
    m.id,
    m.member_number,
    m.full_name,
    m.primary_phone,
    m.email,
    COALESCE((
        SELECT SUM(
            CASE WHEN tt.impact = 'credit' THEN t.amount 
                 ELSE -t.amount 
            END
        ) 
        FROM transactions t
        JOIN transaction_types tt ON t.transaction_type_id = tt.id
        JOIN transaction_statuses ts_mfr ON t.status_id = ts_mfr.id
        WHERE t.member_id = m.id 
        AND ts_mfr.name = 'completed'
        AND tt.affects_savings = 1
    ), 0) as total_savings,
    COALESCE((
        SELECT SUM(balance_due) 
        FROM loans 
        WHERE member_id = m.id 
        AND status_id IN (
            SELECT id FROM loan_statuses 
            WHERE name IN ('disbursed', 'approved')
        )
    ), 0) as outstanding_loans,
    COALESCE((
        SELECT SUM(total_value) 
        FROM shares 
        WHERE member_id = m.id 
        AND status_id = (SELECT id FROM share_statuses WHERE name = 'active')
    ), 0) as share_value,
    COALESCE((
        SELECT SUM(net_amount) 
        FROM member_dividends 
        WHERE member_id = m.id 
        AND status = 'paid'
    ), 0) as total_dividends,
    COALESCE((
        SELECT SUM(
            CASE WHEN tt.impact = 'credit' THEN t.amount 
                 ELSE -t.amount 
            END
        ) 
        FROM transactions t
        JOIN transaction_types tt ON t.transaction_type_id = tt.id
        JOIN transaction_statuses ts_net ON t.status_id = ts_net.id
        WHERE t.member_id = m.id 
        AND ts_net.name = 'completed'
    ), 0) - COALESCE((
        SELECT SUM(balance_due) 
        FROM loans 
        WHERE member_id = m.id 
        AND status_id IN (
            SELECT id FROM loan_statuses 
            WHERE name IN ('disbursed', 'approved')
        )
    ), 0) as net_worth
FROM members m
SQL);

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_loan_performance AS
SELECT 
    l.id,
    l.loan_number,
    m.member_number,
    m.full_name as member_name,
    lt.name as loan_type,
    l.principal_amount,
    l.interest_rate,
    l.total_amount,
    l.repayment_months,
    l.monthly_payment,
    ls.name as status,
    l.application_date,
    l.approval_date,
    l.disbursement_date,
    l.maturity_date,
    l.amount_paid,
    l.balance_due,
    l.payments_made,
    l.payments_remaining,
    ROUND(l.amount_paid / l.total_amount * 100, 2) as repayment_percentage,
    l.is_defaulted,
    l.days_overdue,
    CASE 
        WHEN l.is_defaulted = 1 THEN 'Defaulted'
        WHEN l.balance_due = 0 THEN 'Fully Paid'
        WHEN l.days_overdue > 30 THEN 'At Risk'
        WHEN l.days_overdue > 0 THEN 'Late'
        WHEN l.disbursement_date IS NOT NULL AND l.balance_due > 0 THEN 'Performing'
        ELSE 'Pending'
    END as performance_status
FROM loans l
JOIN members m ON l.member_id = m.id
JOIN loan_types lt ON l.loan_type_id = lt.id
JOIN loan_statuses ls ON l.status_id = ls.id
SQL);

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_transaction_volume AS
SELECT 
    DATE(t.transaction_date) as transaction_date,
    tt.name as transaction_type,
    tc.name as category,
    COUNT(*) as transaction_count,
    SUM(t.amount) as total_amount,
    SUM(t.fee) as total_fees,
    AVG(t.amount) as average_amount,
    COUNT(DISTINCT t.member_id) as unique_members
FROM transactions t
JOIN transaction_types tt ON t.transaction_type_id = tt.id
JOIN transaction_categories tc ON t.category_id = tc.id
JOIN transaction_statuses ts_vol ON t.status_id = ts_vol.id
WHERE ts_vol.name = 'completed'
GROUP BY DATE(t.transaction_date), tt.name, tc.name
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS `v_member_summary`');
        DB::statement('DROP VIEW IF EXISTS `v_loan_details`');
        DB::statement('DROP VIEW IF EXISTS `v_transaction_summary`');
        DB::statement('DROP VIEW IF EXISTS `v_dashboard_stats`');
        DB::statement('DROP VIEW IF EXISTS `v_member_financial_report`');
        DB::statement('DROP VIEW IF EXISTS `v_loan_performance`');
        DB::statement('DROP VIEW IF EXISTS `v_transaction_volume`');
    }
};
