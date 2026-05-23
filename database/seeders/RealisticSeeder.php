<?php

namespace Database\Seeders;

use App\Services\System\AccountNumberService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RealisticSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $now = now();

        $this->seedRoles();
        $roleIds = DB::table('roles')->pluck('id', 'name')->toArray();

        $this->seedCurrencies();
        $currencyId = (int) DB::table('currencies')->where('code', 'UGX')->value('id');

        $this->seedPaymentMethods();
        $paymentMethodIds = DB::table('payment_methods')->pluck('id', 'name')->toArray();

        $this->seedNotificationTypes();

        $this->seedTransactionStatuses();
        $transactionStatusIds = DB::table('transaction_statuses')->pluck('id', 'name')->toArray();

        $this->seedTransactionTypes();
        $transactionTypeIds = DB::table('transaction_types')->pluck('id', 'name')->toArray();

        $this->seedTransactionCategories($transactionTypeIds);
        $transactionCategoryIds = DB::table('transaction_categories')->pluck('id', 'name')->toArray();

        $this->seedLoanStatuses();
        $loanStatusIds = DB::table('loan_statuses')->pluck('id', 'name')->toArray();

        $this->seedLoanTypes();
        $loanTypeIds = DB::table('loan_types')->pluck('id', 'name')->toArray();

        $planId = $this->seedSavingsPlans();

        $adminId = $this->createUser('admin', 'admin@bss.local', $roleIds['admin'] ?? 1, null);
        $cashierId = $this->createUser('cashier', 'cashier@bss.local', $roleIds['cashier'] ?? 1, $adminId);
        $ceoId = $this->createUser('ceo', 'ceo@bss.local', $roleIds['ceo'] ?? 1, $adminId);
        $tdId = $this->createUser('treasurer', 'treasurer@bss.local', $roleIds['td'] ?? 1, $adminId);
        $shareholderId = $this->createUser('shareholder', 'shareholder@bss.local', $roleIds['shareholder'] ?? 1, $adminId);

        $systemUsers = [
            ['id' => $adminId, 'role' => 'admin', 'first' => 'System', 'last' => 'Admin'],
            ['id' => $cashierId, 'role' => 'cashier', 'first' => 'Grace', 'last' => 'Nakato'],
            ['id' => $ceoId, 'role' => 'ceo', 'first' => 'Michael', 'last' => 'Ssemanda'],
            ['id' => $tdId, 'role' => 'td', 'first' => 'James', 'last' => 'Kato'],
            ['id' => $shareholderId, 'role' => 'shareholder', 'first' => 'Sarah', 'last' => 'Achieng'],
        ];

        $memberIds = [];
        foreach ($systemUsers as $user) {
            $memberIds[] = $this->ensureMemberForUser(
                $user['id'],
                $user['first'],
                $user['last'],
                $roleIds[$user['role']] ?? ($roleIds['client'] ?? 1),
                $adminId
            );
        }

        $clientCount = 40;
        for ($i = 0; $i < $clientCount; $i++) {
            $first = $faker->firstName;
            $last = $faker->lastName;
            $email = strtolower($first . '.' . $last . $i . '@example.com');
            $username = strtolower($first . '.' . $last . $i);
            $userId = $this->createUser($username, $email, $roleIds['client'] ?? 1, $adminId);

            $memberId = $this->ensureMemberForUser(
                $userId,
                $first,
                $last,
                $roleIds['client'] ?? 1,
                $adminId,
                $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d')
            );
            $memberIds[] = $memberId;
        }

        $memberIds = array_values(array_unique(array_filter($memberIds)));
        $memberBalances = [];

        $this->disableTransactionInsertTrigger();

        foreach ($memberIds as $memberId) {
            $openingDate = $faker->dateTimeBetween('-18 months', 'now')->format('Y-m-d');
            $accountNumber = AccountNumberService::generateSavingsAccountNumber();
            $accountId = DB::table('savings_accounts')->insertGetId([
                'account_number' => $accountNumber,
                'member_id' => $memberId,
                'plan_id' => $planId,
                'account_name' => 'Member Savings',
                'opening_balance' => 0,
                'current_balance' => 0,
                'available_balance' => 0,
                'opening_date' => $openingDate,
                'status' => 'active',
                'created_at' => $now,
            ]);

            $balance = 0.0;
            $txnCount = random_int(3, 8);
            for ($t = 0; $t < $txnCount; $t++) {
                $isDeposit = (bool) random_int(0, 1);
                $amount = (float) (random_int(2, 80) * 10000);

                if (!$isDeposit && $balance - $amount < 0) {
                    $isDeposit = true;
                }

                $typeName = $isDeposit ? 'deposit' : 'withdrawal';
                $categoryName = $isDeposit ? 'savings_deposit' : 'savings_withdrawal';
                $typeId = (int) ($transactionTypeIds[$typeName] ?? $transactionTypeIds['deposit']);
                $categoryId = (int) ($transactionCategoryIds[$categoryName] ?? $transactionCategoryIds['savings_deposit']);
                $statusId = (int) ($transactionStatusIds['completed'] ?? array_values($transactionStatusIds)[0]);

                $before = $balance;
                $after = $isDeposit ? $balance + $amount : $balance - $amount;
                $balance = $after;

                DB::table('transactions')->insert([
                    'transaction_number' => $this->makeTransactionNumber($now),
                    'member_id' => $memberId,
                    'transaction_type_id' => $typeId,
                    'category_id' => $categoryId,
                    'status_id' => $statusId,
                    'amount' => $amount,
                    'fee' => 0,
                    'tax_amount' => 0,
                    'commission' => 0,
                    'exchange_rate' => 1,
                    'currency_id' => $currencyId,
                    'balance_before' => $before,
                    'balance_after' => $after,
                    'payment_method_id' => (int) ($paymentMethodIds['cash'] ?? array_values($paymentMethodIds)[0]),
                    'reference_number' => 'REF-' . strtoupper(Str::random(8)),
                    'receipt_number' => 'RCT-' . strtoupper(Str::random(6)),
                    'channel' => $faker->randomElement(['cash', 'mobile_money', 'bank_transfer']),
                    'description' => $isDeposit ? 'Savings deposit' : 'Savings withdrawal',
                    'processed_by' => $cashierId,
                    'processed_at' => $faker->dateTimeBetween('-12 months', 'now'),
                    'transaction_date' => $faker->dateTimeBetween('-12 months', 'now'),
                    'created_at' => $now,
                ]);
            }

            DB::table('savings_accounts')
                ->where('id', $accountId)
                ->update([
                    'opening_balance' => $balance,
                    'current_balance' => $balance,
                    'available_balance' => $balance,
                ]);

            $memberBalances[$memberId] = $balance;
        }

        $this->enableTransactionInsertTrigger();

        $loanMembers = array_slice($memberIds, 0, min(10, count($memberIds)));
        foreach ($loanMembers as $memberId) {
            $principal = (float) (random_int(5, 50) * 100000);
            $interestRate = (float) $faker->randomElement([10, 12, 15, 18]);
            $repaymentMonths = (int) $faker->randomElement([6, 9, 12, 18, 24]);
            $totalInterest = round(($principal * $interestRate) / 100, 2);
            $statusName = $faker->randomElement(['approved', 'pending', 'completed']);
            $statusId = (int) ($loanStatusIds[$statusName] ?? array_values($loanStatusIds)[0]);
            $applicationDate = $faker->dateTimeBetween('-10 months', '-1 month')->format('Y-m-d');
            $approvalDate = $statusName !== 'pending' ? $faker->dateTimeBetween('-9 months', '-1 month')->format('Y-m-d') : null;
            $disbursementDate = $statusName === 'approved' || $statusName === 'completed'
                ? $faker->dateTimeBetween('-8 months', '-1 month')->format('Y-m-d')
                : null;

            $totalAmount = $principal + $totalInterest;
            $amountPaid = $statusName === 'completed' ? $totalAmount : round($totalAmount * $faker->randomFloat(2, 0.05, 0.6), 2);

            DB::table('loans')->insert([
                'loan_number' => $this->makeLoanNumber($now),
                'member_id' => $memberId,
                'loan_type_id' => (int) ($loanTypeIds['business'] ?? array_values($loanTypeIds)[0]),
                'principal_amount' => $principal,
                'interest_rate' => $interestRate,
                'interest_type' => 'fixed',
                'total_interest' => $totalInterest,
                'repayment_months' => $repaymentMonths,
                'processing_fee' => 0,
                'insurance_fee' => 0,
                'legal_fee' => 0,
                'other_fees' => 0,
                'application_date' => $applicationDate,
                'approval_date' => $approvalDate,
                'disbursement_date' => $disbursementDate,
                'first_payment_date' => $disbursementDate,
                'amount_paid' => $amountPaid,
                'status_id' => $statusId,
                'approved_by' => $adminId,
                'approved_at' => $approvalDate ? $approvalDate . ' 09:00:00' : null,
                'disbursed_by' => $cashierId,
                'disbursed_at' => $disbursementDate ? $disbursementDate . ' 10:00:00' : null,
                'disbursement_method_id' => (int) ($paymentMethodIds['mobile_money'] ?? array_values($paymentMethodIds)[0]),
                'created_at' => $now,
            ]);
        }
    }

    private function seedRoles(): void
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrator', 'priority' => 100],
            ['name' => 'ceo', 'display_name' => 'Chief Executive Officer', 'priority' => 90],
            ['name' => 'td', 'display_name' => 'Treasurer', 'priority' => 80],
            ['name' => 'cashier', 'display_name' => 'Cashier', 'priority' => 70],
            ['name' => 'shareholder', 'display_name' => 'Shareholder', 'priority' => 60],
            ['name' => 'client', 'display_name' => 'Client', 'priority' => 10],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                [
                    'display_name' => $role['display_name'],
                    'description' => $role['display_name'],
                    'priority' => $role['priority'],
                    'is_active' => 1,
                ]
            );
        }
    }

    private function seedCurrencies(): void
    {
        DB::table('currencies')->updateOrInsert(
            ['code' => 'UGX'],
            ['name' => 'Ugandan Shilling', 'symbol' => 'UGX', 'decimal_places' => 0, 'is_base' => 1, 'exchange_rate' => 1]
        );
        DB::table('currencies')->updateOrInsert(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_base' => 0, 'exchange_rate' => 3800]
        );
        DB::table('currencies')->updateOrInsert(
            ['code' => 'KES'],
            ['name' => 'Kenyan Shilling', 'symbol' => 'KSh', 'decimal_places' => 2, 'is_base' => 0, 'exchange_rate' => 27]
        );
    }

    private function seedPaymentMethods(): void
    {
        $methods = [
            ['name' => 'cash', 'display_name' => 'Cash'],
            ['name' => 'mobile_money', 'display_name' => 'Mobile Money'],
            ['name' => 'bank_transfer', 'display_name' => 'Bank Transfer'],
        ];

        foreach ($methods as $method) {
            DB::table('payment_methods')->updateOrInsert(
                ['name' => $method['name']],
                [
                    'display_name' => $method['display_name'],
                    'description' => $method['display_name'],
                    'requires_reference' => $method['name'] === 'cash' ? 0 : 1,
                    'is_active' => 1,
                ]
            );
        }
    }

    private function seedNotificationTypes(): void
    {
        $types = [
            ['name' => 'transaction', 'display_name' => 'Transaction Alert'],
            ['name' => 'system', 'display_name' => 'System Alert'],
            ['name' => 'loan', 'display_name' => 'Loan Update'],
            ['name' => 'savings', 'display_name' => 'Savings Update'],
        ];

        foreach ($types as $type) {
            DB::table('notification_types')->updateOrInsert(
                ['name' => $type['name']],
                [
                    'display_name' => $type['display_name'],
                    'description' => $type['display_name'],
                    'icon' => 'bell',
                    'color' => 'blue',
                    'is_active' => 1,
                ]
            );
        }
    }

    private function seedTransactionStatuses(): void
    {
        $statuses = [
            ['name' => 'pending', 'display_name' => 'Pending', 'is_final' => 0, 'sort_order' => 1],
            ['name' => 'completed', 'display_name' => 'Completed', 'is_final' => 1, 'sort_order' => 2],
            ['name' => 'failed', 'display_name' => 'Failed', 'is_final' => 1, 'sort_order' => 3],
            ['name' => 'reversed', 'display_name' => 'Reversed', 'is_final' => 1, 'sort_order' => 4],
        ];

        foreach ($statuses as $status) {
            DB::table('transaction_statuses')->updateOrInsert(
                ['name' => $status['name']],
                [
                    'display_name' => $status['display_name'],
                    'description' => $status['display_name'],
                    'color' => 'gray',
                    'is_final' => $status['is_final'],
                    'sort_order' => $status['sort_order'],
                ]
            );
        }
    }

    private function seedTransactionTypes(): void
    {
        $types = [
            ['name' => 'deposit', 'display_name' => 'Deposit', 'impact' => 'credit', 'affects_savings' => 1, 'affects_loan' => 0],
            ['name' => 'withdrawal', 'display_name' => 'Withdrawal', 'impact' => 'debit', 'affects_savings' => 1, 'affects_loan' => 0],
            ['name' => 'transfer', 'display_name' => 'Transfer', 'impact' => 'debit', 'affects_savings' => 1, 'affects_loan' => 0],
            ['name' => 'loan_payment', 'display_name' => 'Loan Payment', 'impact' => 'credit', 'affects_savings' => 0, 'affects_loan' => 1],
            ['name' => 'loan_disbursement', 'display_name' => 'Loan Disbursement', 'impact' => 'credit', 'affects_savings' => 1, 'affects_loan' => 1],
        ];

        foreach ($types as $type) {
            DB::table('transaction_types')->updateOrInsert(
                ['name' => $type['name']],
                [
                    'display_name' => $type['display_name'],
                    'description' => $type['display_name'],
                    'impact' => $type['impact'],
                    'affects_savings' => $type['affects_savings'],
                    'affects_loan' => $type['affects_loan'],
                    'is_active' => 1,
                ]
            );
        }
    }

    private function seedTransactionCategories(array $typeIds): void
    {
        $categories = [
            ['name' => 'savings_deposit', 'display_name' => 'Savings Deposit', 'type' => 'deposit'],
            ['name' => 'savings_withdrawal', 'display_name' => 'Savings Withdrawal', 'type' => 'withdrawal'],
            ['name' => 'transfer_in', 'display_name' => 'Transfer In', 'type' => 'deposit'],
            ['name' => 'transfer_out', 'display_name' => 'Transfer Out', 'type' => 'withdrawal'],
            ['name' => 'loan_payment', 'display_name' => 'Loan Payment', 'type' => 'loan_payment'],
            ['name' => 'loan_disbursement', 'display_name' => 'Loan Disbursement', 'type' => 'loan_disbursement'],
        ];

        foreach ($categories as $category) {
            $typeId = (int) ($typeIds[$category['type']] ?? array_values($typeIds)[0]);
            DB::table('transaction_categories')->updateOrInsert(
                ['name' => $category['name']],
                [
                    'display_name' => $category['display_name'],
                    'transaction_type_id' => $typeId,
                    'description' => $category['display_name'],
                    'is_active' => 1,
                ]
            );
        }
    }

    private function seedLoanStatuses(): void
    {
        $statuses = [
            ['name' => 'pending', 'display_name' => 'Pending'],
            ['name' => 'approved', 'display_name' => 'Approved'],
            ['name' => 'disbursed', 'display_name' => 'Disbursed'],
            ['name' => 'rejected', 'display_name' => 'Rejected'],
            ['name' => 'completed', 'display_name' => 'Completed'],
        ];

        foreach ($statuses as $status) {
            DB::table('loan_statuses')->updateOrInsert(
                ['name' => $status['name']],
                [
                    'display_name' => $status['display_name'],
                    'description' => $status['display_name'],
                    'color' => 'gray',
                    'is_active' => 1,
                ]
            );
        }
    }

    private function seedLoanTypes(): void
    {
        $types = [
            ['name' => 'business', 'description' => 'Small business loan', 'min_amount' => 100000, 'max_amount' => 10000000, 'rate' => 12],
            ['name' => 'school_fees', 'description' => 'Education loan', 'min_amount' => 50000, 'max_amount' => 5000000, 'rate' => 10],
            ['name' => 'emergency', 'description' => 'Emergency loan', 'min_amount' => 50000, 'max_amount' => 2000000, 'rate' => 15],
        ];

        foreach ($types as $type) {
            DB::table('loan_types')->updateOrInsert(
                ['name' => $type['name']],
                [
                    'description' => $type['description'],
                    'min_amount' => $type['min_amount'],
                    'max_amount' => $type['max_amount'],
                    'default_interest_rate' => $type['rate'],
                    'min_repayment_months' => 3,
                    'max_repayment_months' => 24,
                    'requires_guarantors' => 0,
                    'guarantors_required' => 0,
                    'is_active' => 1,
                ]
            );
        }
    }

    private function seedSavingsPlans(): int
    {
        DB::table('savings_plan_types')->updateOrInsert(
            ['name' => 'regular_savings'],
            [
                'description' => 'Standard savings plan',
                'min_balance' => 0,
                'interest_rate' => 2.5,
                'interest_calculation' => 'monthly',
                'is_active' => 1,
            ]
        );

        $planTypeId = (int) DB::table('savings_plan_types')->where('name', 'regular_savings')->value('id');

        DB::table('savings_plans')->updateOrInsert(
            ['name' => 'Regular Savings'],
            [
                'plan_type_id' => $planTypeId,
                'description' => 'Everyday savings plan',
                'minimum_balance' => 0,
                'interest_rate' => 2.5,
                'interest_calculation' => 'monthly',
                'interest_payout' => 'compound',
                'monthly_fee' => 0,
                'withdrawal_fee_percentage' => 0,
                'withdrawal_fee_fixed' => 0,
                'early_withdrawal_penalty' => 0,
                'is_taxable' => 0,
                'tax_rate' => 0,
                'allows_overdraft' => 0,
                'is_active' => 1,
            ]
        );

        return (int) DB::table('savings_plans')->where('name', 'Regular Savings')->value('id');
    }

    private function createUser(string $username, string $email, int $roleId, ?int $createdBy): int
    {
        $existingId = DB::table('users')->where('email', $email)->value('id');
        if ($existingId) {
            return (int) $existingId;
        }

        return (int) DB::table('users')->insertGetId([
            'username' => $username,
            'email' => $email,
            'password' => Hash::make('password123'),
            'role_id' => $roleId,
            'status' => 'active',
            'created_by' => $createdBy,
            'created_at' => now(),
        ]);
    }

    private function ensureMemberForUser(
        int $userId,
        string $firstName,
        string $lastName,
        int $roleId,
        int $assignedBy,
        ?string $joinDate = null
    ): int {
        $memberId = DB::table('members')->where('user_id', $userId)->value('id');
        $userEmail = (string) DB::table('users')->where('id', $userId)->value('email');
        if (!$memberId) {
            $memberNumber = AccountNumberService::generateMemberAccountNumber();
            $memberId = DB::table('members')->insertGetId([
                'user_id' => $userId,
                'member_number' => $memberNumber,
                'member_account_number' => $memberNumber,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $userEmail !== '' ? $userEmail : null,
                'membership_status' => 'active',
                'join_date' => $joinDate ?? now()->toDateString(),
                'created_by' => $assignedBy,
                'created_at' => now(),
            ]);
        } else {
            $existing = DB::table('members')->where('id', $memberId)->first();
            $memberNumber = $existing?->member_account_number ?: $existing?->member_number ?: AccountNumberService::generateMemberAccountNumber();
            DB::table('members')
                ->where('id', $memberId)
                ->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'member_account_number' => $existing?->member_account_number ?: $memberNumber,
                    'member_number' => $existing?->member_number ?: $memberNumber,
                    'email' => $existing?->email ?: ($userEmail !== '' ? $userEmail : null),
                ]);
        }

        DB::table('member_roles')->updateOrInsert(
            ['member_id' => $memberId, 'role_id' => $roleId],
            ['is_primary' => 1, 'assigned_by' => $assignedBy, 'assigned_at' => now()]
        );

        return (int) $memberId;
    }

    private function makeTransactionNumber($now): string
    {
        return 'TXN-' . $now->format('Ymd') . '-' . strtoupper(Str::random(6));
    }

    private function makeLoanNumber($now): string
    {
        return 'LN-' . $now->format('Y') . '-' . strtoupper(Str::random(6));
    }

    private function disableTransactionInsertTrigger(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS `after_transaction_insert`');
    }

    private function enableTransactionInsertTrigger(): void
    {
        DB::unprepared(<<<'SQL'
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
END
SQL);
    }
}
