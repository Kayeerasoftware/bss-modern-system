# BSS API Examples

## Authentication

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@bss.com",
    "password": "password"
  }'
```

Response:
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@bss.com",
    "role": "admin"
  }
}
```

## Members

### Create Member
```bash
curl -X POST http://localhost:8000/api/members \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+256700000000",
    "address": "Kampala, Uganda"
  }'
```

### Get Member
```bash
curl -X GET http://localhost:8000/api/members/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Loans

### Create Loan
```bash
curl -X POST http://localhost:8000/api/loans \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "member_id": 1,
    "amount": 5000000,
    "interest_rate": 5.5,
    "duration_months": 12,
    "purpose": "Business expansion"
  }'
```

### Approve Loan
```bash
curl -X POST http://localhost:8000/api/loans/1/approve \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "approved_amount": 5000000,
    "notes": "Approved for business expansion"
  }'
```

## Transactions

### Create Deposit
```bash
curl -X POST http://localhost:8000/api/deposits \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "member_id": 1,
    "amount": 100000,
    "payment_method": "cash",
    "reference": "DEP-2024-001"
  }'
```

## Reports

### Generate Report
```bash
curl -X POST http://localhost:8000/api/reports \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "financial",
    "start_date": "2024-01-01",
    "end_date": "2024-12-31",
    "format": "pdf"
  }'
```

## System Health
```bash
curl -X GET http://localhost:8000/api/system/health \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Response:
```json
{
  "status": "healthy",
  "database": "connected",
  "cache": "operational",
  "queue": "running",
  "timestamp": "2024-02-06T04:00:00Z"
}
```
