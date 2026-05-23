# BSS Account Numbering System Implementation

## Overview
The BSS system now uses standardized account numbering formats:
- **Member Account**: `BSS-C15-000x` format
- **Savings Account**: `SAV-BSS-C15-000x` format

## Changes Made

### 1. Database Changes
- **Migration**: `2026_03_16_000001_add_account_numbers_to_system.php`
  - Added `member_account_number` column to `members` table
  - Updated existing records with new format
  - Added database index for performance

### 2. Service Layer
- **AccountNumberService**: `app/Services/System/AccountNumberService.php`
  - `generateMemberAccountNumber()`: Creates BSS-C15-000x format
  - `generateSavingsAccountNumber()`: Creates SAV-BSS-C15-000x format
  - Validation methods for both formats

### 3. Model Updates
- **Member Model**: Auto-generates account numbers on creation
- **SavingsAccount Model**: Auto-generates account numbers on creation
- Updated `getMemberIdAttribute()` to prioritize new format

### 4. Controller Updates
- **Admin/MemberController**: Updated search and display logic
- Added `member_account_number` to search queries
- Updated member creation to generate both legacy and new numbers

### 5. View Updates
- **Admin Members Table**: Shows new account numbers
- **Member Profile**: Displays both new and legacy numbers
- **Loan Details**: Shows member account number
- **Various forms**: Updated to use new format

### 6. Helper Functions
- Updated `generate_member_id()` to use new service
- Added helper functions for account number generation
- Maintained backward compatibility

### 7. Console Command
- **UpdateAccountNumbers**: `php artisan bss:update-account-numbers`
  - Updates existing records to new format
  - Provides summary of changes

## Account Number Formats

### Member Accounts
- **Format**: `BSS-C15-000x`
- **Example**: `BSS-C15-0001`, `BSS-C15-0002`, etc.
- **Sequential**: Numbers increment automatically
- **Unique**: Each member gets a unique number

### Savings Accounts
- **Format**: `SAV-BSS-C15-000x`
- **Example**: `SAV-BSS-C15-0001`, `SAV-BSS-C15-0002`, etc.
- **Independent**: Savings account numbers are independent of member numbers
- **Different timing**: Created at different times than member accounts

## Usage Examples

### Creating New Members
```php
$member = new Member();
// member_account_number is auto-generated as BSS-C15-000x
$member->save();
```

### Creating Savings Accounts
```php
$savingsAccount = new SavingsAccount();
// account_number is auto-generated as SAV-BSS-C15-000x
$savingsAccount->save();
```

### Searching Members
```php
// Search by new account number
Member::where('member_account_number', 'BSS-C15-0001')->first();

// Search by legacy number (still works)
Member::where('member_number', 'BSS-26-0001')->first();
```

## Backward Compatibility
- Legacy `member_number` field is preserved
- Old search functionality still works
- Views show new format but maintain legacy support
- API responses include both formats

## Database Schema
```sql
-- Members table
ALTER TABLE members ADD COLUMN member_account_number VARCHAR(20) UNIQUE;
ALTER TABLE members ADD INDEX idx_members_account_number (member_account_number);

-- Savings accounts table (account_number field updated)
-- Format changed from old format to SAV-BSS-C15-000x
```

## Migration Status
✅ Database migration completed
✅ Existing records updated
✅ New account generation implemented
✅ Views updated to show new format
✅ Search functionality updated
✅ Backward compatibility maintained

## Next Steps
1. Test all functionality with new account numbers
2. Update any remaining views that might show old format
3. Consider deprecating legacy member_number field in future versions
4. Update API documentation to reflect new formats