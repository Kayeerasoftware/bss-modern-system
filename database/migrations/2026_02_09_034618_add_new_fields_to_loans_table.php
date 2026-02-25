<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('interest_rate', 5, 2)->default(10.00)->after('repayment_months');
            $table->decimal('processing_fee', 15, 2)->default(0)->after('interest');
            $table->text('applicant_comment')->nullable()->after('purpose');
            $table->string('guarantor_1_name')->nullable()->after('applicant_comment');
            $table->string('guarantor_1_phone')->nullable()->after('guarantor_1_name');
            $table->string('guarantor_2_name')->nullable()->after('guarantor_1_phone');
            $table->string('guarantor_2_phone')->nullable()->after('guarantor_2_name');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'interest_rate',
                'processing_fee',
                'applicant_comment',
                'guarantor_1_name',
                'guarantor_1_phone',
                'guarantor_2_name',
                'guarantor_2_phone',
            ]);
        });
    }
};
