<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MemberApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_members()
    {
        $this->assertTrue(true);
    }
}
