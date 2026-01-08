# BSS-2025 Investment Group System

A Laravel-based investment group management system for Bunya Secondary School Alumni.

## Features

- Multi-role authentication (Client, Shareholder, Cashier, Technical Director, CEO)
- Member management
- Loan application and approval system
- Financial transaction tracking
- Project management
- Bio data form collection
- Dashboard analytics

## Setup Instructions

### Prerequisites
- PHP 8.1 or higher
- Composer
- SQLite (included with PHP)

### Installation Steps

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Start Development Server**
   ```bash
   php artisan serve
   ```

5. **Access the Application**
   - Main Dashboard: http://localhost:8000
   - Bio Data Form: http://localhost:8000/bio-data-form

### Default Login Credentials

**Cashier:**
- Email: lydia@bssgroup.com
- Password: lydia

**Technical Director:**
- Email: mathias@bssgroup.com
- Password: mathias

**CEO & Chairperson:**
- Email: raymon@bssgroup.com
- Password: raymon

### Quick Setup (Windows)

Run the provided batch file:
```bash
setup.bat
```

## System Architecture

### Models
- **Member**: User authentication and member data
- **Loan**: Loan applications and approvals
- **Transaction**: Financial transactions
- **Project**: Investment projects
- **BioData**: Member bio data forms

### Controllers
- **AuthController**: Authentication (login, register, logout)
- **DashboardController**: Main dashboard functionality
- **MemberController**: Member management
- **BioDataController**: Bio data form handling

### Key Features by Role

**Client:**
- View group overview
- Apply for loans
- View personal savings and loans

**Shareholder:**
- View all members
- View member portfolios
- Apply for loans

**Cashier:**
- Financial summary dashboard
- Approve/reject loans
- Record transactions
- View pending loans

**Technical Director:**
- Manage projects
- View project progress
- Team communication

**CEO & Chairperson:**
- Complete system overview
- Member management
- Activity logs
- Send announcements

## API Endpoints

### Authentication
- POST `/api/login` - User login
- POST `/api/register` - User registration
- POST `/api/logout` - User logout

### Dashboard
- GET `/api/dashboard-data` - Get dashboard data
- POST `/api/apply-loan` - Apply for loan
- POST `/api/approve-loan/{loanId}` - Approve loan
- POST `/api/reject-loan/{loanId}` - Reject loan
- POST `/api/record-transaction` - Record transaction
- POST `/api/create-project` - Create project

### Member Management
- POST `/api/members` - Add member
- PUT `/api/members/{memberId}` - Update member
- DELETE `/api/members/{memberId}` - Remove member

### Bio Data
- POST `/api/bio-data` - Submit bio data
- GET `/api/bio-data` - Get bio data

## Database Schema

The system uses SQLite database with the following tables:
- `members` - Member information and authentication
- `loans` - Loan applications and status
- `transactions` - Financial transactions
- `projects` - Investment projects
- `bio_data` - Member bio data forms

## Security Features

- CSRF protection
- Password hashing
- Role-based access control
- Input validation
- SQL injection prevention

## Troubleshooting

1. **Migration Issues**: Run `php artisan migrate:fresh --seed`
2. **Permission Issues**: Ensure storage and bootstrap/cache directories are writable
3. **Database Issues**: Check .env file database configuration
4. **Server Issues**: Ensure PHP and required extensions are installed

## Support

For technical support, contact the development team.