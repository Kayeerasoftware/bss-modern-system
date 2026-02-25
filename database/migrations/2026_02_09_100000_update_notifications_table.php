<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'recipients_count')) {
                $table->integer('recipients_count')->default(0)->after('message');
            }
            if (!Schema::hasColumn('notifications', 'status')) {
                $table->string('status')->default('sent')->after('recipients_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['recipients_count', 'status']);
        });
    }
};
