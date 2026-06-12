#!/bin/bash

echo "Seeding demo data..."

php artisan migrate:fresh

php artisan db:seed --class=UserSeeder
php artisan db:seed --class=MemberSeeder
php artisan db:seed --class=TransactionSeeder
php artisan db:seed --class=LoanSeeder
php artisan db:seed --class=SavingsSeeder

echo "Demo data seeded successfully!"
