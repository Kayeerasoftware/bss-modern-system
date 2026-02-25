# BSS Investment Group - Resources Structure

## Complete Resources Organization

### Views Structure (resources/views/)

#### Layouts
- `layouts/app.blade.php` - Base application layout
- `layouts/admin.blade.php` - Admin panel layout
- `layouts/member.blade.php` - Member panel layout
- `layouts/auth.blade.php` - Authentication pages layout
- `layouts/guest.blade.php` - Guest/public pages layout

#### Authentication (auth/)
- `auth/login.blade.php` - Login page
- `auth/register.blade.php` - Registration page
- `auth/forgot-password.blade.php` - Password reset request
- `auth/reset-password.blade.php` - Password reset form

#### Admin Panel (admin/)

**Dashboard**
- `admin/dashboard.blade.php` - Main admin dashboard

**Members Management (admin/members/)**
- `admin/members/index.blade.php` - Members list
- `admin/members/create.blade.php` - Add new member
- `admin/members/edit.blade.php` - Edit member
- `admin/members/show.blade.php` - View member details
- `admin/members/import.blade.php` - Bulk import members

**Financial Management (admin/financial/)**
- `admin/financial/transactions.blade.php` - All transactions
- `admin/financial/deposits.blade.php` - Deposit records
- `admin/financial/withdrawals.blade.php` - Withdrawal records
- `admin/financial/transfers.blade.php` - Transfer records
- `admin/financial/reports.blade.php` - Financial reports

**Loans Management (admin/loans/)**
- `admin/loans/index.blade.php` - All loans
- `admin/loans/applications.blade.php` - Pending applications
- `admin/loans/approvals.blade.php` - Approved loans
- `admin/loans/repayments.blade.php` - Repayment tracking

**Projects Management (admin/projects/)**
- `admin/projects/index.blade.php` - Projects list
- `admin/projects/create.blade.php` - Create project
- `admin/projects/edit.blade.php` - Edit project
- `admin/projects/show.blade.php` - Project details

**Fundraising (admin/fundraising/)**
- `admin/fundraising/campaigns.blade.php` - Campaign list
- `admin/fundraising/create.blade.php` - Create campaign
- `admin/fundraising/show.blade.php` - Campaign details

**Users Management (admin/users/)**
- `admin/users/index.blade.php` - System users
- `admin/users/create.blade.php` - Add user
- `admin/users/edit.blade.php` - Edit user

**System Management (admin/system/)**
- `admin/system/settings.blade.php` - System settings
- `admin/system/audit-logs.blade.php` - Activity logs
- `admin/system/backups.blade.php` - Backup management
- `admin/system/health.blade.php` - System health monitor

**Notifications (admin/notifications/)**
- `admin/notifications/index.blade.php` - Notifications list
- `admin/notifications/create.blade.php` - Send notification
- `admin/notifications/history.blade.php` - Notification history

#### Member Panel (member/)

**Dashboard**
- `member/dashboard.blade.php` - Member dashboard
- `member/profile.blade.php` - Profile management

**Transactions (member/transactions/)**
- `member/transactions/history.blade.php` - Transaction history
- `member/transactions/statement.blade.php` - Account statement

**Loans (member/loans/)**
- `member/loans/apply.blade.php` - Apply for loan
- `member/loans/my-loans.blade.php` - My loans

**Projects (member/projects/)**
- `member/projects/my-projects.blade.php` - My project investments

#### Components (components/)

**Alerts (components/alerts/)**
- `components/alerts/success.blade.php` - Success message
- `components/alerts/error.blade.php` - Error message

**Forms (components/forms/)**
- `components/forms/input.blade.php` - Input field
- `components/forms/select.blade.php` - Select dropdown
- `components/forms/file-upload.blade.php` - File upload

**Modals (components/modals/)**
- `components/modals/confirm.blade.php` - Confirmation dialog
- `components/modals/form.blade.php` - Form modal
- `components/modals/view.blade.php` - View modal

**Cards (components/cards/)**
- `components/cards/stat-card.blade.php` - Statistics card
- `components/cards/info-card.blade.php` - Information card

**Tables (components/tables/)**
- `components/tables/data-table.blade.php` - Data table
- `components/tables/actions.blade.php` - Table actions

#### Error Pages (errors/)
- `errors/404.blade.php` - Page not found
- `errors/403.blade.php` - Access forbidden
- `errors/500.blade.php` - Server error

### JavaScript Structure (resources/js/)

#### Core
- `js/app.js` - Main application entry
- `js/bootstrap.js` - Bootstrap configuration

#### Admin (js/admin/)
- `js/admin/main.js` - Admin main functionality
- `js/admin/dashboard.js` - Dashboard specific
- `js/admin/members.js` - Members management
- `js/admin/loans.js` - Loans management
- `js/admin/financial.js` - Financial operations
- `js/admin/charts.js` - Chart initialization

#### Member (js/member/)
- `js/member/main.js` - Member main functionality
- `js/member/dashboard.js` - Member dashboard

#### Components (js/components/)
- `js/components/modal.js` - Modal functionality
- `js/components/form-validation.js` - Form validation
- `js/components/data-table.js` - Table operations
- `js/components/file-upload.js` - File upload handling

#### Utilities (js/utils/)
- `js/utils/api.js` - API request utilities
- `js/utils/formatters.js` - Data formatting
- `js/utils/helpers.js` - Helper functions

### CSS Structure (resources/css/)

#### Core
- `css/app.css` - Main application styles

#### Admin (css/admin/)
- `css/admin/admin.css` - Admin panel styles
- `css/admin/sidebar.css` - Sidebar styles

#### Member (css/member/)
- `css/member/member.css` - Member panel styles

#### Components (css/components/)
- `css/components/buttons.css` - Button styles
- `css/components/forms.css` - Form styles
- `css/components/tables.css` - Table styles

### Language Files (resources/lang/)

#### English (lang/en/)
- `lang/en/auth.php` - Authentication messages
- `lang/en/pagination.php` - Pagination text
- `lang/en/passwords.php` - Password reset messages
- `lang/en/validation.php` - Validation messages
- `lang/en/messages.php` - General messages

#### Swahili (lang/sw/)
- `lang/sw/auth.php` - Authentication messages (Swahili)
- `lang/sw/messages.php` - General messages (Swahili)

## Key Features

### Modular Organization
- Separated admin and member interfaces
- Reusable components
- Organized by functionality

### Scalability
- Easy to add new modules
- Clear separation of concerns
- Maintainable structure

### Best Practices
- Laravel conventions
- Component-based architecture
- Internationalization support

## Usage

### Creating New Views
Place views in appropriate directories:
- Admin features → `admin/[module]/`
- Member features → `member/[module]/`
- Shared components → `components/[type]/`

### Adding JavaScript
- Module-specific → `js/admin/` or `js/member/`
- Reusable components → `js/components/`
- Utilities → `js/utils/`

### Styling
- Module-specific → `css/admin/` or `css/member/`
- Component styles → `css/components/`

## Notes
- All views extend appropriate layouts
- Components are reusable across modules
- Language files support English and Swahili
- JavaScript modules use ES6 syntax
- CSS follows BEM-like naming conventions
