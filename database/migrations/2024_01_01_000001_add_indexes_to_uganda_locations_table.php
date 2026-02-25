<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('uganda_locations', function (Blueprint $table) {
            // Add indexes for better query performance
            $table->index(['type', 'name'], 'idx_type_name');
            $table->index(['type', 'parent_id'], 'idx_type_parent');
            $table->index(['parent_id'], 'idx_parent_id');
            $table->index(['name'], 'idx_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uganda_locations', function (Blueprint $table) {
            $table->dropIndex('idx_type_name');
            $table->dropIndex('idx_type_parent');
            $table->dropIndex('idx_parent_id');
            $table->dropIndex('idx_name');
        });
    }
};