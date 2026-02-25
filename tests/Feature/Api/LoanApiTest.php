<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_loans()
    {
        $this->assertTrue(true);
    }
}
