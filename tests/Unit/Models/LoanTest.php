<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    public function test_loan_can_be_created()
    {
        $loan = Loan::factory()->create();
        $this->assertDatabaseHas('loans', ['id' => $loan->id]);
    }
}
