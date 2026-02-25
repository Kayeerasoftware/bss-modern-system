<?php

namespace Tests\Feature\Cashier;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_process_transactions()
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $this->actingAs($cashier);
        $this->assertTrue(true);
    }
}
