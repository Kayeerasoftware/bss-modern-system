<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function getSettings()
    {
        $settings = [
            'company_name' => Setting::get('company_name', 'BSS Investment Group'),
            'interest_rate' => Setting::get('interest_rate', 5),
            'minimum_savings' => Setting::get('minimum_savings', 1000),
            'loan_processing_fee' => Setting::get('loan_processing_fee', 2),
            'meeting_frequency' => Setting::get('meeting_frequency', 'monthly'),
            'currency' => Setting::get('currency', 'UGX'),
            'max_loan_amount' => Setting::get('max_loan_amount', 5000000),
            'notification_email' => Setting::get('notification_email', 'admin@bss.com'),
            'condolence_fund' => Setting::get('condolence_fund', 2000000)
        ];

        return response()->json($settings);
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            Setting::set($key, $value);
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
            Setting::set($key, $value);
        }

        return response()->json(['message' => 'Settings reset to defaults']);
    }
}
