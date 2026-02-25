@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-zinc-50 p-3 md:p-6">
    <!-- Header -->
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-slate-600 to-gray-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-slate-600 to-gray-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                    <i class="fas fa-cog text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-slate-600 via-gray-600 to-zinc-600 bg-clip-text text-transparent mb-1 md:mb-2">System Settings</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">Configure system preferences and parameters</p>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-slate-500 to-gray-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-slate-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Settings...</span>
    </div>

    <form action="{{ route('admin.system.settings.update') }}" method="POST" class="max-w-7xl mx-auto space-y-6">
        @csrf

        <!-- General Settings -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-slate-600 to-gray-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    General Settings
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-building text-slate-600"></i>
                            System Name *
                        </label>
                        <input type="text" name="system_name" value="{{ $settings['system_name'] ?? 'BSS Investment Group' }}" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-envelope text-gray-600"></i>
                            System Email *
                        </label>
                        <input type="email" name="system_email" value="{{ $settings['system_email'] ?? 'info@bss.com' }}" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-phone text-zinc-600"></i>
                            System Phone
                        </label>
                        <input type="text" name="system_phone" value="{{ $settings['system_phone'] ?? '' }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-zinc-500 focus:border-zinc-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-globe text-blue-600"></i>
                            Website
                        </label>
                        <input type="url" name="website" value="{{ $settings['website'] ?? '' }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-map-marker-alt text-red-600"></i>
                            Address
                        </label>
                        <textarea name="address" rows="2" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm">{{ $settings['address'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Settings -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-money-bill-wave"></i>
                    Financial Settings
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Currency *
                        </label>
                        <select name="currency" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all text-sm appearance-none bg-white">
                            <option value="UGX" {{ ($settings['currency'] ?? 'UGX') == 'UGX' ? 'selected' : '' }}>UGX - Ugandan Shilling</option>
                            <option value="USD" {{ ($settings['currency'] ?? 'UGX') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ ($settings['currency'] ?? 'UGX') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ ($settings['currency'] ?? 'UGX') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-percentage text-green-600"></i>
                            Default Interest Rate (%)
                        </label>
                        <input type="number" name="default_interest_rate" value="{{ $settings['default_interest_rate'] ?? 10 }}" step="0.01" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-hand-holding-usd text-emerald-600"></i>
                            Withdrawal Fee (%)
                        </label>
                        <input type="number" name="withdrawal_fee" value="{{ $settings['withdrawal_fee'] ?? 2 }}" step="0.01" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-receipt text-orange-600"></i>
                            Transaction Fee (%)
                        </label>
                        <input type="number" name="transaction_fee" value="{{ $settings['transaction_fee'] ?? 1 }}" step="0.01" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-wallet text-blue-600"></i>
                            Minimum Balance
                        </label>
                        <input type="number" name="minimum_balance" value="{{ $settings['minimum_balance'] ?? 10000 }}" step="0.01" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-piggy-bank text-pink-600"></i>
                            Minimum Savings
                        </label>
                        <input type="number" name="minimum_savings" value="{{ $settings['minimum_savings'] ?? 5000 }}" step="0.01" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Loan Settings -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-hand-holding-usd"></i>
                    Loan Settings
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-money-check text-indigo-600"></i>
                            Max Loan Amount
                        </label>
                        <input type="number" name="max_loan_amount" value="{{ $settings['max_loan_amount'] ?? 10000000 }}" step="0.01" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-money-bill text-purple-600"></i>
                            Min Loan Amount
                        </label>
                        <input type="number" name="min_loan_amount" value="{{ $settings['min_loan_amount'] ?? 50000 }}" step="0.01" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-calendar-alt text-blue-600"></i>
                            Max Loan Period (months)
                        </label>
                        <input type="number" name="max_loan_period" value="{{ $settings['max_loan_period'] ?? 36 }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-user-check text-green-600"></i>
                            Require Guarantors
                        </label>
                        <select name="require_guarantors" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm appearance-none bg-white">
                            <option value="1" {{ ($settings['require_guarantors'] ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ ($settings['require_guarantors'] ?? 1) == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-users text-teal-600"></i>
                            Number of Guarantors
                        </label>
                        <input type="number" name="number_of_guarantors" value="{{ $settings['number_of_guarantors'] ?? 2 }}" min="1" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-check-circle text-emerald-600"></i>
                            Auto Approve Loans
                        </label>
                        <select name="auto_approve_loans" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-sm appearance-none bg-white">
                            <option value="1" {{ ($settings['auto_approve_loans'] ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ ($settings['auto_approve_loans'] ?? 0) == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-bell"></i>
                    Notification Settings
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-envelope text-yellow-600"></i>
                            Email Notifications
                        </label>
                        <select name="email_notifications" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all text-sm appearance-none bg-white">
                            <option value="1" {{ ($settings['email_notifications'] ?? 1) == 1 ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ ($settings['email_notifications'] ?? 1) == 0 ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-sms text-orange-600"></i>
                            SMS Notifications
                        </label>
                        <select name="sms_notifications" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm appearance-none bg-white">
                            <option value="1" {{ ($settings['sms_notifications'] ?? 1) == 1 ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ ($settings['sms_notifications'] ?? 1) == 0 ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-shield-alt"></i>
                    Security Settings
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-lock text-red-600"></i>
                            Session Timeout (minutes)
                        </label>
                        <input type="number" name="session_timeout" value="{{ $settings['session_timeout'] ?? 30 }}" min="5" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-key text-pink-600"></i>
                            Password Min Length
                        </label>
                        <input type="number" name="password_min_length" value="{{ $settings['password_min_length'] ?? 8 }}" min="6" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-user-shield text-purple-600"></i>
                            Two-Factor Auth
                        </label>
                        <select name="two_factor_auth" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm appearance-none bg-white">
                            <option value="1" {{ ($settings['two_factor_auth'] ?? 0) == 1 ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ ($settings['two_factor_auth'] ?? 0) == 0 ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shareholder Settings -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-user-tie"></i>
                    Shareholder Settings
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-users text-purple-600"></i>
                            Allow Members Section Access
                        </label>
                        <select name="shareholder_members_access" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm appearance-none bg-white">
                            <option value="1" {{ ($settings['shareholder_members_access'] ?? 1) == 1 ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ ($settings['shareholder_members_access'] ?? 1) == 0 ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                            <i class="fas fa-eye-slash text-pink-600"></i>
                            Hide Specific Financial Information
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="hidden" name="shareholder_hide_savings" value="0">
                                <input type="checkbox" name="shareholder_hide_savings" value="1" {{ ($settings['shareholder_hide_savings'] ?? 0) == 1 ? 'checked' : '' }} class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="text-sm text-gray-700 group-hover:text-purple-600 transition-colors">Savings Amount</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="hidden" name="shareholder_hide_shares" value="0">
                                <input type="checkbox" name="shareholder_hide_shares" value="1" {{ ($settings['shareholder_hide_shares'] ?? 0) == 1 ? 'checked' : '' }} class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="text-sm text-gray-700 group-hover:text-purple-600 transition-colors">Share Count</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="hidden" name="shareholder_hide_transactions" value="0">
                                <input type="checkbox" name="shareholder_hide_transactions" value="1" {{ ($settings['shareholder_hide_transactions'] ?? 0) == 1 ? 'checked' : '' }} class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="text-sm text-gray-700 group-hover:text-purple-600 transition-colors">Transaction Count</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 p-4 bg-purple-50 rounded-xl border border-purple-200">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-purple-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Note:</strong> When "Hide Financial Information" is enabled, shareholders will see "HIDDEN" instead of actual financial values in the members section.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-3">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full {{ ($settings['shareholder_members_access'] ?? 1) == 1 ? 'bg-green-500' : 'bg-red-500' }}"></div>
                            <span class="text-xs font-semibold {{ ($settings['shareholder_members_access'] ?? 1) == 1 ? 'text-green-700' : 'text-red-700' }}">
                                Members Access: {{ ($settings['shareholder_members_access'] ?? 1) == 1 ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full {{ (($settings['shareholder_hide_savings'] ?? 0) == 1 || ($settings['shareholder_hide_shares'] ?? 0) == 1 || ($settings['shareholder_hide_transactions'] ?? 0) == 1) ? 'bg-red-500' : 'bg-green-500' }}"></div>
                            <span class="text-xs font-semibold {{ (($settings['shareholder_hide_savings'] ?? 0) == 1 || ($settings['shareholder_hide_shares'] ?? 0) == 1 || ($settings['shareholder_hide_transactions'] ?? 0) == 1) ? 'text-red-700' : 'text-green-700' }}">
                                Hidden Fields: {{ collect(['shareholder_hide_savings', 'shareholder_hide_shares', 'shareholder_hide_transactions'])->filter(fn($key) => ($settings[$key] ?? 0) == 1)->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Status Control -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-teal-600 to-cyan-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-toggle-on"></i>
                    Role Status Control (Login)
                </h3>
            </div>
            <div class="p-6">
                <div class="mb-4 p-4 bg-teal-50 rounded-xl border border-teal-200">
                    <p class="text-sm text-teal-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> When a role is active (checked), it will appear in green with "[Active role]" on the login page. When inactive (unchecked), it will appear in red with "[Inactive role]".
                    </p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach(['client' => 'Client/Member', 'shareholder' => 'Shareholder', 'cashier' => 'Cashier', 'td' => 'Technical Director', 'ceo' => 'CEO', 'admin' => 'Administrator'] as $role => $label)
                    <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-teal-300 transition-all">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="hidden" name="role_status_{{ $role }}" value="0">
                            <input type="checkbox" name="role_status_{{ $role }}" value="1" {{ ($settings['role_status_'.$role] ?? 1) == 1 ? 'checked' : '' }} class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                            <div class="flex-1">
                                <span class="text-sm font-bold text-gray-700 group-hover:text-teal-600 transition-colors block">{{ $label }}</span>
                                <span class="text-xs {{ ($settings['role_status_'.$role] ?? 1) == 1 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                    {{ ($settings['role_status_'.$role] ?? 1) == 1 ? '✓ Active' : '✗ Inactive' }}
                                </span>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Registration Restriction Control -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-user-lock"></i>
                    Registration Restriction Control
                </h3>
            </div>
            <div class="p-6">
                <div class="mb-4 p-4 bg-orange-50 rounded-xl border border-orange-200">
                    <p class="text-sm text-orange-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> Control which roles can register new accounts. Unchecked roles will be blocked from registration even if they are active for login.
                    </p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach(['client' => 'Client/Member', 'shareholder' => 'Shareholder', 'cashier' => 'Cashier', 'td' => 'Technical Director', 'ceo' => 'CEO', 'admin' => 'Administrator'] as $role => $label)
                    <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-orange-300 transition-all">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="hidden" name="allow_registration_{{ $role }}" value="0">
                            <input type="checkbox" name="allow_registration_{{ $role }}" value="1" {{ ($settings['allow_registration_'.$role] ?? 1) == 1 ? 'checked' : '' }} class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            <div class="flex-1">
                                <span class="text-sm font-bold text-gray-700 group-hover:text-orange-600 transition-colors block">{{ $label }}</span>
                                <span class="text-xs {{ ($settings['allow_registration_'.$role] ?? 1) == 1 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                    {{ ($settings['allow_registration_'.$role] ?? 1) == 1 ? '✓ Allowed' : '✗ Blocked' }}
                                </span>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- User Access Control -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-user-lock"></i>
                    User Access Control
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    @foreach(['admin' => 'Admin', 'cashier' => 'Cashier', 'td' => 'Technical Director', 'ceo' => 'CEO', 'shareholder' => 'Shareholder', 'client' => 'Client'] as $role => $label)
                    <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-blue-300 transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-user-tag text-blue-600"></i>
                                {{ $label }}
                            </h4>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach(['dashboard' => 'Dashboard', 'members' => 'Members', 'loans' => 'Loans', 'transactions' => 'Transactions', 'reports' => 'Reports', 'settings' => 'Settings', 'projects' => 'Projects', 'fundraising' => 'Fundraising'] as $perm => $permLabel)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" name="access[{{ $role }}][{{ $perm }}]" value="1" {{ ($settings['access'][$role][$perm] ?? in_array($role, ['admin']) || ($role === 'cashier' && in_array($perm, ['dashboard', 'members', 'transactions'])) || ($role === 'td' && in_array($perm, ['dashboard', 'reports'])) || ($role === 'ceo' && in_array($perm, ['dashboard', 'reports'])) || ($role === 'shareholder' && in_array($perm, ['dashboard', 'projects', 'fundraising'])) || ($role === 'client' && in_array($perm, ['dashboard', 'loans', 'transactions']))) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm text-gray-700 group-hover:text-blue-600 transition-colors">{{ $permLabel }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-end gap-4">
            <a href="{{ route('admin.dashboard') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-slate-600 via-gray-600 to-zinc-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>

<style>
@keyframes slide-right {
    0% { width: 0%; }
    100% { width: 100%; }
}
.animate-slide-right {
    animation: slide-right 5s ease-out forwards;
}
@keyframes slide-text {
    0% { left: 0%; opacity: 1; }
    95% { opacity: 1; }
    100% { left: 100%; opacity: 0; }
}
.animate-slide-text {
    animation: slide-text 5s ease-out forwards;
}
</style>
@endsection
