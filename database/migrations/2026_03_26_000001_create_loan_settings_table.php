<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('loan_settings')) {
            return;
        }

        Schema::create('loan_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_loan_available')->default(true);
            $table->decimal('default_interest_rate', 5, 2)->default(10);
            $table->decimal('min_interest_rate', 5, 2)->default(5);
            $table->decimal('max_interest_rate', 5, 2)->default(30);
            $table->decimal('min_loan_amount', 15, 2)->default(10000);
            $table->decimal('max_loan_amount', 15, 2)->default(10000000);
            $table->decimal('max_loan_to_savings_ratio', 8, 2)->default(300);
            $table->unsignedInteger('min_repayment_months')->default(3);
            $table->unsignedInteger('max_repayment_months')->default(60);
            $table->unsignedInteger('default_repayment_months')->default(12);
            $table->decimal('processing_fee_percentage', 5, 2)->default(2);
            $table->decimal('late_payment_penalty', 5, 2)->default(5);
            $table->unsignedInteger('grace_period_days')->default(7);
            $table->decimal('auto_approve_amount', 15, 2)->default(0);
            $table->boolean('require_guarantors')->default(false);
            $table->unsignedInteger('guarantors_required')->default(2);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(true);
            $table->unsignedInteger('payment_reminder_days')->default(3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_settings');
    }
};

