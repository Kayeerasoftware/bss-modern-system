<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->enum('role', ['admin', 'client', 'cashier', 'td', 'ceo', 'shareholder']);
            $table->timestamps();
            
            $table->unique(['member_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_roles');
    }
};
