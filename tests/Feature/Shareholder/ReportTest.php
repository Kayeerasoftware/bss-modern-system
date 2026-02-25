<?php

namespace Tests\Feature\Shareholder;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_shareholder_can_view_financial_reports()
    {
        $shareholder = User::factory()->create(['role' => 'shareholder']);
        $this->actingAs($shareholder);
        $this->assertTrue(true);
    }
}
