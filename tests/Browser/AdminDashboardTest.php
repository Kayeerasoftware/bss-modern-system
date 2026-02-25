<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class AdminDashboardTest extends DuskTestCase
{
    public function test_admin_dashboard_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/dashboard')
                    ->assertSee('Dashboard');
        });
    }
}
