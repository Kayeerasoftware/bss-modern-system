<?php

namespace Tests\Feature\CEO;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_ceo_can_access_dashboard()
    {
        $ceo = User::factory()->create(['role' => 'ceo']);
        $response = $this->actingAs($ceo)->get('/ceo/dashboard');
        $response->assertStatus(200);
    }
}
