# BSS API Endpoints

## Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `POST /api/auth/logout` - User logout
- `POST /api/auth/refresh` - Refresh token

## Admin Endpoints
- `GET /api/admin/dashboard` - Admin dashboard data
- `GET /api/users` - List all users
- `POST /api/users` - Create new user
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user

## Members
- `GET /api/members` - List all members
- `POST /api/members` - Create new member
- `GET /api/members/{id}` - Get member details
- `PUT /api/members/{id}` - Update member
- `DELETE /api/members/{id}` - Delete member
- `GET /api/members/next-id` - Get next member ID

## Transactions
- `GET /api/transactions` - List transactions
- `POST /api/transactions` - Create transaction
- `GET /api/transactions/{id}` - Get transaction details

## Loans
- `GET /api/loans` - List loans
- `POST /api/loans` - Create loan
- `GET /api/loans/{id}` - Get loan details
- `PUT /api/loans/{id}` - Update loan
- `POST /api/loans/{id}/approve` - Approve loan
- `POST /api/loans/{id}/reject` - Reject loan

## Projects
- `GET /api/projects` - List projects
- `POST /api/projects` - Create project
- `GET /api/projects/{id}` - Get project details
- `PUT /api/projects/{id}` - Update project
- `DELETE /api/projects/{id}` - Delete project

## Reports
- `GET /api/reports` - List reports
- `POST /api/reports` - Generate report
- `GET /api/reports/{id}` - View report
- `GET /api/reports/recent` - Recent reports

## System
- `GET /api/system/health` - System health check
- `GET /api/settings` - Get system settings
- `PUT /api/settings` - Update settings
- `GET /api/audit-logs` - Get audit logs
- `GET /api/backups` - List backups
- `POST /api/backups` - Create backup

## Notifications
- `GET /api/notifications` - List notifications
- `GET /api/notifications/stats` - Notification statistics
- `GET /api/notifications/history` - Notification history
- `POST /api/notifications/mark-read` - Mark as read

## Roles & Permissions
- `GET /api/roles` - List roles
- `GET /api/permissions/role/{role}` - Get role permissions
- `POST /api/roles` - Create role
- `PUT /api/roles/{id}` - Update role

## Financial
- `GET /api/financial-summary` - Financial summary
- `GET /api/deposits` - List deposits
- `POST /api/deposits` - Create deposit
- `GET /api/withdrawals` - List withdrawals
- `POST /api/withdrawals` - Create withdrawal

## Dashboard
- `GET /api/dashboard-data` - Dashboard data
- `GET /api/analytics` - Analytics data
