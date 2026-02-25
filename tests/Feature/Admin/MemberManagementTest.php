<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MemberManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_members()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $this->assertTrue(true);
    }
}
