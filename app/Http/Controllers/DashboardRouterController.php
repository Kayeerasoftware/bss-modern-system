<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRouterController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->get('role', 'client');
        
        // Route to appropriate dashboard based on role
        switch ($role) {
            case 'client':
                return view('client-dashboard');
            case 'shareholder':
                return view('shareholder-dashboard');
            case 'cashier':
                return view('cashier-dashboard');
            case 'td':
                return view('td-dashboard');
            case 'ceo':
                return view('ceo-dashboard');
            case 'admin':
                return view('admin-dashboard');
            default:
                return view('complete-dashboard');
        }
    }
    
    public function getRoleDashboard($role)
    {
        switch ($role) {
            case 'client':
                return view('client-dashboard');
            case 'shareholder':
                return view('shareholder-dashboard');
            case 'cashier':
                return view('cashier-dashboard');
            case 'td':
                return view('td-dashboard');
            case 'ceo':
                return view('ceo-dashboard');
            case 'admin':
                return view('admin-dashboard');
            default:
                return view('complete-dashboard');
        }
    }
}