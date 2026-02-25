<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_be_created()
    {
        $member = Member::factory()->create();
        $this->assertDatabaseHas('members', ['id' => $member->id]);
    }
}
