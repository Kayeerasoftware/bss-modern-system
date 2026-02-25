<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_can_be_created()
    {
        $transaction = Transaction::factory()->create();
        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    }
}
