<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_id')->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('profile_picture')->nullable();
            $table->string('location')->nullable();
            $table->string('occupation')->nullable();
            $table->string('contact')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'client', 'cashier', 'td', 'ceo', 'shareholder'])->default('client');
            $table->decimal('savings', 15, 2)->default(0);
            $table->decimal('loan', 15, 2)->default(0);
            $table->decimal('savings_balance', 15, 2)->default(0);
            $table->string('status')->default('active');
            
            // Foreign key to users table
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['member_id', 'email']);
            $table->index('role');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};