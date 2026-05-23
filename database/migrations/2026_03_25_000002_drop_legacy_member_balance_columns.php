<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [];
        foreach (['savings', 'savings_balance', 'balance'] as $column) {
            if (Schema::hasColumn('members', $column)) {
                $columns[] = $column;
            }
        }

        if (!empty($columns)) {
            Schema::table('members', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    public function down(): void
    {
        $addSavings = !Schema::hasColumn('members', 'savings');
        $addSavingsBalance = !Schema::hasColumn('members', 'savings_balance');
        $addBalance = !Schema::hasColumn('members', 'balance');

        if (!($addSavings || $addSavingsBalance || $addBalance)) {
            return;
        }

        Schema::table('members', function (Blueprint $table) use ($addSavings, $addSavingsBalance, $addBalance) {
            if ($addSavings) {
                $table->decimal('savings', 15, 2)->default(0);
            }
            if ($addSavingsBalance) {
                $table->decimal('savings_balance', 15, 2)->default(0);
            }
            if ($addBalance) {
                $table->decimal('balance', 15, 2)->default(0);
            }
        });
    }
};
