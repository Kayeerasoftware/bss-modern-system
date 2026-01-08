<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function getSettings()
    {
        $settings = [
            'company_name' => Cache::get('company_name', 'BSS Investment Group'),
            'interest_rate' => Cache::get('interest_rate', 5),
            'minimum_savings' => Cache::get('minimum_savings', 1000),
            'loan_processing_fee' => Cache::get('loan_processing_fee', 2),
            'meeting_frequency' => Cache::get('meeting_frequency', 'monthly'),
            'currency' => Cache::get('currency', 'UGX'),
            'max_loan_amount' => Cache::get('max_loan_amount', 5000000),
            'notification_email' => Cache::get('notification_email', 'admin@bss.com'),
            'condolence_fund' => Cache::get('condolence_fund', 2000000)
        ];

        return response()->json($settings);
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            Cache::put($key, $value, now()->addYears(1));
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }

    public function resetSettings()
    {
        $defaultSettings = [
            'company_name' => 'BSS Investment Group',
            'interest_rate' => 5,
            'minimum_savings' => 1000,
            'loan_processing_fee' => 2,
            'meeting_frequency' => 'monthly',
            'currency' => 'UGX',
            'max_loan_amount' => 5000000,
            'notification_email' => 'admin@bss.com'
        ];

        foreach ($defaultSettings as $key => $value) {
            Cache::put($key, $value, now()->addYears(1));
        }

        return response()->json(['message' => 'Settings reset to defaults']);
    }
}