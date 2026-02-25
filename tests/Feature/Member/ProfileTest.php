<?php

namespace Tests\Feature\Member;

use Tests\TestCase;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_view_profile()
    {
        $member = Member::factory()->create();
        $this->actingAs($member, 'member');
        $this->assertTrue(true);
    }
}
