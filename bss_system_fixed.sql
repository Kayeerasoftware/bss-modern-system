-- =====================================================
-- BSS SYSTEM DATABASE - FIXED VERSION
-- Fixes applied:
--   1. SET FOREIGN_KEY_CHECKS=0 to handle circular/forward FKs
--   2. Renamed password_hash → password for Laravel Auth
--   3. Fixed views using t.status → proper JOIN on transaction_statuses
--   4. Fixed v_loan_details: removed l.purpose (not in loans table)
--   5. Created pending_transactions table (referenced in savings_accounts)
--   6. All GENERATED columns documented for Laravel model $guarded
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- Drop and recreate database
DROP DATABASE IF EXISTS bss_system;
CREATE DATABASE bss_system;
USE bss_system;

-- =====================================================
-- LARAVEL MODEL NOTES FOR GENERATED/STORED COLUMNS
-- =====================================================
-- The following columns are GENERATED (computed by MySQL).
-- In each Laravel Eloquent model, add them to $guarded or 
-- mark them as non-fillable so Eloquent doesn't try to INSERT/UPDATE them.
--
-- Member model:        full_name
-- Transaction model:   net_amount
-- LoanApplication:     requested_installment, debt_to_income_ratio
-- Loan model:          interest_amount, total_amount, monthly_payment,
--                      installment_amount, total_fees, maturity_date,
--                      payments_remaining, balance_due
-- LoanRepaymentSchedule: total_due, total_paid
-- ShareIssue:          total_value
-- SharePurchase:       total_amount
-- Share model:         total_value
-- ShareTransfer:       total_amount
-- Dividend:            total_amount
-- MemberDividend:      gross_amount, net_amount
-- Project:             remaining_budget
-- ProjectRisk:         risk_score
-- InvestmentReturn:    net_amount
-- SavingsAccount:      available_balance
-- SavingsInterestAccrual: net_amount
-- Meeting:             end_time (VIRTUAL)
--
-- Example in model:
--   protected $guarded = ['id', 'full_name', 'net_amount', ...];
--   OR use: protected $fillable = [...]; (explicit whitelist)
-- =====================================================


-- =====================================================
-- LOOKUP TABLES (Complete with all possible values)
-- =====================================================

-- Roles
CREATE TABLE roles (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    priority TINYINT DEFAULT 0, -- Higher number = more privileges
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);

-- Gender
CREATE TABLE genders (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    abbreviation CHAR(1)
);

-- Marital Status
CREATE TABLE marital_statuses (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255)
);

-- Employment Status
CREATE TABLE employment_statuses (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255)
);

-- Nationalities
CREATE TABLE nationalities (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    code CHAR(2),
    dial_code VARCHAR(10)
);

-- Loan Statuses
CREATE TABLE loan_statuses (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    color VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1,
    sort_order TINYINT DEFAULT 0
);

-- Loan Types
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
);

-- Share Classes
CREATE TABLE share_classes (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    par_value DECIMAL(10,2) NOT NULL,
    min_purchase INT UNSIGNED,
    max_purchase INT UNSIGNED,
    voting_rights TINYINT(1) DEFAULT 1,
    dividend_priority TINYINT DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1
);

-- Share Statuses
CREATE TABLE share_statuses (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    color VARCHAR(20)
);

-- Dividend Statuses
CREATE TABLE dividend_statuses (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    color VARCHAR(20)
);

-- Transaction Types
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
);

-- Transaction Categories
CREATE TABLE transaction_categories (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    transaction_type_id TINYINT UNSIGNED NOT NULL,
    description VARCHAR(255),
    is_system TINYINT(1) DEFAULT 0, -- Cannot be modified/deleted
    requires_reference TINYINT(1) DEFAULT 0,
    requires_approval TINYINT(1) DEFAULT 0,
    fee_percentage DECIMAL(5,2) DEFAULT 0,
    fee_fixed DECIMAL(15,2) DEFAULT 0,
    color VARCHAR(20),
    icon VARCHAR(50),
    sort_order TINYINT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (transaction_type_id) REFERENCES transaction_types(id),
    INDEX idx_categories_type (transaction_type_id)
);

-- Transaction Statuses
CREATE TABLE transaction_statuses (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    color VARCHAR(20),
    is_final TINYINT(1) DEFAULT 0, -- Cannot be changed once reached
    sort_order TINYINT DEFAULT 0
);

-- Payment Methods
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
);

-- Currencies
CREATE TABLE currencies (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    code CHAR(3) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    symbol VARCHAR(10),
    decimal_places TINYINT DEFAULT 2,
    is_base TINYINT(1) DEFAULT 0,
    exchange_rate DECIMAL(10,4) DEFAULT 1.0000,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);

-- Notification Types
CREATE TABLE notification_types (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    icon VARCHAR(50),
    color VARCHAR(20),
    email_template VARCHAR(255),
    sms_template VARCHAR(255),
    in_app_template VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1
);

-- Document Categories
CREATE TABLE document_categories (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    parent_id TINYINT UNSIGNED NULL,
    is_mandatory TINYINT(1) DEFAULT 0,
    expiry_months SMALLINT NULL, -- Months until expiry, NULL if never expires
    max_size_mb INT DEFAULT 10,
    allowed_types VARCHAR(255) DEFAULT 'pdf,jpg,jpeg,png',
    icon VARCHAR(50),
    sort_order TINYINT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    
    FOREIGN KEY (parent_id) REFERENCES document_categories(id),
    INDEX idx_doc_categories_parent (parent_id)
);

-- Document Statuses
CREATE TABLE document_statuses (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    color VARCHAR(20)
);

-- Project Statuses
CREATE TABLE project_statuses (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    color VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1
);

-- Project Categories
CREATE TABLE project_categories (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1
);

-- Meeting Statuses
CREATE TABLE meeting_statuses (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    color VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1
);

-- Meeting Types
CREATE TABLE meeting_types (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    default_duration_minutes SMALLINT,
    color VARCHAR(20),
    icon VARCHAR(50),
    is_active TINYINT(1) DEFAULT 1
);

-- Attendance Statuses
CREATE TABLE attendance_statuses (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    color VARCHAR(20)
);

-- Fundraising Statuses
CREATE TABLE fundraising_statuses (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    color VARCHAR(20)
);

-- Fundraising Categories
CREATE TABLE fundraising_categories (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1
);

-- Savings Plan Types
CREATE TABLE savings_plan_types (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    min_balance DECIMAL(15,2) DEFAULT 0,
    interest_rate DECIMAL(5,2) DEFAULT 0,
    interest_calculation ENUM('daily', 'monthly', 'quarterly', 'annually') DEFAULT 'monthly',
    withdrawal_fee_percentage DECIMAL(5,2) DEFAULT 0,
    withdrawal_fee_fixed DECIMAL(15,2) DEFAULT 0,
    min_withdrawal DECIMAL(15,2),
    max_withdrawal DECIMAL(15,2),
    withdrawal_limit_period ENUM('day', 'week', 'month') NULL,
    withdrawal_limit_count TINYINT NULL,
    is_taxable TINYINT(1) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Investment Risk Levels
CREATE TABLE investment_risk_levels (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    color VARCHAR(20),
    score_range VARCHAR(20),
    sort_order TINYINT DEFAULT 0
);

-- Investment Statuses
CREATE TABLE investment_statuses (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    color VARCHAR(20)
);

-- Audit Action Types
CREATE TABLE audit_action_types (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    severity ENUM('info', 'warning', 'critical') DEFAULT 'info'
);

-- Entity Types (for audit logging)
CREATE TABLE entity_types (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    table_name VARCHAR(100)
);

-- =====================================================
-- USER & MEMBER MANAGEMENT (With Public Identifiers)
-- =====================================================

-- Users table (authentication & system access)
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(191) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Laravel Auth requires this column name
    role_id TINYINT UNSIGNED NOT NULL,
    
    -- Account Status
    status ENUM('active', 'inactive', 'suspended', 'locked') DEFAULT 'active',
    status_reason TEXT,
    email_verified_at TIMESTAMP NULL,
    email_verification_token VARCHAR(100) NULL,
    password_reset_token VARCHAR(100) NULL,
    password_reset_expires TIMESTAMP NULL,
    two_factor_secret VARCHAR(255) NULL,
    two_factor_enabled TINYINT(1) DEFAULT 0,
    
    -- Login Tracking
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45),
    last_login_user_agent TEXT,
    login_count INT UNSIGNED DEFAULT 0,
    failed_login_attempts TINYINT DEFAULT 0,
    last_failed_login TIMESTAMP NULL,
    locked_until TIMESTAMP NULL,
    
    -- Security
    remember_token VARCHAR(100) NULL,
    api_token VARCHAR(100) NULL,
    api_token_expires TIMESTAMP NULL,
    
    -- Metadata
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_users_email (email),
    INDEX idx_users_username (username),
    INDEX idx_users_role (role_id),
    INDEX idx_users_status (status),
    INDEX idx_users_token (remember_token),
    INDEX idx_users_api_token (api_token),
    
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Members table (profile & group information) - Internal ID is BIGINT, public member_number is VARCHAR
CREATE TABLE members (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED UNIQUE NOT NULL,
    member_number VARCHAR(50) UNIQUE NOT NULL, -- Format: "BSS-C15-0001"
    
    -- Personal Information
    title VARCHAR(20), -- Mr, Mrs, Dr, etc.
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    full_name VARCHAR(300) GENERATED ALWAYS AS (
        CONCAT_WS(' ', 
            title,
            first_name,
            middle_name,
            last_name
        )
    ) STORED,
    
    -- Contact Information
    primary_phone VARCHAR(50),
    primary_phone_country_id TINYINT UNSIGNED,
    alternative_phone VARCHAR(50),
    alternative_phone_country_id TINYINT UNSIGNED,
    whatsapp_phone VARCHAR(50),
    email VARCHAR(191),
    alternative_email VARCHAR(191),
    
    -- Personal Details
    profile_picture VARCHAR(255),
    date_of_birth DATE,
    gender_id TINYINT UNSIGNED,
    nationality_id TINYINT UNSIGNED,
    place_of_birth VARCHAR(255),
    
    -- Professional
    occupation VARCHAR(191),
    employer VARCHAR(191),
    employment_status_id TINYINT UNSIGNED,
    
    -- Status
    membership_status ENUM('active', 'inactive', 'suspended', 'terminated') DEFAULT 'active',
    status_reason TEXT,
    join_date DATE NOT NULL,
    exit_date DATE NULL,
    exit_reason TEXT,
    
    -- Referral
    referred_by BIGINT UNSIGNED NULL,
    referral_code VARCHAR(50),
    
    -- Preferences
    preferred_language VARCHAR(10) DEFAULT 'en',
    notification_preferences JSON DEFAULT NULL,
    communication_preferences JSON DEFAULT NULL,
    
    -- Emergency Contact (quick access)
    emergency_contact_name VARCHAR(191),
    emergency_contact_relationship VARCHAR(100),
    emergency_contact_phone VARCHAR(50),
    
    -- Metadata
    notes TEXT,
    tags JSON,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    deleted_by BIGINT UNSIGNED NULL,
    deleted_reason TEXT,
    
    INDEX idx_members_user (user_id),
    INDEX idx_members_number (member_number),
    INDEX idx_members_name (last_name, first_name),
    INDEX idx_members_email (email),
    INDEX idx_members_phone (primary_phone),
    INDEX idx_members_status (membership_status),
    INDEX idx_members_referral (referred_by),
    INDEX idx_members_dob (date_of_birth),
    INDEX idx_members_join_date (join_date),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (gender_id) REFERENCES genders(id),
    FOREIGN KEY (nationality_id) REFERENCES nationalities(id),
    FOREIGN KEY (employment_status_id) REFERENCES employment_statuses(id),
    FOREIGN KEY (referred_by) REFERENCES members(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id),
    FOREIGN KEY (primary_phone_country_id) REFERENCES nationalities(id),
    FOREIGN KEY (alternative_phone_country_id) REFERENCES nationalities(id)
);

-- Trigger to automatically create member when user is created

-- =====================================================
-- TRIGGER WARNING FOR LARAVEL DEVELOPERS
-- =====================================================
-- The trigger 'after_user_insert' auto-creates a member record
-- every time a user is inserted. This WILL fire during:
--   - php artisan db:seed
--   - UserFactory::create() in tests
--   - Any direct users table INSERT
--
-- To avoid conflicts in tests, either:
--   1. Disable trigger during seeding: SET @DISABLE_TRIGGER = 1;
--      (and add IF @DISABLE_TRIGGER IS NULL check to trigger)
--   2. Or truncate members after UserFactory calls
--   3. Or use DatabaseTransactions trait in tests
-- =====================================================

DELIMITER $$

CREATE TRIGGER after_user_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    DECLARE next_number INT;
    DECLARE member_num VARCHAR(50);
    DECLARE year_prefix CHAR(2);
    
    SET year_prefix = DATE_FORMAT(NOW(), '%y');
    
    -- Generate member number with format: BSS-YY-XXXX (BSS-24-0001)
    SELECT COALESCE(MAX(CAST(SUBSTRING(member_number, 8) AS UNSIGNED)), 0) + 1 
    INTO next_number FROM members;
    
    SET member_num = CONCAT('BSS-', year_prefix, '-', LPAD(next_number, 4, '0'));
    
    -- Create corresponding member record
    INSERT INTO members (
        user_id, 
        member_number, 
        first_name,
        last_name,
        full_name,
        email,
        join_date,
        created_by,
        created_at
    ) VALUES (
        NEW.id,
        member_num,
        NEW.username,
        NEW.username,
        NEW.username,
        NEW.email,
        CURDATE(),
        NEW.id,
        NOW()
    );
END$$

-- Trigger to prevent direct member deletion
CREATE TRIGGER before_member_delete
BEFORE DELETE ON members
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' 
    SET MESSAGE_TEXT = 'Members cannot be deleted directly. Delete the associated user instead.';
END$$

DELIMITER ;

-- Member Roles (for multi-role users)
CREATE TABLE member_roles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    member_id BIGINT UNSIGNED NOT NULL,
    role_id TINYINT UNSIGNED NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    assigned_by BIGINT UNSIGNED NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    revoked_by BIGINT UNSIGNED NULL,
    revoked_at TIMESTAMP NULL,
    revoked_reason TEXT,
    
    UNIQUE KEY unique_member_role (member_id, role_id),
    INDEX idx_member_roles_member (member_id),
    INDEX idx_member_roles_role (role_id),
    
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id),
    FOREIGN KEY (revoked_by) REFERENCES users(id)
);

