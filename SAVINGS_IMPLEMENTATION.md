# Savings Single Source of Truth Implementation

## Overview
This document describes the changes made to ensure that member savings in the BSS system have a single source of truth: the transactions table.

## Changes Made

### 1. Member Model (app/Models/Member.php)
- **Added `getFullNameAttribute()` accessor**: Constructs full name from first_name, middle_name, and last_name
- **Added `$appends` property**: Ensures full_name is always included when model is serialized
- **Existing `getSavingsAttribute()` method**: Already calculates savings from transactions table using:
  - Joins with transaction_types, transaction_categories, and transaction_statuses
  - Filters for completed transactions only
  - Includes relevant categories: savings_deposit, savings_withdrawal, transfer_in, transfer_out, fundraising_transfer, loan_disbursement
  - Calculates sum based on credit/debit impact from transaction_types

### 2. MemberController (app/Http/Controllers/Admin/MemberController.php)
- **Updated `index()` method**: 
  - Modified `$memberStats['totalSavings']` calculation to use transactions table
  - Updated search query to use CONCAT of first_name, middle_name, last_name instead of non-existent full_name column
  - Updated savings filter (savings_min/savings_max) to query transactions instead of savings_accounts
  - Updated sorting (savings_high/savings_low) to query transactions instead of savings_accounts
  - Fixed name sorting to use CONCAT of name fields
- **Updated `search()` method**: Fixed to use CONCAT for full name search

### 3. Members Index View (resources/views/admin/members/index.blade.php)
- **Updated stats card**: Changed from `$memberStats['totalSavings'] ?? $members->sum('savings')` to `$memberStats['totalSavings']`
- Ensures consistent display of total savings from the controller calculation

### 4. Members Table Partial (resources/views/admin/members/partials/table.blade.php)
- **No changes needed**: Already displays `$member->savings` which uses the accessor that gets data from transactions

### 5. CEO TransactionController (app/Http/Controllers/CEO/TransactionController.php)
- **Fixed method signature**: Updated `store()` method to match parent class signature by adding `TransactionPostingService $postingService` parameter

## How It Works

### Single Source of Truth: Transactions Table
All savings calculations now follow this logic:

```php
$amountSql = 'COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)';

$savings = DB::table('transactions')
    ->join('transaction_types', 'transactions.transaction_type_id', '=', 'transaction_types.id')
    ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
    ->join('transaction_statuses', 'transactions.status_id', '=', 'transaction_statuses.id')
    ->where('transaction_statuses.name', 'completed')
    ->whereIn('tc.name', [
        'savings_deposit', 
        'savings_withdrawal', 
        'transfer_in', 
        'transfer_out', 
        'fundraising_transfer', 
        'loan_disbursement'
    ])
    ->whereNull('transactions.deleted_at')
    ->selectRaw("COALESCE(SUM(CASE WHEN transaction_types.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as total")
    ->value('total');
```

### Key Points:
1. **Only completed transactions** are counted
2. **Credit transactions** (deposits, transfers in) add to savings
3. **Debit transactions** (withdrawals, transfers out) subtract from savings
4. **Net amount** is used when available, otherwise gross amount
5. **Soft-deleted transactions** are excluded

## Benefits
1. **Consistency**: All savings calculations use the same logic
2. **Accuracy**: Savings reflect actual transaction history
3. **Auditability**: Easy to trace savings back to individual transactions
4. **Maintainability**: Single calculation logic to maintain
5. **Real-time**: Savings update automatically when transactions are created
6. **Filtering & Sorting**: Savings filters and sorting now work correctly based on actual transaction data

## Testing Results
Tested with actual database:
- Total System Savings: 51,380,000.00 UGX
- Top 5 Members by Savings:
  - Gerard Leuschke: 3,370,000.00
  - Morris Towne: 3,140,000.00
  - Josue Bernier: 2,720,000.00
  - Carlotta Lindgren: 2,530,000.00
  - Nova Kunze: 2,340,000.00

## Fixed Issues
1. ✅ Savings now display correctly in the member table
2. ✅ New transactions immediately reflect in member savings
3. ✅ Total savings stat card shows accurate system-wide total
4. ✅ Savings filtering (min/max) works correctly
5. ✅ Savings sorting (high/low) works correctly
6. ✅ Search by member name works correctly
7. ✅ All queries use transactions as single source of truth

## Notes
- The `savings_accounts` table is no longer used for savings calculations
- The Member model's `getSavingsAttribute()` is called automatically when accessing `$member->savings`
- The controller calculates total savings for all members using the same logic for the stats card
- All filtering and sorting operations now use the same transaction-based calculation
