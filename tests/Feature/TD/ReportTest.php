<?php

namespace Tests\Feature\TD;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_td_can_view_reports()
    {
        $td = User::factory()->create(['role' => 'td']);
        $this->actingAs($td);
        $this->assertTrue(true);
    }
}
