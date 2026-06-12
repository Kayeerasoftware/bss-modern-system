<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CEOController extends Controller
{
    /**
     * Display the CEO dashboard
     */
    public function index()
    {
        return view('ceo-dashboard');
    }

    /**
     * Test the CEO dashboard functionality
     */
    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => 'CEO Dashboard API is working correctly',
            'timestamp' => now()->toDateTimeString(),
            'features' => [
                'Executive Overview',
                'Strategic Management',
                'KPIs',
                'Market Analysis',
                'Financial Management',
                'Member Management',
                'Loan Management',
                'Reports & Analytics',
                'System Settings'
            ]
        ]);
    }
}
