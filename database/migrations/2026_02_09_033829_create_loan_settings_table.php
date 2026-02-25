<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_loan_available')->default(true);
            $table->decimal('default_interest_rate', 5, 2)->default(10.00);
            $table->decimal('min_interest_rate', 5, 2)->default(5.00);
            $table->decimal('max_interest_rate', 5, 2)->default(30.00);
            $table->decimal('min_loan_amount', 15, 2)->default(10000.00);
            $table->decimal('max_loan_amount', 15, 2)->default(10000000.00);
            $table->integer('max_loan_to_savings_ratio')->default(300);
            $table->integer('min_repayment_months')->default(3);
            $table->integer('max_repayment_months')->default(60);
            $table->integer('default_repayment_months')->default(12);
            $table->decimal('processing_fee_percentage', 5, 2)->default(2.00);
            $table->decimal('late_payment_penalty', 5, 2)->default(5.00);
            $table->integer('grace_period_days')->default(7);
            $table->decimal('auto_approve_amount', 15, 2)->default(0.00);
            $table->boolean('require_guarantors')->default(false);
            $table->integer('guarantors_required')->default(2);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(true);
            $table->integer('payment_reminder_days')->default(3);
            $table->timestamps();
        });

        // Insert default settings
        DB::table('loan_settings')->insert([
            'is_loan_available' => true,
            'default_interest_rate' => 10.00,
            'min_interest_rate' => 5.00,
            'max_interest_rate' => 30.00,
            'min_loan_amount' => 10000.00,
            'max_loan_amount' => 10000000.00,
            'max_loan_to_savings_ratio' => 300,
            'min_repayment_months' => 3,
            'max_repayment_months' => 60,
            'default_repayment_months' => 12,
            'processing_fee_percentage' => 2.00,
            'late_payment_penalty' => 5.00,
            'grace_period_days' => 7,
            'auto_approve_amount' => 0.00,
            'require_guarantors' => false,
            'guarantors_required' => 2,
            'email_notifications' => true,
            'sms_notifications' => true,
            'payment_reminder_days' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_settings');
    }
};
