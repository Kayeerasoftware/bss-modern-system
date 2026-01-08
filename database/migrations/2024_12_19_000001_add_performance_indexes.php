<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add basic indexes for performance
        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                if (!$this->hasIndex('members', 'members_member_id_index')) {
                    $table->index(['member_id']);
                }
                if (!$this->hasIndex('members', 'members_created_at_index')) {
                    $table->index(['created_at']);
                }
            });
        }

        if (Schema::hasTable('bio_data')) {
            Schema::table('bio_data', function (Blueprint $table) {
                if (!$this->hasIndex('bio_data', 'bio_data_nin_no_index')) {
                    $table->index(['nin_no']);
                }
                if (!$this->hasIndex('bio_data', 'bio_data_created_at_index')) {
                    $table->index(['created_at']);
                }
            });
        }
    }

    private function hasIndex($table, $index)
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $idx) {
            if ($idx->Key_name === $index) {
                return true;
            }
        }
        return false;
    }

    public function down()
    {
        // Drop indexes if they exist
        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropIndex(['member_id']);
                $table->dropIndex(['created_at']);
            });
        }

        if (Schema::hasTable('bio_data')) {
            Schema::table('bio_data', function (Blueprint $table) {
                $table->dropIndex(['nin_no']);
                $table->dropIndex(['created_at']);
            });
        }
    }
};