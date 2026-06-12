<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Throwable;

class DashboardStatsService
{
    /**
     * Returns the v_dashboard_stats view as an associative array.
     * Falls back to an empty array if the view is unavailable.
     */
    public function get(): array
    {
        try {
            $row = DB::table('v_dashboard_stats')->first();
            if (!$row) {
                return [];
            }

            return (array) $row;
        } catch (Throwable) {
            return [];
        }
    }
}
