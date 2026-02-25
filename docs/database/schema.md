# Database Schema

## Users Table
- id (PK)
- name
- email (unique)
- password
- role (admin, cashier, td, ceo, shareholder, client)
- created_at
- updated_at

## Members Table
- id (PK)
- member_id (unique)
- full_name
- email (unique)
- phone
- location
- occupation
- profile_picture
- created_at
- updated_at

## Transactions Table
- id (PK)
- transaction_id (unique)
- member_id (FK)
- type (deposit, withdrawal, transfer)
- amount
- balance_before
- balance_after
- description
- status
- created_at
- updated_at

## Loans Table
- id (PK)
- loan_id (unique)
- member_id (FK)
- amount
- interest_rate
- repayment_months
- monthly_payment
- purpose
- status (pending, approved, rejected, completed)
- created_at
- updated_at

## Savings Table
- id (PK)
- member_id (FK)
- amount
- interest_earned
- created_at
- updated_at
