<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->integer('duration')->after('amount')->nullable();
            $table->string('loan_type')->after('purpose')->nullable();
            $table->string('employment_status')->after('loan_type')->nullable();
            $table->decimal('monthly_income', 15, 2)->after('employment_status')->nullable();
            $table->string('employer_name')->after('monthly_income')->nullable();
            $table->string('emergency_contact_name')->after('employer_name')->nullable();
            $table->string('emergency_contact_phone')->after('emergency_contact_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn([
                'duration', 'loan_type', 'employment_status', 'monthly_income', 
                'employer_name', 'emergency_contact_name', 'emergency_contact_phone'
            ]);
        });
    }
};