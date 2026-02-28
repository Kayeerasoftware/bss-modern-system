<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('monthly_payment');
            }

            if (!Schema::hasColumn('loans', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('updated_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('loans', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
        });
    }
};
