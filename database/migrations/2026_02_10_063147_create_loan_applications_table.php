<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->string('member_id');
            $table->decimal('amount', 15, 2);
            $table->integer('duration');
            $table->text('purpose');
            $table->string('loan_type')->nullable();
            $table->string('employment_status')->nullable();
            $table->decimal('monthly_income', 15, 2)->nullable();
            $table->string('employer_name')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('approval_comment')->nullable();
            $table->text('applicant_comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};