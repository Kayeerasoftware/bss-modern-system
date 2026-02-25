<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('settings_min_interest_rate', 5, 2)->nullable()->after('guarantor_2_phone');
            $table->decimal('settings_max_interest_rate', 5, 2)->nullable()->after('settings_min_interest_rate');
            $table->decimal('settings_min_loan_amount', 15, 2)->nullable()->after('settings_max_interest_rate');
            $table->decimal('settings_max_loan_amount', 15, 2)->nullable()->after('settings_min_loan_amount');
            $table->integer('settings_max_loan_to_savings_ratio')->nullable()->after('settings_max_loan_amount');
            $table->integer('settings_min_repayment_months')->nullable()->after('settings_max_loan_to_savings_ratio');
            $table->integer('settings_max_repayment_months')->nullable()->after('settings_min_repayment_months');
            $table->integer('settings_default_repayment_months')->nullable()->after('settings_max_repayment_months');
            $table->decimal('settings_processing_fee_percentage', 5, 2)->nullable()->after('settings_default_repayment_months');
            $table->decimal('settings_late_payment_penalty', 5, 2)->nullable()->after('settings_processing_fee_percentage');
            $table->integer('settings_grace_period_days')->nullable()->after('settings_late_payment_penalty');
            $table->decimal('settings_auto_approve_amount', 15, 2)->nullable()->after('settings_grace_period_days');
            $table->boolean('settings_require_guarantors')->nullable()->after('settings_auto_approve_amount');
            $table->integer('settings_guarantors_required')->nullable()->after('settings_require_guarantors');
            $table->boolean('settings_email_notifications')->nullable()->after('settings_guarantors_required');
            $table->boolean('settings_sms_notifications')->nullable()->after('settings_email_notifications');
            $table->integer('settings_payment_reminder_days')->nullable()->after('settings_sms_notifications');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'settings_min_interest_rate',
                'settings_max_interest_rate',
                'settings_min_loan_amount',
                'settings_max_loan_amount',
                'settings_max_loan_to_savings_ratio',
                'settings_min_repayment_months',
                'settings_max_repayment_months',
                'settings_default_repayment_months',
                'settings_processing_fee_percentage',
                'settings_late_payment_penalty',
                'settings_grace_period_days',
                'settings_auto_approve_amount',
                'settings_require_guarantors',
                'settings_guarantors_required',
                'settings_email_notifications',
                'settings_sms_notifications',
                'settings_payment_reminder_days',
            ]);
        });
    }
};
