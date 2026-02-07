<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fundraising_contributions', function (Blueprint $table) {
            $table->id();
            $table->string('contribution_id')->unique();
            $table->foreignId('fundraising_id')->constrained()->onDelete('cascade');
            $table->string('contributor_name')->nullable();
            $table->string('contributor_email')->nullable();
            $table->string('contributor_phone')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->default('cash');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fundraising_contributions');
    }
};