-- Member Groups (for organizing members)
CREATE TABLE member_groups (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(191) NOT NULL,
    description TEXT,
    group_type VARCHAR(50),
    parent_id BIGINT UNSIGNED NULL,
    leader_id BIGINT UNSIGNED NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_groups_parent (parent_id),
    INDEX idx_groups_leader (leader_id),
    
    FOREIGN KEY (parent_id) REFERENCES member_groups(id),
    FOREIGN KEY (leader_id) REFERENCES members(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

CREATE TABLE member_group_members (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    group_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    left_at TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1,
    notes TEXT,
    added_by BIGINT UNSIGNED NOT NULL,
    
    UNIQUE KEY unique_group_member (group_id, member_id, left_at),
    INDEX idx_group_members_group (group_id),
    INDEX idx_group_members_member (member_id),
    
    FOREIGN KEY (group_id) REFERENCES member_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (added_by) REFERENCES users(id)
);

-- =====================================================
-- LOCATION HIERARCHY (Complete Ugandan Structure)
-- =====================================================

CREATE TABLE regions (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE districts (
    id MEDIUMINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    region_id TINYINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    is_active TINYINT(1) DEFAULT 1,
    
    UNIQUE KEY unique_district (region_id, name),
    INDEX idx_districts_region (region_id),
    
    FOREIGN KEY (region_id) REFERENCES regions(id)
);

CREATE TABLE counties (
    id MEDIUMINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    district_id MEDIUMINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    is_active TINYINT(1) DEFAULT 1,
    
    UNIQUE KEY unique_county (district_id, name),
    INDEX idx_counties_district (district_id),
    
    FOREIGN KEY (district_id) REFERENCES districts(id)
);

CREATE TABLE subcounties (
    id MEDIUMINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    county_id MEDIUMINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    is_active TINYINT(1) DEFAULT 1,
    
    UNIQUE KEY unique_subcounty (county_id, name),
    INDEX idx_subcounties_county (county_id),
    
    FOREIGN KEY (county_id) REFERENCES counties(id)
);

CREATE TABLE parishes (
    id MEDIUMINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    subcounty_id MEDIUMINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    is_active TINYINT(1) DEFAULT 1,
    
    UNIQUE KEY unique_parish (subcounty_id, name),
    INDEX idx_parishes_subcounty (subcounty_id),
    
    FOREIGN KEY (subcounty_id) REFERENCES subcounties(id)
);

CREATE TABLE villages (
    id MEDIUMINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    parish_id MEDIUMINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    postal_code VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1,
    
    UNIQUE KEY unique_village (parish_id, name),
    INDEX idx_villages_parish (parish_id),
    
    FOREIGN KEY (parish_id) REFERENCES parishes(id)
);

-- Member Addresses
CREATE TABLE member_addresses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    member_id BIGINT UNSIGNED NOT NULL,
    address_type ENUM('present', 'permanent', 'business', 'postal', 'next_of_kin') NOT NULL,
    village_id MEDIUMINT UNSIGNED NOT NULL,
    
    -- Detailed Address
    street_address VARCHAR(255),
    landmark VARCHAR(255),
    building_name VARCHAR(191),
    floor_number VARCHAR(20),
    room_number VARCHAR(50),
    postal_code VARCHAR(20),
    postal_box VARCHAR(50),
    
    -- Contact for this address
    phone VARCHAR(50),
    alternative_phone VARCHAR(50),
    email VARCHAR(191),
    
    -- Status
    is_primary TINYINT(1) DEFAULT 0,
    is_verified TINYINT(1) DEFAULT 0,
    verified_at TIMESTAMP NULL,
    verified_by BIGINT UNSIGNED NULL,
    
    -- Dates
    start_date DATE,
    end_date DATE,
    
    -- Notes
    notes TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_member_address (member_id, address_type),
    INDEX idx_address_village (village_id),
    UNIQUE KEY unique_primary_address (member_id, address_type, is_primary),
    
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (village_id) REFERENCES villages(id),
    FOREIGN KEY (verified_by) REFERENCES users(id)
);

-- =====================================================
-- BIO-DATA (Complete Member Profile)
-- =====================================================

CREATE TABLE bio_data (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    member_id BIGINT UNSIGNED UNIQUE NOT NULL,
    
    -- Identity Documents
    nin_number VARCHAR(50) UNIQUE,
    nin_issue_date DATE,
    nin_expiry_date DATE,
    nin_verified TINYINT(1) DEFAULT 0,
    nin_verified_at TIMESTAMP NULL,
    
    passport_number VARCHAR(50) UNIQUE,
    passport_issue_date DATE,
    passport_expiry_date DATE,
    passport_country VARCHAR(100),
    passport_verified TINYINT(1) DEFAULT 0,
    
    driving_license VARCHAR(50) UNIQUE,
    license_issue_date DATE,
    license_expiry_date DATE,
    license_class VARCHAR(50),
    license_verified TINYINT(1) DEFAULT 0,
    
    voter_id VARCHAR(50) UNIQUE,
    voter_id_verified TINYINT(1) DEFAULT 0,
    
    -- Personal Details
    marital_status_id TINYINT UNSIGNED,
    wedding_date DATE,
    number_of_children TINYINT DEFAULT 0,
    
    -- Spouse Information
    spouse_name VARCHAR(191),
    spouse_nin VARCHAR(50),
    spouse_dob DATE,
    spouse_phone VARCHAR(50),
    spouse_email VARCHAR(191),
    spouse_occupation VARCHAR(191),
    spouse_employer VARCHAR(191),
    
    -- Parents Information
    father_name VARCHAR(191),
    father_alive TINYINT(1) DEFAULT 1,
    father_phone VARCHAR(50),
    father_occupation VARCHAR(191),
    father_address_id BIGINT UNSIGNED NULL,
    
    mother_name VARCHAR(191),
    mother_alive TINYINT(1) DEFAULT 1,
    mother_phone VARCHAR(50),
    mother_occupation VARCHAR(191),
    mother_address_id BIGINT UNSIGNED NULL,
    
    -- Next of Kin (Primary)
    next_of_kin_name VARCHAR(191) NOT NULL,
    next_of_kin_relationship VARCHAR(100) NOT NULL,
    next_of_kin_phone VARCHAR(50) NOT NULL,
    next_of_kin_email VARCHAR(191),
    next_of_kin_nin VARCHAR(50),
    next_of_kin_dob DATE,
    next_of_kin_occupation VARCHAR(191),
    next_of_kin_address_id BIGINT UNSIGNED,
    next_of_kin_is_minor TINYINT(1) DEFAULT 0,
    next_of_kin_guardian_name VARCHAR(191),
    
    -- Alternative Next of Kin
    alt_next_of_kin_name VARCHAR(191),
    alt_next_of_kin_relationship VARCHAR(100),
    alt_next_of_kin_phone VARCHAR(50),
    alt_next_of_kin_email VARCHAR(191),
    alt_next_of_kin_address_id BIGINT UNSIGNED,
    
    -- Children (Detailed JSON)
    children JSON DEFAULT NULL COMMENT 'Array of {name, dob, gender, school, guardian}',
    
    -- Education
    highest_education VARCHAR(191),
    school_name VARCHAR(191),
    school_address_id BIGINT UNSIGNED NULL,
    professional_qualifications JSON,
    
    -- Employment Details
    employment_status_id TINYINT UNSIGNED,
    employer_name VARCHAR(191),
    employer_address_id BIGINT UNSIGNED NULL,
    employer_phone VARCHAR(50),
    employer_email VARCHAR(191),
    job_title VARCHAR(191),
    job_description TEXT,
    employment_start_date DATE,
    employment_end_date DATE,
    monthly_income DECIMAL(15,2),
    income_frequency ENUM('daily', 'weekly', 'monthly', 'annually') DEFAULT 'monthly',
    other_income_sources JSON,
    
    -- Business Details (if self-employed)
    business_name VARCHAR(191),
    business_type VARCHAR(100),
    business_address_id BIGINT UNSIGNED NULL,
    business_phone VARCHAR(50),
    business_email VARCHAR(191),
    business_registration VARCHAR(100),
    business_license VARCHAR(100),
    business_start_date DATE,
    business_monthly_revenue DECIMAL(15,2),
    business_monthly_expenses DECIMAL(15,2),
    
    -- Banking Details
    bank_name VARCHAR(191),
    bank_branch VARCHAR(191),
    bank_account_number VARCHAR(100),
    bank_account_name VARCHAR(191),
    bank_verified TINYINT(1) DEFAULT 0,
    bank_verified_at TIMESTAMP NULL,
    
    bank_alternative JSON COMMENT 'Alternative bank accounts',
    
    -- Mobile Money
    mobile_money_number VARCHAR(50),
    mobile_money_provider ENUM('mtn', 'airtel', 'africell', 'lycamobile', 'other'),
    mobile_money_name VARCHAR(191),
    mobile_money_verified TINYINT(1) DEFAULT 0,
    mobile_money_verified_at TIMESTAMP NULL,
    
    -- Emergency Contact (different from next of kin)
    emergency_contact_name VARCHAR(191) NOT NULL,
    emergency_contact_relationship VARCHAR(100) NOT NULL,
    emergency_contact_phone VARCHAR(50) NOT NULL,
    emergency_contact_alternative VARCHAR(50),
    emergency_contact_email VARCHAR(191),
    emergency_contact_address_id BIGINT UNSIGNED NULL,
    
    -- Social Media
    social_media JSON COMMENT 'Object with facebook, twitter, linkedin, etc.',
    
    -- Health Information
    has_disability TINYINT(1) DEFAULT 0,
    disability_details TEXT,
    blood_group VARCHAR(5),
    allergies TEXT,
    medical_conditions TEXT,
    
    -- Declaration
    signature_path VARCHAR(255),
    signature_data TEXT,
    declaration_date DATE,
    declaration_ip VARCHAR(45),
    declaration_location VARCHAR(255),
    declaration_latitude DECIMAL(10,8),
    declaration_longitude DECIMAL(11,8),
    
    -- Consent
    consent_sms TINYINT(1) DEFAULT 1,
    consent_email TINYINT(1) DEFAULT 1,
    consent_whatsapp TINYINT(1) DEFAULT 0,
    consent_push TINYINT(1) DEFAULT 1,
    data_processing_consent TINYINT(1) DEFAULT 0,
    consent_date TIMESTAMP NULL,
    
    -- Verification
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    verification_notes TEXT,
    
    -- Metadata
    notes TEXT,
    tags JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    
    INDEX idx_bio_member (member_id),
    INDEX idx_bio_nin (nin_number),
    INDEX idx_bio_passport (passport_number),
    INDEX idx_bio_driving (driving_license),
    INDEX idx_bio_next_of_kin (next_of_kin_phone),
    INDEX idx_bio_emergency (emergency_contact_phone),
    
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (marital_status_id) REFERENCES marital_statuses(id),
    FOREIGN KEY (employment_status_id) REFERENCES employment_statuses(id),
    FOREIGN KEY (father_address_id) REFERENCES member_addresses(id),
    FOREIGN KEY (mother_address_id) REFERENCES member_addresses(id),
    FOREIGN KEY (next_of_kin_address_id) REFERENCES member_addresses(id),
    FOREIGN KEY (alt_next_of_kin_address_id) REFERENCES member_addresses(id),
    FOREIGN KEY (school_address_id) REFERENCES member_addresses(id),
    FOREIGN KEY (employer_address_id) REFERENCES member_addresses(id),
    FOREIGN KEY (business_address_id) REFERENCES member_addresses(id),
    FOREIGN KEY (emergency_contact_address_id) REFERENCES member_addresses(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- =====================================================
-- FINANCIAL CORE - TRANSACTIONS (Single Source of Truth)
-- =====================================================

-- Main Transactions Table (Unified Financial Ledger)
CREATE TABLE transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    transaction_number VARCHAR(50) UNIQUE NOT NULL, -- Format: TXN-YYYYMMDD-XXXXX
    member_id BIGINT UNSIGNED NOT NULL,
    
    -- Classification
    transaction_type_id TINYINT UNSIGNED NOT NULL,
    category_id TINYINT UNSIGNED NOT NULL,
    status_id TINYINT UNSIGNED NOT NULL,
    
    -- Financial Details
    amount DECIMAL(15,2) NOT NULL,
    fee DECIMAL(15,2) DEFAULT 0.00,
    tax_amount DECIMAL(15,2) DEFAULT 0.00,
    commission DECIMAL(15,2) DEFAULT 0.00,
    exchange_rate DECIMAL(10,4) DEFAULT 1.0000,
    currency_id TINYINT UNSIGNED NOT NULL,
    
    -- Net amount (calculated)
    net_amount DECIMAL(15,2) GENERATED ALWAYS AS (
        amount - fee - tax_amount - commission
    ) STORED,
    
    -- Balances at transaction time (for audit trail)
    balance_before DECIMAL(15,2) NOT NULL,
    balance_after DECIMAL(15,2) NOT NULL,
    
    -- Payment Details
    payment_method_id TINYINT UNSIGNED NOT NULL,
    reference_number VARCHAR(100),
    receipt_number VARCHAR(100),
    channel VARCHAR(50),
    terminal_id VARCHAR(50),
    
    -- Related Entities
    related_loan_id BIGINT UNSIGNED NULL,
    related_share_id BIGINT UNSIGNED NULL,
    related_dividend_id BIGINT UNSIGNED NULL,
    related_investment_id BIGINT UNSIGNED NULL,
    related_transfer_to_member_id BIGINT UNSIGNED NULL,
    
    -- Description
    description TEXT,
    notes TEXT,
    metadata JSON NULL COMMENT 'Additional flexible data',
    
    -- Approval
    requires_approval TINYINT(1) DEFAULT 0,
    approval_level TINYINT DEFAULT 0,
    current_approval_level TINYINT DEFAULT 0,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    approval_notes TEXT,
    
    -- Processing
    processed_by BIGINT UNSIGNED NOT NULL,
    processed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processed_ip VARCHAR(45),
    processed_location VARCHAR(255),
    
    -- Reversal Info
    is_reversal TINYINT(1) DEFAULT 0,
    parent_transaction_id BIGINT UNSIGNED NULL,
    reversed_at TIMESTAMP NULL,
    reversed_by BIGINT UNSIGNED NULL,
    reversal_reason TEXT,
    
    -- Reconciliation
    reconciled TINYINT(1) DEFAULT 0,
    reconciled_at TIMESTAMP NULL,
    reconciled_by BIGINT UNSIGNED NULL,
    reconciliation_notes TEXT,
    
    -- Schedule (for future/recurring)
    is_scheduled TINYINT(1) DEFAULT 0,
    scheduled_at TIMESTAMP NULL,
    schedule_rule VARCHAR(255),
    
    -- Dates
    transaction_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    value_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    deleted_by BIGINT UNSIGNED NULL,
    deleted_reason TEXT,
    
    -- Indexes
    INDEX idx_transactions_member (member_id),
    INDEX idx_transactions_type (transaction_type_id),
    INDEX idx_transactions_category (category_id),
    INDEX idx_transactions_status (status_id),
    INDEX idx_transactions_date (transaction_date),
    INDEX idx_transactions_reference (reference_number),
    INDEX idx_transactions_receipt (receipt_number),
    INDEX idx_transactions_related_loan (related_loan_id),
    INDEX idx_transactions_related_share (related_share_id),
    INDEX idx_transactions_related_dividend (related_dividend_id),
    INDEX idx_transactions_transfer (related_transfer_to_member_id),
    INDEX idx_transactions_reconciled (reconciled),
    INDEX idx_transactions_parent (parent_transaction_id),
    
    -- Foreign Keys
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (transaction_type_id) REFERENCES transaction_types(id),
    FOREIGN KEY (category_id) REFERENCES transaction_categories(id),
    FOREIGN KEY (status_id) REFERENCES transaction_statuses(id),
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id),
    FOREIGN KEY (currency_id) REFERENCES currencies(id),
    FOREIGN KEY (related_loan_id) REFERENCES loans(id),
    FOREIGN KEY (related_share_id) REFERENCES shares(id),
    FOREIGN KEY (related_dividend_id) REFERENCES member_dividends(id),
    FOREIGN KEY (related_investment_id) REFERENCES member_investments(id),
    FOREIGN KEY (related_transfer_to_member_id) REFERENCES members(id),
    FOREIGN KEY (processed_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (reversed_by) REFERENCES users(id),
    FOREIGN KEY (reconciled_by) REFERENCES users(id),
    FOREIGN KEY (parent_transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
);

-- Transaction Batches (for bulk processing)
CREATE TABLE transaction_batches (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    batch_number VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(191),
    description TEXT,
    total_transactions INT DEFAULT 0,
    total_amount DECIMAL(15,2) DEFAULT 0,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    created_by BIGINT UNSIGNED NOT NULL,
    processed_by BIGINT UNSIGNED NULL,
    processed_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    failed_at TIMESTAMP NULL,
    failure_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_batches_status (status),
    
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (processed_by) REFERENCES users(id)
);

CREATE TABLE batch_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    batch_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED UNIQUE NOT NULL,
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_batch_transactions_batch (batch_id),
    
    FOREIGN KEY (batch_id) REFERENCES transaction_batches(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE
);

-- =====================================================
-- LOANS MODULE
-- =====================================================

-- Loan Applications
CREATE TABLE loan_applications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    application_number VARCHAR(50) UNIQUE NOT NULL, -- Format: LAPP-YYYYMM-XXXX
    member_id BIGINT UNSIGNED NOT NULL,
    loan_type_id TINYINT UNSIGNED NOT NULL,
    
    -- Application Details
    requested_amount DECIMAL(15,2) NOT NULL,
    approved_amount DECIMAL(15,2) NULL,
    requested_tenure_months TINYINT NOT NULL,
    approved_tenure_months TINYINT NULL,
    purpose TEXT NOT NULL,
    applicant_comment TEXT,
    
    -- Financial Assessment
    monthly_income DECIMAL(15,2),
    monthly_expenses DECIMAL(15,2),
    existing_loan_commitments DECIMAL(15,2),
    requested_installment DECIMAL(15,2) GENERATED ALWAYS AS (
        requested_amount / requested_tenure_months
    ) STORED,
    debt_to_income_ratio DECIMAL(5,2) GENERATED ALWAYS AS (
        (existing_loan_commitments + (requested_amount / requested_tenure_months)) / monthly_income * 100
    ) STORED,
    
    -- Assessment Results
    credit_score SMALLINT,
    risk_rating VARCHAR(20),
    assessment_notes TEXT,
    assessed_by BIGINT UNSIGNED NULL,
    assessed_at TIMESTAMP NULL,
    
    -- Status
    status_id TINYINT UNSIGNED NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    decision_date TIMESTAMP NULL,
    decision_by BIGINT UNSIGNED NULL,
    decision_notes TEXT,
    rejection_reason TEXT,
    
    -- Approval Chain
    requires_approval TINYINT(1) DEFAULT 1,
    approval_level TINYINT DEFAULT 1,
    current_approval_level TINYINT DEFAULT 0,
    
    -- Linked Data
    converted_to_loan_id BIGINT UNSIGNED NULL,
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_loan_apps_member (member_id),
    INDEX idx_loan_apps_type (loan_type_id),
    INDEX idx_loan_apps_status (status_id),
    INDEX idx_loan_apps_submission (submission_date),
    
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (loan_type_id) REFERENCES loan_types(id),
    FOREIGN KEY (status_id) REFERENCES loan_statuses(id),
    FOREIGN KEY (assessed_by) REFERENCES users(id),
    FOREIGN KEY (decision_by) REFERENCES users(id),
    FOREIGN KEY (converted_to_loan_id) REFERENCES loans(id)
);

-- Loans
CREATE TABLE loans (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_number VARCHAR(50) UNIQUE NOT NULL, -- Format: LOAN-YYYYMM-XXXX
    application_id BIGINT UNSIGNED NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    loan_type_id TINYINT UNSIGNED NOT NULL,
    
    -- Loan Details
    principal_amount DECIMAL(15,2) NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,
    interest_type ENUM('fixed', 'declining', 'reducing') DEFAULT 'fixed',
    interest_amount DECIMAL(15,2) GENERATED ALWAYS AS (
        CASE interest_type
            WHEN 'fixed' THEN principal_amount * interest_rate / 100
            ELSE 0 -- Will be calculated by application
        END
    ) STORED,
    total_interest DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (
        principal_amount + total_interest
    ) STORED,
    
    -- Repayment Details
    repayment_months TINYINT NOT NULL,
    repayment_frequency ENUM('daily', 'weekly', 'monthly', 'quarterly') DEFAULT 'monthly',
    monthly_payment DECIMAL(15,2) GENERATED ALWAYS AS (
        (principal_amount + total_interest) / repayment_months
    ) STORED,
    installment_amount DECIMAL(15,2) GENERATED ALWAYS AS (
        (principal_amount + total_interest) / 
        CASE repayment_frequency
            WHEN 'daily' THEN repayment_months * 30
            WHEN 'weekly' THEN repayment_months * 4
            WHEN 'monthly' THEN repayment_months
            WHEN 'quarterly' THEN repayment_months / 3
        END
    ) STORED,
    
    -- Fees
    processing_fee DECIMAL(15,2) DEFAULT 0,
    processing_fee_transaction_id BIGINT UNSIGNED NULL,
    insurance_fee DECIMAL(15,2) DEFAULT 0,
    legal_fee DECIMAL(15,2) DEFAULT 0,
    other_fees DECIMAL(15,2) DEFAULT 0,
    total_fees DECIMAL(15,2) GENERATED ALWAYS AS (
        processing_fee + insurance_fee + legal_fee + other_fees
    ) STORED,
    
    -- Guarantors
    guarantor1_id BIGINT UNSIGNED NULL,
    guarantor1_agreed_at TIMESTAMP NULL,
    guarantor1_ip VARCHAR(45),
    guarantor2_id BIGINT UNSIGNED NULL,
    guarantor2_agreed_at TIMESTAMP NULL,
    guarantor2_ip VARCHAR(45),
    
    -- Collateral
    has_collateral TINYINT(1) DEFAULT 0,
    collateral_details JSON,
    
    -- Dates
    application_date DATE NOT NULL,
    approval_date DATE NULL,
    disbursement_date DATE NULL,
    first_payment_date DATE NULL,
    maturity_date DATE GENERATED ALWAYS AS (
        disbursement_date + INTERVAL repayment_months MONTH
    ) STORED,
    completed_date DATE NULL,
    
    -- Disbursement
    disbursement_transaction_id BIGINT UNSIGNED NULL,
    disbursement_method_id TINYINT UNSIGNED NULL,
    
    -- Tracking
    amount_paid DECIMAL(15,2) DEFAULT 0.00,
    last_payment_date DATE NULL,
    last_payment_amount DECIMAL(15,2) NULL,
    payments_made TINYINT DEFAULT 0,
    payments_remaining TINYINT GENERATED ALWAYS AS (repayment_months - payments_made) STORED,
    balance_due DECIMAL(15,2) GENERATED ALWAYS AS (total_amount - amount_paid) STORED,
    
    -- Status
    status_id TINYINT UNSIGNED NOT NULL,
    
    -- Audit
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    approved_ip VARCHAR(45),
    disbursed_by BIGINT UNSIGNED NULL,
    disbursed_at TIMESTAMP NULL,
    closed_by BIGINT UNSIGNED NULL,
    closed_at TIMESTAMP NULL,
    closed_reason VARCHAR(50), -- 'completed', 'defaulted', 'written_off'
    
    -- Default Tracking
    is_defaulted TINYINT(1) DEFAULT 0,
    defaulted_date DATE NULL,
    default_amount DECIMAL(15,2) NULL,
    days_overdue INT DEFAULT 0,
    last_reminder_sent TIMESTAMP NULL,
    
    -- Restructuring
    is_restructured TINYINT(1) DEFAULT 0,
    original_loan_id BIGINT UNSIGNED NULL,
    restructure_date DATE NULL,
    restructure_reason TEXT,
    
    -- Notes
    notes TEXT,
    metadata JSON,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    deleted_by BIGINT UNSIGNED NULL,
    deleted_reason TEXT,
    
    INDEX idx_loans_member (member_id),
    INDEX idx_loans_number (loan_number),
    INDEX idx_loans_type (loan_type_id),
    INDEX idx_loans_status (status_id),
    INDEX idx_loans_dates (application_date, disbursement_date, maturity_date),
    INDEX idx_loans_guarantor1 (guarantor1_id),
    INDEX idx_loans_guarantor2 (guarantor2_id),
    INDEX idx_loans_disbursement (disbursement_transaction_id),
    INDEX idx_loans_default (is_defaulted),
    
    FOREIGN KEY (application_id) REFERENCES loan_applications(id),
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (loan_type_id) REFERENCES loan_types(id),
    FOREIGN KEY (guarantor1_id) REFERENCES members(id),
    FOREIGN KEY (guarantor2_id) REFERENCES members(id),
    FOREIGN KEY (status_id) REFERENCES loan_statuses(id),
    FOREIGN KEY (disbursement_transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (disbursement_method_id) REFERENCES payment_methods(id),
    FOREIGN KEY (processing_fee_transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (disbursed_by) REFERENCES users(id),
    FOREIGN KEY (closed_by) REFERENCES users(id),
    FOREIGN KEY (original_loan_id) REFERENCES loans(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
);

-- Loan Repayments Schedule
CREATE TABLE loan_repayment_schedule (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_id BIGINT UNSIGNED NOT NULL,
    installment_number TINYINT NOT NULL,
    due_date DATE NOT NULL,
    
    -- Amounts
    principal_due DECIMAL(15,2) NOT NULL,
    interest_due DECIMAL(15,2) NOT NULL,
    fee_due DECIMAL(15,2) DEFAULT 0,
    total_due DECIMAL(15,2) GENERATED ALWAYS AS (
        principal_due + interest_due + fee_due
    ) STORED,
    
    -- Payments
    principal_paid DECIMAL(15,2) DEFAULT 0,
    interest_paid DECIMAL(15,2) DEFAULT 0,
    fee_paid DECIMAL(15,2) DEFAULT 0,
    total_paid DECIMAL(15,2) GENERATED ALWAYS AS (
        principal_paid + interest_paid + fee_paid
    ) STORED,
    
    -- Status
    is_paid TINYINT(1) DEFAULT 0,
    paid_date DATE NULL,
    paid_transaction_id BIGINT UNSIGNED NULL,
    
    -- Late Payment
    is_late TINYINT(1) DEFAULT 0,
    days_late INT DEFAULT 0,
    penalty_amount DECIMAL(15,2) DEFAULT 0,
    penalty_paid DECIMAL(15,2) DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_loan_installment (loan_id, installment_number),
    INDEX idx_schedule_loan (loan_id),
    INDEX idx_schedule_due_date (due_date),
    INDEX idx_schedule_paid (is_paid),
    
    FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY (paid_transaction_id) REFERENCES transactions(id)
);

-- Loan Repayments (actual payments)
CREATE TABLE loan_repayments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    repayment_number VARCHAR(50) UNIQUE NOT NULL,
    loan_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED UNIQUE NOT NULL,
    
    -- Payment Allocation
    amount DECIMAL(15,2) NOT NULL,
    principal_applied DECIMAL(15,2) NOT NULL,
    interest_applied DECIMAL(15,2) NOT NULL,
    fee_applied DECIMAL(15,2) DEFAULT 0,
    penalty_applied DECIMAL(15,2) DEFAULT 0,
    
    -- Payment Details
    payment_date DATE NOT NULL,
    applied_to_installments JSON, -- Which installments this payment covered
    
    -- Status
    is_early TINYINT(1) DEFAULT 0,
    is_late TINYINT(1) DEFAULT 0,
    days_late SMALLINT DEFAULT 0,
    
    -- Receipt
    receipt_number VARCHAR(100),
    receipt_issued_by BIGINT UNSIGNED NULL,
    receipt_issued_at TIMESTAMP NULL,
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_repayments_loan (loan_id),
    INDEX idx_repayments_date (payment_date),
    INDEX idx_repayments_transaction (transaction_id),
    
    FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (receipt_issued_by) REFERENCES users(id)
);

-- Loan Guarantor Agreements
CREATE TABLE loan_guarantor_agreements (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    agreement_number VARCHAR(50) UNIQUE NOT NULL,
    
    -- Agreement Details
    agreed_amount DECIMAL(15,2) NOT NULL,
    agreed_date DATE NOT NULL,
    agreed_ip VARCHAR(45),
    agreement_document_id BIGINT UNSIGNED NULL,
    
    -- Status
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
);

-- Loan Default Notices
CREATE TABLE loan_default_notices (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_id BIGINT UNSIGNED NOT NULL,
    notice_number VARCHAR(50) UNIQUE NOT NULL,
    notice_type ENUM('reminder', 'warning', 'final', 'legal') NOT NULL,
    
    -- Notice Details
    days_overdue INT NOT NULL,
    amount_overdue DECIMAL(15,2) NOT NULL,
    notice_date DATE NOT NULL,
    response_deadline DATE,
    
    -- Communication
    sent_via JSON, -- ['sms', 'email', 'letter']
    sent_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    
    -- Response
    responded_at TIMESTAMP NULL,
    response TEXT,
    response_by BIGINT UNSIGNED NULL,
    
    -- Document
    document_id BIGINT UNSIGNED NULL,
    
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_default_notices_loan (loan_id),
    INDEX idx_default_notices_date (notice_date),
    
    FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY (response_by) REFERENCES members(id),
    FOREIGN KEY (document_id) REFERENCES documents(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- =====================================================
-- SHARES MODULE
-- =====================================================

-- Share Issues (when company issues new shares)
CREATE TABLE share_issues (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    issue_number VARCHAR(50) UNIQUE NOT NULL,
    share_class_id TINYINT UNSIGNED NOT NULL,
    
    -- Issue Details
    issue_date DATE NOT NULL,
    total_shares INT UNSIGNED NOT NULL,
    price_per_share DECIMAL(10,2) NOT NULL,
    total_value DECIMAL(15,2) GENERATED ALWAYS AS (total_shares * price_per_share) STORED,
    
    -- Availability
    available_shares INT UNSIGNED NOT NULL,
    reserved_shares INT UNSIGNED DEFAULT 0,
    min_purchase INT UNSIGNED,
    max_purchase INT UNSIGNED,
    
    -- Dates
    opening_date DATE NOT NULL,
    closing_date DATE NULL,
    
    -- Status
    status ENUM('planned', 'open', 'closed', 'cancelled') DEFAULT 'planned',
    
    description TEXT,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_share_issues_class (share_class_id),
    INDEX idx_share_issues_status (status),
    
    FOREIGN KEY (share_class_id) REFERENCES share_classes(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Member Share Purchases
CREATE TABLE share_purchases (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    purchase_number VARCHAR(50) UNIQUE NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    share_issue_id BIGINT UNSIGNED NULL,
    share_class_id TINYINT UNSIGNED NOT NULL,
    
    -- Purchase Details
    shares_count INT UNSIGNED NOT NULL,
    price_per_share DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (shares_count * price_per_share) STORED,
    purchase_date DATE NOT NULL,
    
    -- Payment
    transaction_id BIGINT UNSIGNED NOT NULL,
    payment_method_id TINYINT UNSIGNED NOT NULL,
    is_fully_paid TINYINT(1) DEFAULT 1,
    payment_plan JSON NULL,
    
    -- Status
    status_id TINYINT UNSIGNED NOT NULL,
    
    -- Certificate
    certificate_number VARCHAR(100) UNIQUE NOT NULL,
    certificate_issued_date DATE,
    certificate_issued_by BIGINT UNSIGNED NULL,
    
    notes TEXT,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_share_purchases_member (member_id),
    INDEX idx_share_purchases_issue (share_issue_id),
    INDEX idx_share_purchases_class (share_class_id),
    INDEX idx_share_purchases_certificate (certificate_number),
    
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (share_issue_id) REFERENCES share_issues(id),
    FOREIGN KEY (share_class_id) REFERENCES share_classes(id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id),
    FOREIGN KEY (status_id) REFERENCES share_statuses(id),
    FOREIGN KEY (certificate_issued_by) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Shares (individual share records)
CREATE TABLE shares (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    share_number VARCHAR(50) UNIQUE NOT NULL,
    certificate_number VARCHAR(100) NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    share_class_id TINYINT UNSIGNED NOT NULL,
    purchase_id BIGINT UNSIGNED NOT NULL,
    
    -- Share Details
    shares_count INT UNSIGNED NOT NULL,
    purchase_price DECIMAL(10,2) NOT NULL,
    current_value DECIMAL(10,2) NOT NULL,
    total_value DECIMAL(15,2) GENERATED ALWAYS AS (shares_count * current_value) STORED,
    
    -- Dates
    purchase_date DATE NOT NULL,
    vesting_date DATE NULL,
    expiry_date DATE NULL,
    
    -- Status
    status_id TINYINT UNSIGNED NOT NULL,
    
    -- Transfer/Sale Info (if sold)
    sold_date DATE NULL,
    sold_price DECIMAL(10,2) NULL,
    sold_to_member_id BIGINT UNSIGNED NULL,
    sale_transaction_id BIGINT UNSIGNED NULL,
    
    -- Dividend Eligibility
    dividend_eligible TINYINT(1) DEFAULT 1,
    last_dividend_paid DATE NULL,
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_shares_member (member_id),
    INDEX idx_shares_certificate (certificate_number),
    INDEX idx_shares_class (share_class_id),
    INDEX idx_shares_purchase (purchase_id),
    INDEX idx_shares_status (status_id),
    
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (share_class_id) REFERENCES share_classes(id),
    FOREIGN KEY (purchase_id) REFERENCES share_purchases(id),
    FOREIGN KEY (status_id) REFERENCES share_statuses(id),
    FOREIGN KEY (sold_to_member_id) REFERENCES members(id),
    FOREIGN KEY (sale_transaction_id) REFERENCES transactions(id)
);

-- Share Transfers (between members)
CREATE TABLE share_transfers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    transfer_number VARCHAR(50) UNIQUE NOT NULL,
    share_id BIGINT UNSIGNED NOT NULL,
    from_member_id BIGINT UNSIGNED NOT NULL,
    to_member_id BIGINT UNSIGNED NOT NULL,
    
    -- Transfer Details
    shares_count INT UNSIGNED NOT NULL,
    transfer_price DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (shares_count * transfer_price) STORED,
    transfer_date DATE NOT NULL,
    
    -- Approval
    requires_approval TINYINT(1) DEFAULT 1,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    
    -- Transactions
    from_transaction_id BIGINT UNSIGNED NOT NULL,
    to_transaction_id BIGINT UNSIGNED NOT NULL,
    
    -- Documents
    transfer_document_id BIGINT UNSIGNED NULL,
    
    -- Status
    status ENUM('pending', 'approved', 'completed', 'rejected') DEFAULT 'pending',
    rejection_reason TEXT,
    
    notes TEXT,
    requested_by BIGINT UNSIGNED NOT NULL,
    processed_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_share_transfers_share (share_id),
    INDEX idx_share_transfers_from (from_member_id),
    INDEX idx_share_transfers_to (to_member_id),
    INDEX idx_share_transfers_status (status),
    
    FOREIGN KEY (share_id) REFERENCES shares(id),
    FOREIGN KEY (from_member_id) REFERENCES members(id),
    FOREIGN KEY (to_member_id) REFERENCES members(id),
    FOREIGN KEY (from_transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (to_transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (transfer_document_id) REFERENCES documents(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (requested_by) REFERENCES users(id),
    FOREIGN KEY (processed_by) REFERENCES users(id)
);

-- =====================================================
-- DIVIDENDS MODULE
-- =====================================================

-- Dividend Declarations
CREATE TABLE dividends (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dividend_number VARCHAR(50) UNIQUE NOT NULL,
    share_class_id TINYINT UNSIGNED NOT NULL,
    
    -- Dividend Details
    amount_per_share DECIMAL(10,2) NOT NULL,
    total_shares_eligible INT UNSIGNED NOT NULL,
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (amount_per_share * total_shares_eligible) STORED,
    
    -- Period
    year SMALLINT NOT NULL,
    quarter TINYINT,
    period_start DATE,
    period_end DATE,
    
    -- Dates
    declaration_date DATE NOT NULL,
    record_date DATE NOT NULL,
    payment_date DATE,
    
    -- Financials
    total_paid DECIMAL(15,2) DEFAULT 0,
    total_withheld DECIMAL(15,2) DEFAULT 0,
    withholding_tax_rate DECIMAL(5,2) DEFAULT 0,
    
    -- Status
    status_id TINYINT UNSIGNED NOT NULL,
    
    -- Approval
    declared_by BIGINT UNSIGNED NOT NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_dividends_class (share_class_id),
    INDEX idx_dividends_status (status_id),
    INDEX idx_dividends_period (year, quarter),
    
    FOREIGN KEY (share_class_id) REFERENCES share_classes(id),
    FOREIGN KEY (status_id) REFERENCES dividend_statuses(id),
    FOREIGN KEY (declared_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- Member Dividend Allocations
CREATE TABLE member_dividends (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dividend_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    
    -- Calculation
    shares_eligible INT UNSIGNED NOT NULL,
    amount_per_share DECIMAL(10,2) NOT NULL,
    gross_amount DECIMAL(15,2) GENERATED ALWAYS AS (shares_eligible * amount_per_share) STORED,
    withholding_tax DECIMAL(15,2) DEFAULT 0,
    net_amount DECIMAL(15,2) GENERATED ALWAYS AS (gross_amount - withholding_tax) STORED,
    
    -- Payment
    transaction_id BIGINT UNSIGNED NULL,
    paid_at TIMESTAMP NULL,
    paid_by BIGINT UNSIGNED NULL,
    
    -- Status
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    
    -- Payment Method
    payment_method_id TINYINT UNSIGNED NULL,
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_dividend_member (dividend_id, member_id),
    INDEX idx_member_dividends_member (member_id),
    INDEX idx_member_dividends_status (status),
    
    FOREIGN KEY (dividend_id) REFERENCES dividends(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (paid_by) REFERENCES users(id),
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
);

-- =====================================================
-- SAVINGS MODULE
-- =====================================================

-- Savings Plans
CREATE TABLE savings_plans (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    plan_type_id TINYINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    
    -- Financial Terms
    minimum_balance DECIMAL(15,2) DEFAULT 0,
    interest_rate DECIMAL(5,2) DEFAULT 0,
    interest_calculation ENUM('daily', 'monthly', 'quarterly', 'annually') DEFAULT 'monthly',
    interest_payout ENUM('compound', 'withdrawable') DEFAULT 'compound',
    
    -- Fees
    monthly_fee DECIMAL(15,2) DEFAULT 0,
    withdrawal_fee_percentage DECIMAL(5,2) DEFAULT 0,
    withdrawal_fee_fixed DECIMAL(15,2) DEFAULT 0,
    early_withdrawal_penalty DECIMAL(5,2) DEFAULT 0,
    
    -- Limits
    min_deposit DECIMAL(15,2),
    max_deposit DECIMAL(15,2),
    min_withdrawal DECIMAL(15,2),
    max_withdrawal DECIMAL(15,2),
    withdrawal_limit_period ENUM('day', 'week', 'month') NULL,
    withdrawal_limit_count TINYINT NULL,
    
    -- Duration (for fixed-term plans)
    min_duration_months SMALLINT,
    max_duration_months SMALLINT,
    
    -- Tax
    is_taxable TINYINT(1) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    
    -- Features
    allows_overdraft TINYINT(1) DEFAULT 0,
    overdraft_limit DECIMAL(15,2),
    overdraft_interest_rate DECIMAL(5,2),
    
    -- Status
    is_active TINYINT(1) DEFAULT 1,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_savings_plans_type (plan_type_id),
    
    FOREIGN KEY (plan_type_id) REFERENCES savings_plan_types(id)
);


-- pending_transactions table (ADDED: referenced in savings_accounts generated column)
-- Tracks transactions that are pending and affect available balance
CREATE TABLE pending_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    savings_account_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED NULL,
    amount DECIMAL(15,2) NOT NULL,
    transaction_type ENUM('debit', 'credit') NOT NULL DEFAULT 'debit',
    description VARCHAR(255),
    expires_at TIMESTAMP NULL,
    status ENUM('pending', 'cleared', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_pending_trans_account (savings_account_id),
    INDEX idx_pending_trans_status (status),
    
    FOREIGN KEY (savings_account_id) REFERENCES savings_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE SET NULL
);

-- Member Savings Accounts
CREATE TABLE savings_accounts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    account_number VARCHAR(50) UNIQUE NOT NULL, -- Format: SAV-YYYYMM-XXXXX
    member_id BIGINT UNSIGNED NOT NULL,
    plan_id TINYINT UNSIGNED NOT NULL,
    
    -- Account Details
    account_name VARCHAR(191) NOT NULL,
    opening_balance DECIMAL(15,2) DEFAULT 0.00,
    current_balance DECIMAL(15,2) DEFAULT 0.00,
    available_balance DECIMAL(15,2) GENERATED ALWAYS AS (
        current_balance - 
        COALESCE((SELECT SUM(amount) FROM pending_transactions WHERE savings_account_id = id), 0)
    ) STORED,
    
    -- Dates
    opening_date DATE NOT NULL,
    maturity_date DATE NULL,
    closing_date DATE NULL,
    
    -- Interest
    last_interest_calculation DATE NULL,
    accrued_interest DECIMAL(15,2) DEFAULT 0,
    
    -- Overdraft
    overdraft_limit DECIMAL(15,2) DEFAULT 0,
    overdraft_used DECIMAL(15,2) DEFAULT 0,
    
    -- Nomination (for joint accounts)
    is_joint TINYINT(1) DEFAULT 0,
    joint_holders JSON,
    
    -- Status
    status ENUM('active', 'dormant', 'frozen', 'closed') DEFAULT 'active',
    status_reason TEXT,
    frozen_by BIGINT UNSIGNED NULL,
    frozen_at TIMESTAMP NULL,
    
    -- Standing Instructions
    standing_instructions JSON,
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    closed_by BIGINT UNSIGNED NULL,
    closed_reason TEXT,
    
    INDEX idx_savings_member (member_id),
    INDEX idx_savings_plan (plan_id),
    INDEX idx_savings_status (status),
    
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (plan_id) REFERENCES savings_plans(id),
    FOREIGN KEY (frozen_by) REFERENCES users(id),
    FOREIGN KEY (closed_by) REFERENCES users(id)
);

-- Savings Account Transactions (link to main transactions)
CREATE TABLE savings_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    savings_account_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED UNIQUE NOT NULL,
    
    -- Impact on savings
    amount DECIMAL(15,2) NOT NULL,
    running_balance DECIMAL(15,2) NOT NULL,
    
    -- Type (for reporting)
    transaction_type ENUM('deposit', 'withdrawal', 'interest', 'fee', 'transfer') NOT NULL,
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_savings_trans_account (savings_account_id),
    INDEX idx_savings_trans_type (transaction_type),
    
    FOREIGN KEY (savings_account_id) REFERENCES savings_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id)
);

-- Interest Accruals
CREATE TABLE savings_interest_accruals (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    savings_account_id BIGINT UNSIGNED NOT NULL,
    
    -- Period
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    
    -- Calculation
    average_balance DECIMAL(15,2) NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,
    interest_amount DECIMAL(15,2) NOT NULL,
    
    -- Tax
    tax_amount DECIMAL(15,2) DEFAULT 0,
    net_amount DECIMAL(15,2) GENERATED ALWAYS AS (interest_amount - tax_amount) STORED,
    
    -- Payment
    paid_transaction_id BIGINT UNSIGNED NULL,
    paid_at TIMESTAMP NULL,
    
    -- Status
    status ENUM('accrued', 'paid', 'cancelled') DEFAULT 'accrued',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_interest_account (savings_account_id),
    INDEX idx_interest_period (period_start, period_end),
    INDEX idx_interest_status (status),
    
    FOREIGN KEY (savings_account_id) REFERENCES savings_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (paid_transaction_id) REFERENCES transactions(id)
);

-- =====================================================
-- PROJECTS AND INVESTMENTS
-- =====================================================

-- Projects
CREATE TABLE projects (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    project_number VARCHAR(50) UNIQUE NOT NULL, -- Format: PRJ-YYYYMM-XXXX
    name VARCHAR(191) NOT NULL,
    description TEXT,
    category_id TINYINT UNSIGNED,
    
    -- Budget
    budget_amount DECIMAL(15,2) NOT NULL,
    committed_amount DECIMAL(15,2) DEFAULT 0,
    spent_amount DECIMAL(15,2) DEFAULT 0,
    remaining_budget DECIMAL(15,2) GENERATED ALWAYS AS (budget_amount - spent_amount) STORED,
    
    -- Timeline
    start_date DATE,
    expected_end_date DATE,
    actual_end_date DATE NULL,
    
    -- Progress
    status_id TINYINT UNSIGNED NOT NULL,
    progress_percentage TINYINT DEFAULT 0,
    milestones JSON,
    
    -- Financial Performance
    expected_revenue DECIMAL(15,2),
    actual_revenue DECIMAL(15,2) DEFAULT 0,
    expected_roi DECIMAL(5,2),
    actual_roi DECIMAL(5,2),
    potential_roi DECIMAL(8,2),
    risk_level_id TINYINT UNSIGNED,
    risk_score TINYINT DEFAULT 50,
    
    -- Management
    project_manager_id BIGINT UNSIGNED NULL,
    supervisor_id BIGINT UNSIGNED NULL,
    
    -- Location
    location_text TEXT,
    village_id MEDIUMINT UNSIGNED NULL,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    
    -- Documents
    proposal_document_id BIGINT UNSIGNED NULL,
    contract_document_id BIGINT UNSIGNED NULL,
    
    -- Status Flags
    is_featured TINYINT(1) DEFAULT 0,
    is_public TINYINT(1) DEFAULT 1,
    
    notes TEXT,
    metadata JSON,
    
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    deleted_by BIGINT UNSIGNED NULL,
    deleted_reason TEXT,
    
    INDEX idx_projects_number (project_number),
    INDEX idx_projects_status (status_id),
    INDEX idx_projects_category (category_id),
    INDEX idx_projects_manager (project_manager_id),
    INDEX idx_projects_dates (start_date, expected_end_date),
    INDEX idx_projects_location (village_id),
    
    FOREIGN KEY (category_id) REFERENCES project_categories(id),
    FOREIGN KEY (status_id) REFERENCES project_statuses(id),
    FOREIGN KEY (risk_level_id) REFERENCES investment_risk_levels(id),
    FOREIGN KEY (project_manager_id) REFERENCES members(id),
    FOREIGN KEY (supervisor_id) REFERENCES members(id),
    FOREIGN KEY (village_id) REFERENCES villages(id),
    FOREIGN KEY (proposal_document_id) REFERENCES documents(id),
    FOREIGN KEY (contract_document_id) REFERENCES documents(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
);

-- Project Team Members
CREATE TABLE project_team (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    role VARCHAR(100) NOT NULL,
    responsibilities TEXT,
    assigned_at DATE NOT NULL,
    assigned_by BIGINT UNSIGNED NOT NULL,
    released_at DATE NULL,
    released_by BIGINT UNSIGNED NULL,
    is_active TINYINT(1) DEFAULT 1,
    
    UNIQUE KEY unique_project_member (project_id, member_id, released_at),
    INDEX idx_project_team_project (project_id),
    INDEX idx_project_team_member (member_id),
    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (assigned_by) REFERENCES users(id),
    FOREIGN KEY (released_by) REFERENCES users(id)
);

-- Project Transactions
CREATE TABLE project_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED UNIQUE NOT NULL,
    transaction_type ENUM('budget_allocation', 'expense', 'revenue', 'transfer') NOT NULL,
    category VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_project_transactions_project (project_id),
    INDEX idx_project_transactions_type (transaction_type),
    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id)
);

-- Project Risks
CREATE TABLE project_risks (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT UNSIGNED NOT NULL,
    risk_description TEXT NOT NULL,
    risk_category VARCHAR(100),
    probability ENUM('low', 'medium', 'high') DEFAULT 'medium',
    impact ENUM('low', 'medium', 'high') DEFAULT 'medium',
    risk_score TINYINT GENERATED ALWAYS AS (
        CASE 
            WHEN probability = 'high' AND impact = 'high' THEN 9
            WHEN probability = 'high' AND impact = 'medium' THEN 6
            WHEN probability = 'high' AND impact = 'low' THEN 3
            WHEN probability = 'medium' AND impact = 'high' THEN 6
            WHEN probability = 'medium' AND impact = 'medium' THEN 4
            WHEN probability = 'medium' AND impact = 'low' THEN 2
            WHEN probability = 'low' AND impact = 'high' THEN 3
            WHEN probability = 'low' AND impact = 'medium' THEN 2
            ELSE 1
        END
    ) STORED,
    mitigation_strategy TEXT,
    contingency_plan TEXT,
    owner_id BIGINT UNSIGNED NULL,
    status ENUM('identified', 'monitoring', 'mitigated', 'occurred') DEFAULT 'identified',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_project_risks_project (project_id),
    INDEX idx_project_risks_score (risk_score),
    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES members(id)
);

-- Project Milestones
CREATE TABLE project_milestones (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(191) NOT NULL,
    description TEXT,
    due_date DATE NOT NULL,
    completion_date DATE NULL,
    completion_percentage TINYINT DEFAULT 0,
    deliverables TEXT,
    is_critical TINYINT(1) DEFAULT 0,
    status ENUM('pending', 'in_progress', 'completed', 'delayed') DEFAULT 'pending',
    completed_by BIGINT UNSIGNED NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_project_milestones_project (project_id),
    INDEX idx_project_milestones_due_date (due_date),
    INDEX idx_project_milestones_status (status),
    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (completed_by) REFERENCES members(id)
);

-- Investment Opportunities
CREATE TABLE investment_opportunities (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    opportunity_number VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(191) NOT NULL,
    description TEXT,
    
    -- Financial Targets
    target_amount DECIMAL(15,2) NOT NULL,
    minimum_investment DECIMAL(15,2) NOT NULL,
    maximum_investment DECIMAL(15,2),
    
    -- Returns
    expected_roi DECIMAL(5,2),
    projected_returns JSON,
    risk_level_id TINYINT UNSIGNED,
    
    -- Dates
    launch_date DATE NOT NULL,
    deadline_date DATE,
    close_date DATE NULL,
    
    -- Current Status
    raised_amount DECIMAL(15,2) DEFAULT 0,
    investor_count INT DEFAULT 0,
    status_id TINYINT UNSIGNED NOT NULL,
    
    -- Documents
    prospectus_document_id BIGINT UNSIGNED NULL,
    
    -- Management
    fund_manager_id BIGINT UNSIGNED NULL,
    
    -- Terms
    lock_in_period_months SMALLINT,
    dividend_frequency ENUM('monthly', 'quarterly', 'annually', 'maturity') DEFAULT 'annually',
    
    notes TEXT,
    metadata JSON,
    
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_opportunities_number (opportunity_number),
    INDEX idx_opportunities_status (status_id),
    INDEX idx_opportunities_risk (risk_level_id),
    INDEX idx_opportunities_dates (launch_date, deadline_date),
    
    FOREIGN KEY (risk_level_id) REFERENCES investment_risk_levels(id),
    FOREIGN KEY (status_id) REFERENCES investment_statuses(id),
    FOREIGN KEY (prospectus_document_id) REFERENCES documents(id),
    FOREIGN KEY (fund_manager_id) REFERENCES members(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Member Investments
CREATE TABLE member_investments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    investment_number VARCHAR(50) UNIQUE NOT NULL,
    opportunity_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    
    -- Investment Details
    amount_invested DECIMAL(15,2) NOT NULL,
    units_allocated INT UNSIGNED,
    unit_price DECIMAL(10,2),
    investment_date DATE NOT NULL,
    
    -- Payment
    transaction_id BIGINT UNSIGNED NOT NULL,
    payment_method_id TINYINT UNSIGNED NOT NULL,
    
    -- Returns
    returns_received DECIMAL(15,2) DEFAULT 0,
    roi_realized DECIMAL(5,2),
    last_dividend_paid DATE NULL,
    
    -- Status
    status_id TINYINT UNSIGNED NOT NULL,
    
    -- Maturity
    maturity_date DATE,
    is_reinvested TINYINT(1) DEFAULT 0,
    
    -- Withdrawal
    withdrawal_date DATE NULL,
    withdrawal_amount DECIMAL(15,2),
    withdrawal_transaction_id BIGINT UNSIGNED NULL,
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_investments_member (member_id),
    INDEX idx_investments_opportunity (opportunity_id),
    INDEX idx_investments_status (status_id),
    INDEX idx_investments_date (investment_date),
    
    FOREIGN KEY (opportunity_id) REFERENCES investment_opportunities(id),
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id),
    FOREIGN KEY (status_id) REFERENCES investment_statuses(id),
    FOREIGN KEY (withdrawal_transaction_id) REFERENCES transactions(id)
);

-- Investment Returns/Dividends
CREATE TABLE investment_returns (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    return_number VARCHAR(50) UNIQUE NOT NULL,
    investment_id BIGINT UNSIGNED NOT NULL,
    
    -- Return Details
    amount DECIMAL(15,2) NOT NULL,
    return_type ENUM('dividend', 'interest', 'profit_share', 'capital_gain') NOT NULL,
    return_date DATE NOT NULL,
    
    -- Payment
    transaction_id BIGINT UNSIGNED NOT NULL,
    paid_at TIMESTAMP NOT NULL,
    
    -- Period
    period_start DATE,
    period_end DATE,
    
    -- Tax
    tax_withheld DECIMAL(15,2) DEFAULT 0,
    net_amount DECIMAL(15,2) GENERATED ALWAYS AS (amount - tax_withheld) STORED,
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_investment_returns_investment (investment_id),
    INDEX idx_investment_returns_date (return_date),
    
    FOREIGN KEY (investment_id) REFERENCES member_investments(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id)
);

-- =====================================================
-- FUNDRAISING MODULE
-- =====================================================

-- Fundraising Campaigns
CREATE TABLE fundraising_campaigns (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    campaign_number VARCHAR(50) UNIQUE NOT NULL, -- Format: FR-YYYYMM-XXXX
    category_id TINYINT UNSIGNED,
    title VARCHAR(191) NOT NULL,
    description TEXT,
    
    -- Financial
    target_amount DECIMAL(15,2) NOT NULL,
    raised_amount DECIMAL(15,2) DEFAULT 0.00,
    min_contribution DECIMAL(15,2),
    max_contribution DECIMAL(15,2),
    
    -- Dates
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    
    -- Status
    status_id TINYINT UNSIGNED NOT NULL,
    
    -- Organizer
    organizer_id BIGINT UNSIGNED NOT NULL,
    contact_person VARCHAR(191),
    contact_phone VARCHAR(50),
    contact_email VARCHAR(191),
    
    -- Location
    location_text TEXT,
    village_id MEDIUMINT UNSIGNED NULL,
    
    -- Media
    cover_image VARCHAR(255),
    gallery JSON,
    video_url VARCHAR(255),
    
    -- Payment Details
    bank_account_details JSON,
    mobile_money_details JSON,
    
    -- Tax Benefits
    is_tax_deductible TINYINT(1) DEFAULT 0,
    tax_receipts_issued TINYINT(1) DEFAULT 0,
    
    -- Updates
    updates JSON,
    
    notes TEXT,
    metadata JSON,
    
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_campaigns_number (campaign_number),
    INDEX idx_campaigns_status (status_id),
    INDEX idx_campaigns_category (category_id),
    INDEX idx_campaigns_dates (start_date, end_date),
    INDEX idx_campaigns_organizer (organizer_id),
    
    FOREIGN KEY (category_id) REFERENCES fundraising_categories(id),
    FOREIGN KEY (status_id) REFERENCES fundraising_statuses(id),
    FOREIGN KEY (organizer_id) REFERENCES members(id),
    FOREIGN KEY (village_id) REFERENCES villages(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Fundraising Contributions
CREATE TABLE fundraising_contributions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    contribution_number VARCHAR(50) UNIQUE NOT NULL,
    campaign_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED UNIQUE NOT NULL,
    
    -- Contributor (if member)
    member_id BIGINT UNSIGNED NULL,
    
    -- Contributor Info (if non-member)
    contributor_name VARCHAR(191) NOT NULL,
    contributor_email VARCHAR(191),
    contributor_phone VARCHAR(50),
    contributor_address TEXT,
    is_anonymous TINYINT(1) DEFAULT 0,
    
    -- Contribution
    amount DECIMAL(15,2) NOT NULL,
    contribution_date DATE NOT NULL,
    payment_method_id TINYINT UNSIGNED NOT NULL,
    
    -- Receipt
    receipt_number VARCHAR(100),
    receipt_issued TINYINT(1) DEFAULT 0,
    receipt_issued_at TIMESTAMP NULL,
    receipt_issued_by BIGINT UNSIGNED NULL,
    
    -- Acknowledgment
    thank_you_sent TINYINT(1) DEFAULT 0,
    thank_you_sent_at TIMESTAMP NULL,
    
    -- Message
    message TEXT,
    is_public_message TINYINT(1) DEFAULT 1,
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_contributions_campaign (campaign_id),
    INDEX idx_contributions_member (member_id),
    INDEX idx_contributions_date (contribution_date),
    INDEX idx_contributions_receipt (receipt_number),
    
    FOREIGN KEY (campaign_id) REFERENCES fundraising_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id),
    FOREIGN KEY (receipt_issued_by) REFERENCES users(id)
);

-- Fundraising Expenses
CREATE TABLE fundraising_expenses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    expense_number VARCHAR(50) UNIQUE NOT NULL,
    campaign_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED UNIQUE NOT NULL,
    
    -- Expense Details
    description VARCHAR(191) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    expense_date DATE NOT NULL,
    category VARCHAR(100),
    
    -- Payee
    payee_name VARCHAR(191),
    payee_type ENUM('individual', 'company', 'other') DEFAULT 'individual',
    
    -- Receipt
    receipt_number VARCHAR(100),
    receipt_document_id BIGINT UNSIGNED NULL,
    
    -- Approval
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    
    notes TEXT,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_expenses_campaign (campaign_id),
    INDEX idx_expenses_date (expense_date),
    INDEX idx_expenses_category (category),
    
    FOREIGN KEY (campaign_id) REFERENCES fundraising_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (receipt_document_id) REFERENCES documents(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- =====================================================
-- MEETINGS MODULE
-- =====================================================

-- Meetings
CREATE TABLE meetings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    meeting_number VARCHAR(50) UNIQUE NOT NULL, -- Format: MTG-YYYYMM-XXXX
    type_id TINYINT UNSIGNED NOT NULL,
    title VARCHAR(191) NOT NULL,
    description TEXT,
    agenda JSON,
    
    -- Schedule
    scheduled_at DATETIME NOT NULL,
    duration_minutes SMALLINT,
    end_time DATETIME GENERATED ALWAYS AS (scheduled_at + INTERVAL duration_minutes MINUTE) VIRTUAL,
    
    -- Location
    location VARCHAR(255),
    meeting_link VARCHAR(255),
    is_virtual TINYINT(1) DEFAULT 0,
    virtual_platform VARCHAR(100),
    access_code VARCHAR(100),
    
    -- Status
    status_id TINYINT UNSIGNED NOT NULL,
    
    -- Capacity
    max_attendees INT,
    current_attendees INT DEFAULT 0,
    waitlist_enabled TINYINT(1) DEFAULT 0,
    
    -- Materials
    materials JSON,
    
    -- Minutes & Recordings
    minutes TEXT,
    minutes_document_id BIGINT UNSIGNED NULL,
    recording_link VARCHAR(255),
    recording_password VARCHAR(255),
    
    -- Decisions
    decisions JSON,
    action_items JSON,
    
    -- Recurrence
    is_recurring TINYINT(1) DEFAULT 0,
    recurrence_rule VARCHAR(255),
    parent_meeting_id BIGINT UNSIGNED NULL,
    
    -- Notifications
    reminders_sent JSON,
    
    notes TEXT,
    metadata JSON,
    
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_meetings_number (meeting_number),
    INDEX idx_meetings_status (status_id),
    INDEX idx_meetings_type (type_id),
    INDEX idx_meetings_scheduled (scheduled_at),
    INDEX idx_meetings_virtual (is_virtual),
    
    FOREIGN KEY (type_id) REFERENCES meeting_types(id),
    FOREIGN KEY (status_id) REFERENCES meeting_statuses(id),
    FOREIGN KEY (minutes_document_id) REFERENCES documents(id),
    FOREIGN KEY (parent_meeting_id) REFERENCES meetings(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Meeting Attendees
CREATE TABLE meeting_attendees (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    meeting_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    
    -- Invitation
    invited_by BIGINT UNSIGNED NOT NULL,
    invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    invitation_sent_at TIMESTAMP NULL,
    invitation_response ENUM('pending', 'confirmed', 'declined', 'tentative') DEFAULT 'pending',
    responded_at TIMESTAMP NULL,
    response_notes TEXT,
    
    -- Attendance
    attendance_status_id TINYINT UNSIGNED NULL,
    check_in_time TIMESTAMP NULL,
    check_in_method ENUM('manual', 'qr', 'face_recognition', 'self') NULL,
    check_out_time TIMESTAMP NULL,
    
    -- Participation
    spoke_at TIMESTAMP NULL,
    duration_attended_minutes SMALLINT,
    
    -- Feedback
    feedback_rating TINYINT,
    feedback_comment TEXT,
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_meeting_member (meeting_id, member_id),
    INDEX idx_attendees_meeting (meeting_id),
    INDEX idx_attendees_member (member_id),
    INDEX idx_attendees_response (invitation_response),
    INDEX idx_attendees_check_in (check_in_time),
    
    FOREIGN KEY (meeting_id) REFERENCES meetings(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (attendance_status_id) REFERENCES attendance_statuses(id),
    FOREIGN KEY (invited_by) REFERENCES users(id)
);

-- Meeting Guests (non-members)
CREATE TABLE meeting_guests (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    meeting_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(191) NOT NULL,
    email VARCHAR(191),
    phone VARCHAR(50),
    organization VARCHAR(191),
    title VARCHAR(191),
    
    -- Invitation
    invited_by BIGINT UNSIGNED NOT NULL,
    invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    invitation_sent_at TIMESTAMP NULL,
    invitation_response ENUM('pending', 'confirmed', 'declined', 'tentative') DEFAULT 'pending',
    responded_at TIMESTAMP NULL,
    
    -- Attendance
    checked_in_at TIMESTAMP NULL,
    checked_in_by BIGINT UNSIGNED NULL,
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_guests_meeting (meeting_id),
    INDEX idx_guests_response (invitation_response),
    
    FOREIGN KEY (meeting_id) REFERENCES meetings(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES users(id),
    FOREIGN KEY (checked_in_by) REFERENCES users(id)
);

-- Meeting Action Items
CREATE TABLE meeting_action_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    meeting_id BIGINT UNSIGNED NOT NULL,
    description TEXT NOT NULL,
    assigned_to BIGINT UNSIGNED NULL,
    due_date DATE,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    completed_at TIMESTAMP NULL,
    completed_by BIGINT UNSIGNED NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_action_items_meeting (meeting_id),
    INDEX idx_action_items_assigned (assigned_to),
    INDEX idx_action_items_status (status),
    
    FOREIGN KEY (meeting_id) REFERENCES meetings(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES members(id),
    FOREIGN KEY (completed_by) REFERENCES users(id)
);

-- =====================================================
-- DOCUMENTS MODULE
-- =====================================================

-- Documents
CREATE TABLE documents (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    document_number VARCHAR(100) UNIQUE NOT NULL, -- Format: DOC-YYYYMM-XXXXX
    member_id BIGINT UNSIGNED NOT NULL,
    category_id TINYINT UNSIGNED NOT NULL,
    
    -- Document Info
    name VARCHAR(191) NOT NULL,
    description TEXT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    file_size BIGINT UNSIGNED,
    mime_type VARCHAR(100),
    file_hash VARCHAR(255),
    
    -- Document Details
    issue_date DATE,
    expiry_date DATE,
    issuing_authority VARCHAR(191),
    document_number_id VARCHAR(100),
    country_of_issue_id TINYINT UNSIGNED,
    
    -- Verification
    status_id TINYINT UNSIGNED NOT NULL DEFAULT 1,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    verification_notes TEXT,
    rejection_reason TEXT,
    
    -- Security
    access_level ENUM('public', 'private', 'confidential') DEFAULT 'private',
    encryption_key VARCHAR(255),
    is_encrypted TINYINT(1) DEFAULT 0,
    
    -- Version Control
    version INT DEFAULT 1,
    previous_version_id BIGINT UNSIGNED NULL,
    
    -- Tags
    tags JSON,
    
    -- Audit
    uploaded_by BIGINT UNSIGNED NOT NULL,
    uploaded_ip VARCHAR(45),
    uploaded_user_agent TEXT,
    downloaded_count INT DEFAULT 0,
    last_downloaded_at TIMESTAMP NULL,
    
    notes TEXT,
    metadata JSON,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    deleted_by BIGINT UNSIGNED NULL,
    deleted_reason TEXT,
    
    INDEX idx_documents_number (document_number),
    INDEX idx_documents_member (member_id),
    INDEX idx_documents_category (category_id),
    INDEX idx_documents_status (status_id),
    INDEX idx_documents_expiry (expiry_date),
    INDEX idx_documents_type (file_type),
    
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES document_categories(id),
    FOREIGN KEY (status_id) REFERENCES document_statuses(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    FOREIGN KEY (country_of_issue_id) REFERENCES nationalities(id),
    FOREIGN KEY (previous_version_id) REFERENCES documents(id),
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
);

-- Document Shares (shared with other members)
CREATE TABLE document_shares (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    document_id BIGINT UNSIGNED NOT NULL,
    shared_with_member_id BIGINT UNSIGNED NOT NULL,
    shared_by BIGINT UNSIGNED NOT NULL,
    shared_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    permission ENUM('view', 'download', 'edit') DEFAULT 'view',
    is_active TINYINT(1) DEFAULT 1,
    
    UNIQUE KEY unique_document_share (document_id, shared_with_member_id),
    INDEX idx_document_shares_member (shared_with_member_id),
    
    FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_with_member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_by) REFERENCES users(id)
);

-- =====================================================
-- NOTIFICATIONS MODULE
-- =====================================================

-- Notifications
CREATE TABLE notifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    notification_number VARCHAR(50) UNIQUE NOT NULL,
    type_id TINYINT UNSIGNED NOT NULL,
    
    -- Recipient (NULL for broadcast)
    member_id BIGINT UNSIGNED NULL,
    role_id TINYINT UNSIGNED NULL,
    
    -- Content
    title VARCHAR(191) NOT NULL,
    message TEXT NOT NULL,
    short_message VARCHAR(255),
    
    -- Action
    action_url VARCHAR(255),
    action_text VARCHAR(100),
    image_url VARCHAR(255),
    icon VARCHAR(50),
    
    -- Priority
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    
    -- Scheduling
    scheduled_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    
    -- Delivery Channels
    send_email TINYINT(1) DEFAULT 0,
    send_sms TINYINT(1) DEFAULT 0,
    send_push TINYINT(1) DEFAULT 1,
    send_in_app TINYINT(1) DEFAULT 1,
    
    -- Tracking
    email_sent TINYINT(1) DEFAULT 0,
    email_sent_at TIMESTAMP NULL,
    sms_sent TINYINT(1) DEFAULT 0,
    sms_sent_at TIMESTAMP NULL,
    push_sent TINYINT(1) DEFAULT 0,
    push_sent_at TIMESTAMP NULL,
    
    -- Read Tracking (for in-app)
    read_count INT DEFAULT 0,
    first_read_at TIMESTAMP NULL,
    last_read_at TIMESTAMP NULL,
    
    -- Metadata
    metadata JSON,
    
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_notifications_member (member_id),
    INDEX idx_notifications_role (role_id),
    INDEX idx_notifications_type (type_id),
    INDEX idx_notifications_priority (priority),
    INDEX idx_notifications_scheduled (scheduled_at),
    INDEX idx_notifications_created (created_at),
    
    FOREIGN KEY (type_id) REFERENCES notification_types(id),
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Notification Receipts (for tracking reads)
CREATE TABLE notification_receipts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    notification_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at TIMESTAMP NULL,
    read_ip VARCHAR(45),
    read_user_agent TEXT,
    is_archived TINYINT(1) DEFAULT 0,
    archived_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_notification_member (notification_id, member_id),
    INDEX idx_receipts_member (member_id, is_read),
    
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- =====================================================
-- CHAT MODULE
-- =====================================================

-- Chat Conversations
CREATE TABLE chat_conversations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    conversation_type ENUM('individual', 'group') DEFAULT 'individual',
    name VARCHAR(191),
    description TEXT,
    avatar VARCHAR(255),
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    last_message_at TIMESTAMP NULL,
    
    INDEX idx_conversations_type (conversation_type),
    INDEX idx_conversations_last_message (last_message_at)
);

-- Chat Participants
CREATE TABLE chat_participants (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    conversation_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    role ENUM('member', 'admin', 'moderator') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    left_at TIMESTAMP NULL,
    last_read_at TIMESTAMP NULL,
    is_muted TINYINT(1) DEFAULT 0,
    muted_until TIMESTAMP NULL,
    nickname VARCHAR(100),
    
    UNIQUE KEY unique_conversation_member (conversation_id, member_id),
    INDEX idx_participants_member (member_id),
    INDEX idx_participants_active (left_at),
    
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Chat Messages
CREATE TABLE chat_messages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    message_number VARCHAR(50) UNIQUE NOT NULL,
    conversation_id BIGINT UNSIGNED NOT NULL,
    sender_id BIGINT UNSIGNED NOT NULL,
    
    -- Content
    message TEXT,
    message_type ENUM('text', 'image', 'file', 'audio', 'video', 'location', 'contact') DEFAULT 'text',
    
    -- Attachments
    attachment_path VARCHAR(255),
    attachment_name VARCHAR(255),
    attachment_type VARCHAR(50),
    attachment_size INT,
    thumbnail_path VARCHAR(255),
    
    -- Location
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    location_name VARCHAR(255),
    
    -- Reply
    reply_to_id BIGINT UNSIGNED NULL,
    
    -- Forward
    forwarded_from_id BIGINT UNSIGNED NULL,
    forwarded_count INT DEFAULT 0,
    
    -- Delivery
    is_delivered TINYINT(1) DEFAULT 0,
    delivered_at TIMESTAMP NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at TIMESTAMP NULL,
    read_count INT DEFAULT 0,
    
    -- Status
    is_edited TINYINT(1) DEFAULT 0,
    edited_at TIMESTAMP NULL,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    deleted_by BIGINT UNSIGNED NULL,
    
    -- Reactions
    reactions JSON,
    
    -- Metadata
    metadata JSON,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_messages_conversation (conversation_id, created_at),
    INDEX idx_messages_sender (sender_id),
    INDEX idx_messages_type (message_type),
    INDEX idx_messages_reply (reply_to_id),
    
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES members(id),
    FOREIGN KEY (reply_to_id) REFERENCES chat_messages(id),
    FOREIGN KEY (forwarded_from_id) REFERENCES chat_messages(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
);

-- Chat Message Receipts (for group message read tracking)
CREATE TABLE chat_message_receipts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    message_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_message_member (message_id, member_id),
    INDEX idx_receipts_member (member_id, is_read),
    
    FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- =====================================================
-- AUDIT AND SYSTEM TABLES
-- =====================================================

-- Audit Logs
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    log_number VARCHAR(50) UNIQUE NOT NULL,
    
    -- Actor
    user_id BIGINT UNSIGNED NULL,
    member_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    session_id VARCHAR(191),
    
    -- Action
    action_type_id TINYINT UNSIGNED NOT NULL,
    entity_type_id TINYINT UNSIGNED NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    entity_identifier VARCHAR(191), -- e.g., loan number, member number
    
    -- Description
    description TEXT,
    details JSON,
    
    -- Changes (for updates)
    old_values JSON,
    new_values JSON,
    
    -- Request Info
    request_method VARCHAR(10),
    request_url TEXT,
    request_headers JSON,
    response_status SMALLINT,
    execution_time_ms INT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_audit_user (user_id),
    INDEX idx_audit_member (member_id),
    INDEX idx_audit_action (action_type_id),
    INDEX idx_audit_entity (entity_type_id, entity_id),
    INDEX idx_audit_created (created_at),
    INDEX idx_audit_ip (ip_address),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL,
    FOREIGN KEY (action_type_id) REFERENCES audit_action_types(id),
    FOREIGN KEY (entity_type_id) REFERENCES entity_types(id)
);

-- Settings
CREATE TABLE settings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(191) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json', 'email', 'url', 'color') DEFAULT 'text',
    category VARCHAR(100),
    display_name VARCHAR(191),
    description VARCHAR(255),
    validation_rules JSON,
    options JSON,
    is_public TINYINT(1) DEFAULT 0,
    is_system TINYINT(1) DEFAULT 0,
    sort_order SMALLINT DEFAULT 0,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_settings_key (setting_key),
    INDEX idx_settings_category (category),
    
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Permissions
CREATE TABLE permissions (
    id SMALLINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_permissions_category (category)
);

-- Role Permissions
CREATE TABLE role_permissions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    role_id TINYINT UNSIGNED NOT NULL,
    permission_id SMALLINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_role_permission (role_id, permission_id),
    INDEX idx_role_permissions_role (role_id),
    INDEX idx_role_permissions_permission (permission_id),
    
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- Backups
CREATE TABLE backups (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    backup_number VARCHAR(50) UNIQUE NOT NULL,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    file_size BIGINT UNSIGNED,
    type ENUM('manual', 'scheduled', 'automatic') DEFAULT 'manual',
    status ENUM('pending', 'in_progress', 'completed', 'failed') DEFAULT 'pending',
    includes ENUM('full', 'structure_only', 'data_only') DEFAULT 'full',
    compression ENUM('none', 'gzip', 'zip') DEFAULT 'gzip',
    encryption TINYINT(1) DEFAULT 0,
    checksum VARCHAR(255),
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    failed_at TIMESTAMP NULL,
    failure_reason TEXT,
    notes TEXT,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_backups_status (status),
    INDEX idx_backups_type (type),
    
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Generated Reports
CREATE TABLE generated_reports (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    report_number VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(191) NOT NULL,
    type VARCHAR(100) NOT NULL,
    from_date DATE NOT NULL,
    to_date DATE NOT NULL,
    format ENUM('pdf', 'excel', 'csv', 'html', 'json') NOT NULL,
    file_path VARCHAR(255),
    file_size BIGINT UNSIGNED,
    parameters JSON,
    filters JSON,
    columns JSON,
    row_count INT,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    generated_by BIGINT UNSIGNED NOT NULL,
    downloaded_count INT DEFAULT 0,
    last_downloaded_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    notes TEXT,
    
    INDEX idx_reports_type (type),
    INDEX idx_reports_dates (from_date, to_date),
    INDEX idx_reports_generated (generated_at),
    
    FOREIGN KEY (generated_by) REFERENCES users(id)
);

-- Dashboard Photos
CREATE TABLE dashboard_photos (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    photo_number VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('project', 'meeting', 'event', 'achievement', 'slider') NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255),
    title VARCHAR(191),
    description TEXT,
    link_url VARCHAR(255),
    display_order SMALLINT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    start_date DATE NULL,
    end_date DATE NULL,
    views_count INT DEFAULT 0,
    clicks_count INT DEFAULT 0,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_photos_type (type, is_active, display_order),
    INDEX idx_photos_dates (start_date, end_date),
    
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- =====================================================
-- SYSTEM TABLES (Laravel Required)
-- =====================================================

-- Cache
CREATE TABLE cache (
    `key` VARCHAR(191) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL,
    INDEX idx_cache_expiration (expiration)
);

-- Cache Locks
CREATE TABLE cache_locks (
    `key` VARCHAR(191) PRIMARY KEY,
    owner VARCHAR(191) NOT NULL,
    expiration INT NOT NULL
);

-- Sessions
CREATE TABLE sessions (
    id VARCHAR(191) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    
    INDEX idx_sessions_user (user_id),
    INDEX idx_sessions_last_activity (last_activity),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Jobs
CREATE TABLE jobs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    queue VARCHAR(191) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    
    INDEX idx_jobs_queue (queue)
);

-- Job Batches
CREATE TABLE job_batches (
    id VARCHAR(191) PRIMARY KEY,
    name VARCHAR(191) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL
);

-- Failed Jobs
CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(191) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Password Reset Tokens
CREATE TABLE password_reset_tokens (
    email VARCHAR(191) PRIMARY KEY,
    token VARCHAR(191) NOT NULL,
    created_at TIMESTAMP NULL,
    INDEX idx_tokens_token (token)
);

-- Personal Access Tokens (for API)
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tokenable_type VARCHAR(191) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(191) NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_tokenable (tokenable_type, tokenable_id)
);

-- =====================================================
-- VIEWS FOR EASY DATA RETRIEVAL
-- =====================================================

-- Member Summary View (FIXED: uses status_id JOIN instead of t.status)
CREATE VIEW v_member_summary AS
SELECT 
    m.id,
    m.member_number,
    m.full_name,
    m.primary_phone,
    m.email,
    m.membership_status,
    m.join_date,
    r.name as primary_role,
    
    -- Financial Summary (FIXED: JOIN transaction_statuses instead of t.status)
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
    
    -- Loan Summary
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
    
    -- Share Summary
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
    
    -- Recent Activity
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
LEFT JOIN roles r ON u.role_id = r.id;

-- Loan Details View (FIXED: removed l.purpose which doesn't exist in loans table)
CREATE VIEW v_loan_details AS
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
    la.purpose as loan_purpose, -- FIXED: purpose comes from loan_applications
    ls.name as status,
    ls.color as status_color,
    l.application_date,
    l.approval_date,
    l.disbursement_date,
    l.maturity_date,
    
    -- Repayment Progress
    l.amount_paid,
    l.balance_due,
    l.payments_made,
    l.payments_remaining,
    ROUND((l.amount_paid / l.total_amount * 100), 2) as repayment_percentage,
    
    -- Default Status
    l.is_defaulted,
    l.days_overdue,
    
    -- Guarantors
    g1.full_name as guarantor1_name,
    g1.primary_phone as guarantor1_phone,
    g2.full_name as guarantor2_name,
    g2.primary_phone as guarantor2_phone,
    
    -- Dates
    l.created_at,
    l.updated_at

FROM loans l
JOIN members m ON l.member_id = m.id
JOIN loan_types lt ON l.loan_type_id = lt.id
JOIN loan_statuses ls ON l.status_id = ls.id
LEFT JOIN loan_applications la ON l.application_id = la.id
LEFT JOIN members g1 ON l.guarantor1_id = g1.id
LEFT JOIN members g2 ON l.guarantor2_id = g2.id;

-- Transaction Summary View
CREATE VIEW v_transaction_summary AS
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
LEFT JOIN users u ON t.processed_by = u.id;

-- Dashboard Statistics View
CREATE VIEW v_dashboard_stats AS
SELECT
    -- Members
    (SELECT COUNT(*) FROM members WHERE deleted_at IS NULL) as total_members,
    (SELECT COUNT(*) FROM members WHERE membership_status = 'active') as active_members,
    (SELECT COUNT(*) FROM members WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_members_30d,
    
    -- Financial
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
    ), 0) as total_system_balance, -- FIXED: joined transaction_statuses
    
    COALESCE((
        SELECT SUM(amount) 
        FROM transactions 
        WHERE status = 'completed' 
        AND transaction_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ), 0) as transaction_volume_30d,
    
    (SELECT COUNT(*) FROM transactions WHERE transaction_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as transaction_count_30d,
    
    -- Loans
    COALESCE((
        SELECT SUM(principal_amount) 
        FROM loans 
        WHERE status_id = (SELECT id FROM loan_statuses WHERE name = 'disbursed')
    ), 0) as total_active_loans,
    
    (SELECT COUNT(*) FROM loans WHERE status_id = (SELECT id FROM loan_statuses WHERE name = 'pending')) as pending_loans_count,
    (SELECT COUNT(*) FROM loans WHERE status_id = (SELECT id FROM loan_statuses WHERE name = 'disbursed')) as active_loans_count,
    (SELECT COUNT(*) FROM loans WHERE is_defaulted = 1) as defaulted_loans_count,
    
    -- Shares
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
    
    -- Projects
    (SELECT COUNT(*) FROM projects WHERE status_id = (SELECT id FROM project_statuses WHERE name = 'active')) as active_projects,
    
    -- Upcoming Meetings
    (SELECT COUNT(*) FROM meetings WHERE scheduled_at > NOW() AND scheduled_at < DATE_ADD(NOW(), INTERVAL 7 DAY)) as upcoming_meetings_7d,
    
    -- Users
    (SELECT COUNT(*) FROM users WHERE last_login_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as active_users_7d,
    
    -- System
    (SELECT COUNT(*) FROM sessions) as active_sessions,
    
    -- Current Timestamp
    NOW() as as_of;

-- =====================================================
-- INSERT LOOKUP DATA
-- =====================================================

-- Insert Roles
INSERT INTO roles (name, display_name, description, priority) VALUES
('client', 'Client', 'Regular member who can save and take loans', 1),
('shareholder', 'Shareholder', 'Member who owns shares and receives dividends', 2),
('cashier', 'Cashier', 'Staff member who processes transactions', 3),
('td', 'Technical Director', 'Manages projects and technical operations', 4),
('ceo', 'Chief Executive Officer', 'Executive oversight and strategic decisions', 5),
('admin', 'Administrator', 'System administrator with full access', 10);

-- Insert Genders
INSERT INTO genders (name, abbreviation) VALUES
('Male', 'M'),
('Female', 'F'),
('Other', 'O');

-- Insert Marital Statuses
INSERT INTO marital_statuses (name, description) VALUES
('Single', 'Never married'),
('Married', 'Legally married'),
('Divorced', 'Legally divorced'),
('Widowed', 'Spouse deceased'),
('Separated', 'Married but separated');

-- Insert Employment Statuses
INSERT INTO employment_statuses (name, description) VALUES
('employed', 'Employed full-time'),
('self-employed', 'Self-employed / Business owner'),
('unemployed', 'Currently unemployed'),
('retired', 'Retired'),
('student', 'Student');

-- Insert Nationalities
INSERT INTO nationalities (name, code, dial_code) VALUES
('Ugandan', 'UG', '+256'),
('Kenyan', 'KE', '+254'),
('Tanzanian', 'TZ', '+255'),
('Rwandan', 'RW', '+250'),
('South Sudanese', 'SS', '+211'),
('Other', 'OT', '+000');

-- Insert Loan Statuses
INSERT INTO loan_statuses (name, display_name, description, color, sort_order) VALUES
('pending', 'Pending', 'Loan application submitted, awaiting review', 'yellow', 1),
('approved', 'Approved', 'Loan has been approved', 'blue', 2),
('disbursed', 'Disbursed', 'Funds have been released to member', 'green', 3),
('rejected', 'Rejected', 'Application was rejected', 'red', 4),
('cancelled', 'Cancelled', 'Application was cancelled by applicant', 'gray', 5),
('completed', 'Completed', 'Loan has been fully repaid', 'green', 6),
('defaulted', 'Defaulted', 'Loan is in default', 'red', 7);

-- Insert Loan Types
INSERT INTO loan_types (name, description, min_amount, max_amount, default_interest_rate, min_repayment_months, max_repayment_months, requires_guarantors, guarantors_required) VALUES
('Emergency Loan', 'Quick loan for emergencies', 50000, 500000, 5.00, 1, 6, 1, 1),
('Education Loan', 'For school fees and educational expenses', 100000, 2000000, 8.00, 3, 24, 1, 2),
('Business Loan', 'For business expansion and working capital', 200000, 5000000, 10.00, 6, 36, 1, 2),
('Agriculture Loan', 'For farming activities and equipment', 100000, 3000000, 7.00, 3, 18, 1, 2),
('Home Improvement', 'For home repairs and improvements', 200000, 2000000, 9.00, 6, 24, 1, 2);

-- Insert Share Classes
INSERT INTO share_classes (name, description, par_value, min_purchase, max_purchase, voting_rights, dividend_priority) VALUES
('Ordinary Shares', 'Standard shares with voting rights', 10000, 1, 1000, 1, 1),
('Preference Shares', 'Preferred shares with higher dividend priority', 15000, 1, 500, 0, 2),
('Founders Shares', 'Special shares for founding members', 20000, 1, 100, 1, 3);

-- Insert Share Statuses
INSERT INTO share_statuses (name, display_name, description, color) VALUES
('active', 'Active', 'Shares are currently held and active', 'green'),
('sold', 'Sold', 'Shares have been sold', 'blue'),
('transferred', 'Transferred', 'Shares have been transferred', 'purple'),
('cancelled', 'Cancelled', 'Shares have been cancelled', 'red');

-- Insert Dividend Statuses
INSERT INTO dividend_statuses (name, display_name, description, color) VALUES
('declared', 'Declared', 'Dividend has been declared', 'blue'),
('processing', 'Processing', 'Dividend payment is being processed', 'yellow'),
('paid', 'Paid', 'Dividend has been paid', 'green'),
('cancelled', 'Cancelled', 'Dividend has been cancelled', 'red');

-- Insert Transaction Types
INSERT INTO transaction_types (name, display_name, description, impact, requires_approval, affects_savings, affects_loan, affects_share, is_fee, color, icon, sort_order) VALUES
('deposit', 'Deposit', 'Money added to account', 'credit', 0, 1, 0, 0, 0, 'green', 'arrow-down', 1),
('withdrawal', 'Withdrawal', 'Money taken from account', 'debit', 1, 1, 0, 0, 0, 'red', 'arrow-up', 2),
('transfer', 'Transfer', 'Money transferred between accounts', 'debit', 1, 1, 0, 0, 0, 'blue', 'exchange', 3),
('loan_disbursement', 'Loan Disbursement', 'Loan funds released', 'credit', 1, 1, 1, 0, 0, 'green', 'hand-holding-usd', 4),
('loan_repayment', 'Loan Repayment', 'Loan payment received', 'debit', 0, 1, 1, 0, 0, 'blue', 'credit-card', 5),
('fee', 'Fee', 'Service fee charged', 'debit', 1, 1, 0, 0, 1, 'orange', 'receipt', 6),
('dividend', 'Dividend', 'Dividend payment', 'credit', 1, 1, 0, 1, 0, 'purple', 'chart-line', 7),
('share_purchase', 'Share Purchase', 'Purchase of shares', 'debit', 1, 1, 0, 1, 0, 'indigo', 'store', 8),
('refund', 'Refund', 'Money returned to member', 'credit', 1, 1, 0, 0, 0, 'teal', 'undo', 9);

-- Insert Transaction Categories
INSERT INTO transaction_categories (name, display_name, transaction_type_id, description, is_system, requires_reference) VALUES
('savings_deposit', 'Savings Deposit', 1, 'Regular savings deposit', 1, 0),
('savings_withdrawal', 'Savings Withdrawal', 2, 'Regular savings withdrawal', 1, 0),
('loan_principal', 'Loan Principal', 5, 'Principal portion of loan payment', 1, 1),
('loan_interest', 'Loan Interest', 5, 'Interest portion of loan payment', 1, 1),
('loan_penalty', 'Loan Penalty', 6, 'Late payment penalty', 1, 1),
('processing_fee', 'Processing Fee', 6, 'Loan processing fee', 1, 1),
('transfer_out', 'Transfer Out', 3, 'Money sent to another member', 1, 1),
('transfer_in', 'Transfer In', 1, 'Money received from another member', 1, 1),
('share_dividend', 'Share Dividend', 7, 'Dividend payment from shares', 1, 1),
('share_purchase', 'Share Purchase', 8, 'Purchase of shares', 1, 1);

-- Insert Transaction Statuses
INSERT INTO transaction_statuses (name, display_name, description, color, is_final, sort_order) VALUES
('pending', 'Pending', 'Transaction is pending processing', 'yellow', 0, 1),
('completed', 'Completed', 'Transaction completed successfully', 'green', 1, 2),
('failed', 'Failed', 'Transaction failed', 'red', 1, 3),
('reversed', 'Reversed', 'Transaction has been reversed', 'orange', 1, 4);

-- Insert Payment Methods
INSERT INTO payment_methods (name, display_name, description, processing_time, fee_percentage, fee_fixed, requires_reference, icon, sort_order) VALUES
('cash', 'Cash', 'Physical cash payment', 'immediate', 0, 0, 0, 'money-bill', 1),
('bank_transfer', 'Bank Transfer', 'Transfer from bank account', '1-3 business days', 0, 5000, 1, 'university', 2),
('mobile_money', 'Mobile Money', 'Payment via mobile money', 'immediate', 0.5, 0, 1, 'mobile-alt', 3),
('cheque', 'Cheque', 'Payment by cheque', '2-5 business days', 0, 2000, 1, 'money-check', 4),
('card', 'Card Payment', 'Payment via debit/credit card', 'immediate', 1.5, 0, 1, 'credit-card', 5),
('internal', 'Internal Transfer', 'Transfer between accounts', 'immediate', 0, 0, 0, 'exchange', 6);

-- Insert Currencies
INSERT INTO currencies (code, name, symbol, decimal_places, is_base, exchange_rate) VALUES
('UGX', 'Uganda Shilling', 'USh', 0, 1, 1.0000),
('KES', 'Kenya Shilling', 'KSh', 2, 0, 26.5000),
('TZS', 'Tanzania Shilling', 'TSh', 2, 0, 950.0000),
('RWF', 'Rwandan Franc', 'RF', 0, 0, 980.0000),
('USD', 'US Dollar', '$', 2, 0, 0.00027);

-- Insert Notification Types
INSERT INTO notification_types (name, display_name, description, icon, color) VALUES
('info', 'Information', 'General information', 'info-circle', 'blue'),
('success', 'Success', 'Successful operation', 'check-circle', 'green'),
('warning', 'Warning', 'Warning message', 'exclamation-triangle', 'yellow'),
('error', 'Error', 'Error message', 'times-circle', 'red'),
('loan', 'Loan Update', 'Loan related notification', 'hand-holding-usd', 'purple'),
('transaction', 'Transaction', 'Transaction notification', 'exchange-alt', 'teal'),
('meeting', 'Meeting', 'Meeting reminder', 'calendar-alt', 'orange'),
('document', 'Document', 'Document update', 'file-alt', 'gray'),
('dividend', 'Dividend', 'Dividend notification', 'chart-line', 'green'),
('share', 'Share', 'Share update', 'store', 'indigo');

-- Insert Document Categories
INSERT INTO document_categories (name, display_name, description, is_mandatory, expiry_months, allowed_types) VALUES
('identification', 'Identification', 'National ID, passport, driving license', 1, 120, 'pdf,jpg,jpeg,png'),
('nin', 'National ID', 'National Identification Card', 1, NULL, 'pdf,jpg,jpeg,png'),
('passport', 'Passport', 'Valid passport', 0, 60, 'pdf,jpg,jpeg,png'),
('driving_license', 'Driving License', 'Valid driving permit', 0, 60, 'pdf,jpg,jpeg,png'),
('proof_of_address', 'Proof of Address', 'Utility bills, bank statements', 1, 3, 'pdf,jpg,jpeg,png'),
('employment', 'Employment Proof', 'Employment letters, pay slips', 1, 3, 'pdf'),
('business', 'Business Documents', 'Business permits, registration', 0, 12, 'pdf'),
('education', 'Education Certificates', 'Academic certificates', 0, NULL, 'pdf'),
('loan', 'Loan Documents', 'Loan applications, guarantor forms', 0, NULL, 'pdf'),
('share', 'Share Certificate', 'Share ownership certificate', 0, NULL, 'pdf'),
('photo', 'Passport Photo', 'Profile photograph', 1, NULL, 'jpg,jpeg,png'),
('signature', 'Signature', 'Member signature', 1, NULL, 'jpg,jpeg,png');

-- Insert Document Statuses
INSERT INTO document_statuses (name, display_name, description, color) VALUES
('pending', 'Pending', 'Document awaiting verification', 'yellow'),
('verified', 'Verified', 'Document has been verified', 'green'),
('rejected', 'Rejected', 'Document was rejected', 'red'),
('expired', 'Expired', 'Document has expired', 'gray');

-- Insert Project Statuses
INSERT INTO project_statuses (name, display_name, description, color) VALUES
('planning', 'Planning', 'Project in planning phase', 'blue'),
('active', 'Active', 'Project is ongoing', 'green'),
('on_hold', 'On Hold', 'Project temporarily paused', 'yellow'),
('completed', 'Completed', 'Project successfully completed', 'green'),
('cancelled', 'Cancelled', 'Project cancelled', 'red');

-- Insert Project Categories
INSERT INTO project_categories (name, description, icon, color) VALUES
('Infrastructure', 'Physical infrastructure projects', 'building', 'blue'),
('Education', 'Educational initiatives', 'book', 'green'),
('Health', 'Healthcare projects', 'heartbeat', 'red'),
('Agriculture', 'Farming and agricultural projects', 'seedling', 'green'),
('Technology', 'Technology and innovation', 'laptop', 'purple'),
('Community', 'Community development', 'users', 'orange');

-- Insert Meeting Statuses
INSERT INTO meeting_statuses (name, display_name, description, color) VALUES
('scheduled', 'Scheduled', 'Meeting is planned', 'blue'),
('ongoing', 'Ongoing', 'Meeting is in progress', 'green'),
('completed', 'Completed', 'Meeting has ended', 'gray'),
('cancelled', 'Cancelled', 'Meeting was cancelled', 'red');

-- Insert Meeting Types
INSERT INTO meeting_types (name, description, default_duration_minutes, color, icon) VALUES
('General Meeting', 'Monthly general meeting', 120, 'blue', 'users'),
('Board Meeting', 'Board of directors meeting', 90, 'purple', 'user-tie'),
('Committee Meeting', 'Committee meeting', 60, 'green', 'users-cog'),
('Project Review', 'Project progress review', 60, 'orange', 'project-diagram'),
('Emergency', 'Emergency meeting', 45, 'red', 'exclamation-triangle');

-- Insert Attendance Statuses
INSERT INTO attendance_statuses (name, display_name, description, color) VALUES
('present', 'Present', 'Attended the meeting', 'green'),
('absent', 'Absent', 'Did not attend', 'red'),
('late', 'Late', 'Arrived late', 'yellow'),
('excused', 'Excused', 'Absent with excuse', 'gray');

-- Insert Fundraising Statuses
INSERT INTO fundraising_statuses (name, display_name, description, color) VALUES
('draft', 'Draft', 'Campaign in draft mode', 'gray'),
('active', 'Active', 'Campaign is active', 'green'),
('completed', 'Completed', 'Campaign completed successfully', 'blue'),
('cancelled', 'Cancelled', 'Campaign was cancelled', 'red');

-- Insert Fundraising Categories
INSERT INTO fundraising_categories (name, description, icon, color) VALUES
('Education', 'Educational fundraising', 'graduation-cap', 'blue'),
('Health', 'Medical and health fundraising', 'hospital', 'red'),
('Community', 'Community projects', 'users', 'green'),
('Emergency', 'Emergency relief', 'ambulance', 'orange'),
('Infrastructure', 'Infrastructure development', 'building', 'purple');

-- Insert Savings Plan Types
INSERT INTO savings_plan_types (name, description, min_balance, interest_rate, interest_calculation, withdrawal_fee_percentage, is_taxable) VALUES
('Regular Savings', 'Standard savings account', 0, 2.0, 'monthly', 0, 0),
('Fixed Deposit', 'Fixed term deposit', 100000, 5.0, 'annually', 1, 1),
('Target Savings', 'Goal-based savings', 5000, 3.0, 'monthly', 0.5, 0),
('Children\'s Savings', 'Savings for minors', 0, 2.5, 'monthly', 0, 0);

-- Insert Investment Risk Levels
INSERT INTO investment_risk_levels (name, display_name, description, color, score_range, sort_order) VALUES
('low', 'Low Risk', 'Conservative investments with stable returns', 'green', '0-30', 1),
('medium', 'Medium Risk', 'Balanced investments with moderate returns', 'yellow', '31-60', 2),
('high', 'High Risk', 'Aggressive investments with high potential returns', 'red', '61-100', 3);

-- Insert Investment Statuses
INSERT INTO investment_statuses (name, display_name, description, color) VALUES
('upcoming', 'Upcoming', 'Investment opportunity upcoming', 'blue'),
('active', 'Active', 'Investment is active', 'green'),
('matured', 'Matured', 'Investment has matured', 'purple'),
('closed', 'Closed', 'Investment is closed', 'gray'),
('cancelled', 'Cancelled', 'Investment was cancelled', 'red');

-- Insert Audit Action Types
INSERT INTO audit_action_types (name, display_name, description, severity) VALUES
('create', 'Create', 'Record created', 'info'),
('update', 'Update', 'Record updated', 'info'),
('delete', 'Delete', 'Record deleted', 'warning'),
('login', 'Login', 'User logged in', 'info'),
('logout', 'Logout', 'User logged out', 'info'),
('login_failed', 'Login Failed', 'Failed login attempt', 'warning'),
('role_switch', 'Role Switch', 'User switched role', 'info'),
('approve', 'Approve', 'Record approved', 'info'),
('reject', 'Reject', 'Record rejected', 'warning'),
('disburse', 'Disburse', 'Funds disbursed', 'critical');

-- Insert Entity Types
INSERT INTO entity_types (name, display_name, table_name) VALUES
('user', 'User', 'users'),
('member', 'Member', 'members'),
('loan', 'Loan', 'loans'),
('transaction', 'Transaction', 'transactions'),
('share', 'Share', 'shares'),
('dividend', 'Dividend', 'dividends'),
('project', 'Project', 'projects'),
('meeting', 'Meeting', 'meetings'),
('document', 'Document', 'documents'),
('investment', 'Investment', 'member_investments'),
('fundraising', 'Fundraising', 'fundraising_campaigns');

-- Insert Permissions
INSERT INTO permissions (name, display_name, category, description) VALUES
('view_dashboard', 'View Dashboard', 'general', 'View system dashboard'),

-- Member Permissions
('view_members', 'View Members', 'members', 'View member list'),
('view_member_details', 'View Member Details', 'members', 'View detailed member information'),
('create_members', 'Create Members', 'members', 'Create new members'),
('edit_members', 'Edit Members', 'members', 'Edit member details'),
('delete_members', 'Delete Members', 'members', 'Delete members'),
('export_members', 'Export Members', 'members', 'Export member data'),

-- Loan Permissions
('view_loans', 'View Loans', 'loans', 'View loan list'),
('view_loan_details', 'View Loan Details', 'loans', 'View detailed loan information'),
('create_loans', 'Create Loans', 'loans', 'Apply for loans'),
('approve_loans', 'Approve Loans', 'loans', 'Approve loan applications'),
('disburse_loans', 'Disburse Loans', 'loans', 'Disburse loan funds'),
('edit_loans', 'Edit Loans', 'loans', 'Edit loan details'),
('delete_loans', 'Delete Loans', 'loans', 'Delete loans'),

-- Transaction Permissions
('view_transactions', 'View Transactions', 'transactions', 'View transaction list'),
('view_transaction_details', 'View Transaction Details', 'transactions', 'View detailed transaction information'),
('create_transactions', 'Create Transactions', 'transactions', 'Create transactions'),
('approve_transactions', 'Approve Transactions', 'transactions', 'Approve pending transactions'),
('reverse_transactions', 'Reverse Transactions', 'transactions', 'Reverse completed transactions'),
('reconcile_transactions', 'Reconcile Transactions', 'transactions', 'Reconcile transactions'),
('export_transactions', 'Export Transactions', 'transactions', 'Export transaction data'),

-- Share Permissions
('view_shares', 'View Shares', 'shares', 'View share list'),
('view_share_details', 'View Share Details', 'shares', 'View detailed share information'),
('create_shares', 'Create Shares', 'shares', 'Issue new shares'),
('transfer_shares', 'Transfer Shares', 'shares', 'Approve share transfers'),

-- Dividend Permissions
('view_dividends', 'View Dividends', 'dividends', 'View dividend list'),
('declare_dividends', 'Declare Dividends', 'dividends', 'Declare new dividends'),
('process_dividends', 'Process Dividends', 'dividends', 'Process dividend payments'),

-- Project Permissions
('view_projects', 'View Projects', 'projects', 'View project list'),
('view_project_details', 'View Project Details', 'projects', 'View detailed project information'),
('create_projects', 'Create Projects', 'projects', 'Create new projects'),
('edit_projects', 'Edit Projects', 'projects', 'Edit project details'),
('delete_projects', 'Delete Projects', 'projects', 'Delete projects'),

-- Meeting Permissions
('view_meetings', 'View Meetings', 'meetings', 'View meeting list'),
('view_meeting_details', 'View Meeting Details', 'meetings', 'View detailed meeting information'),
('create_meetings', 'Create Meetings', 'meetings', 'Schedule meetings'),
('edit_meetings', 'Edit Meetings', 'meetings', 'Edit meeting details'),
('delete_meetings', 'Delete Meetings', 'meetings', 'Delete meetings'),
('manage_attendees', 'Manage Attendees', 'meetings', 'Manage meeting attendees'),

-- Document Permissions
('view_documents', 'View Documents', 'documents', 'View document list'),
('upload_documents', 'Upload Documents', 'documents', 'Upload documents'),
('verify_documents', 'Verify Documents', 'documents', 'Verify documents'),
('delete_documents', 'Delete Documents', 'documents', 'Delete documents'),

-- Report Permissions
('view_reports', 'View Reports', 'reports', 'View report list'),
('generate_reports', 'Generate Reports', 'reports', 'Generate new reports'),
('export_data', 'Export Data', 'reports', 'Export system data'),

-- System Permissions
('manage_settings', 'Manage Settings', 'system', 'Manage system settings'),
('view_audit_logs', 'View Audit Logs', 'system', 'View audit logs'),
('manage_users', 'Manage Users', 'system', 'Manage system users'),
('manage_roles', 'Manage Roles', 'system', 'Manage roles and permissions'),
('manage_backups', 'Manage Backups', 'system', 'Manage system backups'),
('view_system_logs', 'View System Logs', 'system', 'View system logs');

-- Insert Default Settings
INSERT INTO settings (setting_key, setting_value, setting_type, category, display_name, description, is_public, sort_order) VALUES
('app.name', 'BSS Investment Group', 'text', 'general', 'Application Name', 'The name of the application', 1, 1),
('app.version', '2.0.0', 'text', 'general', 'Application Version', 'Current version of the application', 1, 2),
('app.logo', '/images/logo.png', 'text', 'general', 'Application Logo', 'Path to the application logo', 1, 3),
('app.favicon', '/images/favicon.ico', 'text', 'general', 'Favicon', 'Path to the favicon', 1, 4),

-- Company Information
('company.name', 'BSS Investment Group', 'text', 'company', 'Company Name', 'Legal name of the company', 1, 5),
('company.email', 'info@bss.com', 'email', 'company', 'Company Email', 'General company email', 1, 6),
('company.phone', '+256 XXX XXX XXX', 'text', 'company', 'Company Phone', 'Company contact phone', 1, 7),
('company.whatsapp', '+256 XXX XXX XXX', 'text', 'company', 'WhatsApp Number', 'Company WhatsApp number', 1, 8),
('company.address', 'Kampala, Uganda', 'text', 'company', 'Company Address', 'Physical address', 1, 9),
('company.website', 'https://bss.com', 'url', 'company', 'Website', 'Company website URL', 1, 10),

-- Financial Settings
('currency.default', 'UGX', 'text', 'financial', 'Default Currency', 'Default currency for transactions', 1, 11),
('currency.decimals', '0', 'number', 'financial', 'Currency Decimals', 'Number of decimal places for currency', 1, 12),
('interest.default_rate', '10.00', 'number', 'financial', 'Default Interest Rate', 'Default loan interest rate (%)', 0, 13),
('savings.min_balance', '5000', 'number', 'financial', 'Minimum Savings Balance', 'Minimum balance for savings accounts', 0, 14),

-- System Settings
('system.maintenance_mode', 'false', 'boolean', 'system', 'Maintenance Mode', 'Put system in maintenance mode', 0, 15),
('system.maintenance_message', 'System under maintenance', 'text', 'system', 'Maintenance Message', 'Message shown during maintenance', 0, 16),
('system.timezone', 'Africa/Kampala', 'text', 'system', 'Timezone', 'System timezone', 0, 17),
('system.date_format', 'Y-m-d', 'text', 'system', 'Date Format', 'Format for displaying dates', 0, 18),
('system.time_format', 'H:i:s', 'text', 'system', 'Time Format', 'Format for displaying times', 0, 19),

-- Security Settings
('security.allow_registration', 'true', 'boolean', 'security', 'Allow Registration', 'Allow new user registration', 0, 20),
('security.require_email_verification', 'true', 'boolean', 'security', 'Require Email Verification', 'Require email verification for new accounts', 0, 21),
('security.session_timeout', '30', 'number', 'security', 'Session Timeout', 'Session timeout in minutes', 0, 22),
('security.password_min_length', '8', 'number', 'security', 'Minimum Password Length', 'Minimum required password length', 0, 23),
('security.password_require_uppercase', 'true', 'boolean', 'security', 'Require Uppercase', 'Passwords must contain uppercase letters', 0, 24),
('security.password_require_numbers', 'true', 'boolean', 'security', 'Require Numbers', 'Passwords must contain numbers', 0, 25),
('security.password_require_symbols', 'false', 'boolean', 'security', 'Require Symbols', 'Passwords must contain symbols', 0, 26),
('security.two_factor_auth', 'false', 'boolean', 'security', 'Two Factor Authentication', 'Enable 2FA for all users', 0, 27),
('security.max_login_attempts', '5', 'number', 'security', 'Max Login Attempts', 'Maximum failed login attempts before lockout', 0, 28),
('security.lockout_duration', '15', 'number', 'security', 'Lockout Duration', 'Lockout duration in minutes', 0, 29),

-- Notification Settings
('notifications.email_enabled', 'true', 'boolean', 'notifications', 'Email Notifications', 'Enable email notifications', 0, 30),
('notifications.sms_enabled', 'false', 'boolean', 'notifications', 'SMS Notifications', 'Enable SMS notifications', 0, 31),
('notifications.whatsapp_enabled', 'false', 'boolean', 'notifications', 'WhatsApp Notifications', 'Enable WhatsApp notifications', 0, 32),
('notifications.from_email', 'noreply@bss.com', 'email', 'notifications', 'From Email', 'Email address for outgoing notifications', 0, 33),
('notifications.from_name', 'BSS System', 'text', 'notifications', 'From Name', 'Name for outgoing notifications', 0, 34),

-- Feature Toggles
('features.loans_enabled', 'true', 'boolean', 'features', 'Loans Enabled', 'Enable loan module', 0, 35),
('features.shares_enabled', 'true', 'boolean', 'features', 'Shares Enabled', 'Enable shares module', 0, 36),
('features.projects_enabled', 'true', 'boolean', 'features', 'Projects Enabled', 'Enable projects module', 0, 37),
('features.fundraising_enabled', 'true', 'boolean', 'features', 'Fundraising Enabled', 'Enable fundraising module', 0, 38),
('features.chat_enabled', 'true', 'boolean', 'features', 'Chat Enabled', 'Enable chat module', 0, 39);

-- =====================================================
-- CREATE TRIGGERS FOR DATA INTEGRITY
-- =====================================================

DELIMITER $$

-- Trigger to update member's financial summary after transaction
CREATE TRIGGER after_transaction_complete
AFTER UPDATE ON transactions
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
        -- Update member balances will be handled by application logic
        -- This is just for audit
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
            JSON_OBJECT('old_status', OLD.status, 'new_status', NEW.status),
            NOW()
        );
    END IF;
END$$

-- Trigger to ensure unique primary address per member
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
END$$

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
END$$

-- Trigger to update loan balance after repayment
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
    
    -- Check if loan is fully paid
    UPDATE loans 
    SET 
        status_id = (SELECT id FROM loan_statuses WHERE name = 'completed'),
        completed_date = NEW.payment_date
    WHERE id = NEW.loan_id 
    AND amount_paid >= total_amount;
END$$

-- Trigger to prevent deleting users with related data
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
END$$

-- Trigger to automatically create notification for members
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
END$$

DELIMITER ;

-- =====================================================
-- CREATE INDEXES FOR PERFORMANCE
-- =====================================================

-- Additional indexes for common queries
CREATE INDEX idx_transactions_member_date ON transactions(member_id, transaction_date);
CREATE INDEX idx_transactions_status_date ON transactions(status_id, transaction_date);
CREATE INDEX idx_loans_member_status ON loans(member_id, status_id);
CREATE INDEX idx_loans_dates ON loans(application_date, approval_date, disbursement_date);
CREATE INDEX idx_shares_member_status ON shares(member_id, status_id);
CREATE INDEX idx_dividends_member_status ON member_dividends(member_id, status);
CREATE INDEX idx_projects_status_date ON projects(status_id, start_date);
CREATE INDEX idx_meetings_status_date ON meetings(status_id, scheduled_at);
CREATE INDEX idx_documents_member_expiry ON documents(member_id, expiry_date);
CREATE INDEX idx_notifications_member_read ON notification_receipts(member_id, is_read);
CREATE INDEX idx_audit_logs_entity ON audit_logs(entity_type_id, entity_id, created_at);
CREATE INDEX idx_audit_logs_user_date ON audit_logs(user_id, created_at);

-- =====================================================
-- CREATE VIEWS FOR REPORTING
-- =====================================================

-- Member Financial Report View
CREATE VIEW v_member_financial_report AS
SELECT 
    m.id,
    m.member_number,
    m.full_name,
    m.primary_phone,
    m.email,
    
    -- Savings Summary
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
    
    -- Loan Summary
    COALESCE((
        SELECT SUM(balance_due) 
        FROM loans 
        WHERE member_id = m.id 
        AND status_id IN (
            SELECT id FROM loan_statuses 
            WHERE name IN ('disbursed', 'approved')
        )
    ), 0) as outstanding_loans,
    
    -- Share Summary
    COALESCE((
        SELECT SUM(total_value) 
        FROM shares 
        WHERE member_id = m.id 
        AND status_id = (SELECT id FROM share_statuses WHERE name = 'active')
    ), 0) as share_value,
    
    -- Dividends Received
    COALESCE((
        SELECT SUM(net_amount) 
        FROM member_dividends 
        WHERE member_id = m.id 
        AND status = 'paid'
    ), 0) as total_dividends,
    
    -- Current Balance (Net Worth)
    COALESCE((
        SELECT SUM(
            CASE WHEN tt.impact = 'credit' THEN t.amount 
                 ELSE -t.amount 
            END
        ) 
        FROM transactions t
        JOIN transaction_types tt ON t.transaction_type_id = tt.id
        WHERE t.member_id = m.id 
        AND t.status = 'completed'
    ), 0) - COALESCE((
        SELECT SUM(balance_due) 
        FROM loans 
        WHERE member_id = m.id 
        AND status_id IN (
            SELECT id FROM loan_statuses 
            WHERE name IN ('disbursed', 'approved')
        )
    ), 0) as net_worth

FROM members m;

-- Loan Performance Report View
CREATE VIEW v_loan_performance AS
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
    
    -- Calculate if loan is performing well
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
JOIN loan_statuses ls ON l.status_id = ls.id;

-- Transaction Volume Report View
CREATE VIEW v_transaction_volume AS
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
GROUP BY DATE(t.transaction_date), tt.name, tc.name;

-- =====================================================
-- FINAL CHECKS
-- =====================================================

-- Show all tables
SHOW TABLES;

-- Show all views
SHOW FULL TABLES WHERE TABLE_TYPE LIKE 'VIEW';

-- Check foreign key constraints
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_SCHEMA = 'bss_system'
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;

-- Check indexes
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME,
    SEQ_IN_INDEX,
    NON_UNIQUE
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'bss_system'
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

SET FOREIGN_KEY_CHECKS = 1;
