<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Role and is_active columns are already created in the initial migration
        // This migration is kept for reference but does nothing
    }

    public function down()
    {
        // Nothing to rollback
    }
};