<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = ['client', 'shareholder', 'cashier', 'td', 'ceo', 'admin'];
        
        foreach ($roles as $role) {
            DB::table('settings')->updateOrInsert(
                ['key' => "allow_registration_{$role}"],
                ['value' => '1', 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        $roles = ['client', 'shareholder', 'cashier', 'td', 'ceo', 'admin'];
        
        foreach ($roles as $role) {
            DB::table('settings')->where('key', "allow_registration_{$role}")->delete();
        }
    }
};
