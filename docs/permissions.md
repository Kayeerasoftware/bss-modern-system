# Role Permissions Matrix

## Admin
**Full System Access**
- ✅ User Management (Create, Read, Update, Delete)
- ✅ Member Management (All operations)
- ✅ Transaction Management (All operations)
- ✅ Loan Management (Approve, Reject, Manage)
- ✅ Savings Management (All operations)
- ✅ System Configuration
- ✅ Reports (All types)
- ✅ Backup & Restore
- ✅ Audit Logs
- ✅ Role Assignment

## Cashier
**Daily Operations**
- ✅ Member Registration
- ✅ Transaction Processing (Deposits, Withdrawals)
- ✅ View Member Details
- ✅ Print Receipts
- ✅ Daily Reports
- ❌ Loan Approval
- ❌ System Configuration
- ❌ User Management

## TD (Technical Director)
**Technical Oversight**
- ✅ System Reports
- ✅ Data Analytics
- ✅ Member Statistics
- ✅ Transaction Reports
- ✅ Loan Reports
- ✅ Technical Documentation
- ✅ Database Backups
- ❌ Transaction Processing
- ❌ User Management
- ❌ System Configuration

## CEO
**Executive Dashboard**
- ✅ Financial Reports
- ✅ Executive Dashboard
- ✅ Strategic Analytics
- ✅ Member Overview
- ✅ Loan Portfolio
- ✅ Revenue Reports
- ✅ Approve Large Transactions
- ❌ Daily Operations
- ❌ System Configuration

## Shareholder
**Investment Tracking**
- ✅ Portfolio Overview
- ✅ Dividend History
- ✅ Investment Projects
- ✅ ROI Reports
- ✅ Financial Statements
- ✅ Personal Savings
- ✅ Loan Applications
- ❌ Other Members' Data
- ❌ System Operations

## Client
**Personal Account**
- ✅ View Personal Profile
- ✅ View Transaction History
- ✅ View Savings Balance
- ✅ Apply for Loans
- ✅ View Loan Status
- ✅ Update Profile
- ✅ Change Password
- ❌ Other Members' Data
- ❌ System Operations
- ❌ Reports

## Permission Implementation

### Middleware
```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin routes
});

Route::middleware(['auth', 'role:cashier'])->group(function () {
    // Cashier routes
});
```

### Policy-Based Authorization
```php
Gate::define('approve-loan', function ($user) {
    return in_array($user->role, ['admin', 'ceo']);
});

Gate::define('process-transaction', function ($user) {
    return in_array($user->role, ['admin', 'cashier']);
});
```

### Blade Directives
```blade
@can('approve-loan')
    <button>Approve Loan</button>
@endcan

@role('admin')
    <a href="/admin/settings">Settings</a>
@endrole
```
