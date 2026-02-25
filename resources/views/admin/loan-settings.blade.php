@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-yellow-50 via-orange-50 to-red-50 p-3 md:p-6">
    <!-- Animated Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
            <div class="flex items-center gap-4">
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-600 via-orange-600 to-red-600 rounded-3xl blur-2xl opacity-60 group-hover:opacity-80 transition-all duration-500 animate-pulse"></div>
                    <div class="relative bg-gradient-to-br from-yellow-500 via-orange-500 to-red-500 p-5 rounded-3xl shadow-2xl transform group-hover:scale-110 transition-all duration-300">
                        <i class="fas fa-cog text-white text-4xl animate-spin-slow"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-yellow-600 via-orange-600 to-red-600 bg-clip-text text-transparent mb-2 drop-shadow-lg">Loan Settings</h1>
                    <p class="text-gray-600 text-sm font-semibold flex items-center gap-2">
                        <i class="fas fa-sliders-h text-orange-500"></i>
                        Configure comprehensive loan parameters and policies
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.loan-applications.index') }}" class="px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-xl hover:shadow-2xl transition-all duration-300 font-bold flex items-center gap-2 transform hover:scale-105">
                <i class="fas fa-arrow-left"></i>
                Back to Applications
            </a>
        </div>
    </div>

    <!-- Animated Separator -->
    <div class="relative h-2 bg-gradient-to-r from-yellow-200 via-orange-200 to-red-200 rounded-full overflow-hidden mb-6 shadow-lg">
        <div class="h-full bg-gradient-to-r from-yellow-500 via-orange-500 to-red-500 rounded-full animate-slide-right"></div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-gradient-to-r from-green-500 to-emerald-500 border-l-4 border-green-700 text-white px-6 py-4 rounded-2xl shadow-2xl animate-fade-in">
        <div class="flex items-center">
            <div class="bg-white/20 p-3 rounded-xl mr-4">
                <i class="fas fa-check-circle text-3xl"></i>
            </div>
            <div>
                <p class="font-bold text-lg">Success!</p>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.loan-settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <!-- Loan Availability -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-yellow-100 hover:border-yellow-300 transition-all duration-300 transform hover:scale-105 hover:shadow-yellow-200">
                <div class="bg-gradient-to-br from-yellow-500 via-orange-500 to-red-500 p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                            <i class="fas fa-toggle-on text-white text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-white drop-shadow-lg">Loan Availability</h2>
                            <p class="text-yellow-100 text-sm font-medium">Enable/Disable loans</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-5 bg-gradient-to-br from-yellow-50 to-orange-50">
                    <div class="flex items-center justify-between p-6 bg-white rounded-2xl shadow-lg border-2 border-yellow-100 hover:border-yellow-300 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="bg-yellow-500 p-3 rounded-xl">
                                <i class="fas fa-hand-holding-usd text-white text-xl"></i>
                            </div>
                            <div>
                                <span class="text-lg font-black text-gray-800">Is Loan Available?</span>
                                <p class="text-xs text-gray-600 font-medium">Toggle loan system on/off</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_loan_available" value="1" {{ old('is_loan_available', $settings['is_loan_available'] ?? 1) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-16 h-8 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-7 after:w-7 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-yellow-500 peer-checked:to-orange-500 shadow-lg"></div>
                        </label>
                    </div>
                    
                    <div class="bg-gradient-to-r from-yellow-100 to-orange-100 border-l-4 border-yellow-500 p-4 rounded-xl">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-yellow-600 text-lg mt-1"></i>
                            <div>
                                <p class="text-sm font-bold text-gray-800 mb-1">Important Note</p>
                                <p class="text-xs text-gray-700">When disabled, members cannot apply for new loans. Existing loans remain active.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interest Rate Settings -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-blue-100 hover:border-blue-300 transition-all duration-300 transform hover:scale-105 hover:shadow-blue-200">
                <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                            <i class="fas fa-percentage text-white text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-white drop-shadow-lg">Interest Rates</h2>
                            <p class="text-blue-100 text-sm font-medium">Configure rate parameters</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-5 bg-gradient-to-br from-blue-50 to-indigo-50">
                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-blue-500 p-2 rounded-lg">
                                <i class="fas fa-percent text-white text-xs"></i>
                            </div>
                            Default Interest Rate (%)
                        </label>
                        <input type="number" name="default_interest_rate" value="{{ old('default_interest_rate', $settings['default_interest_rate'] ?? 10) }}" step="0.01" min="0" max="100" class="w-full px-5 py-4 border-2 border-blue-200 rounded-2xl focus:ring-4 focus:ring-blue-300 focus:border-blue-500 transition-all font-semibold text-lg shadow-lg" required>
                    </div>

                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-green-500 p-2 rounded-lg">
                                <i class="fas fa-arrow-down text-white text-xs"></i>
                            </div>
                            Minimum Interest Rate (%)
                        </label>
                        <input type="number" name="min_interest_rate" value="{{ old('min_interest_rate', $settings['min_interest_rate'] ?? 5) }}" step="0.01" min="0" max="100" class="w-full px-5 py-4 border-2 border-green-200 rounded-2xl focus:ring-4 focus:ring-green-300 focus:border-green-500 transition-all font-semibold text-lg shadow-lg" required>
                    </div>

                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-red-500 p-2 rounded-lg">
                                <i class="fas fa-arrow-up text-white text-xs"></i>
                            </div>
                            Maximum Interest Rate (%)
                        </label>
                        <input type="number" name="max_interest_rate" value="{{ old('max_interest_rate', $settings['max_interest_rate'] ?? 30) }}" step="0.01" min="0" max="100" class="w-full px-5 py-4 border-2 border-red-200 rounded-2xl focus:ring-4 focus:ring-red-300 focus:border-red-500 transition-all font-semibold text-lg shadow-lg" required>
                    </div>
                </div>
            </div>

            <!-- Loan Amount Limits -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-green-100 hover:border-green-300 transition-all duration-300 transform hover:scale-105 hover:shadow-green-200">
                <div class="bg-gradient-to-br from-green-500 via-emerald-600 to-teal-600 p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                            <i class="fas fa-money-bill-wave text-white text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-white drop-shadow-lg">Amount Limits</h2>
                            <p class="text-green-100 text-sm font-medium">Set borrowing boundaries</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-5 bg-gradient-to-br from-green-50 to-teal-50">
                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-green-500 p-2 rounded-lg">
                                <i class="fas fa-arrow-down text-white text-xs"></i>
                            </div>
                            Minimum Loan Amount (RWF)
                        </label>
                        <input type="number" name="min_loan_amount" value="{{ old('min_loan_amount', $settings['min_loan_amount'] ?? 10000) }}" step="1000" min="0" class="w-full px-5 py-4 border-2 border-green-200 rounded-2xl focus:ring-4 focus:ring-green-300 focus:border-green-500 transition-all font-semibold text-lg shadow-lg" required>
                    </div>

                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-red-500 p-2 rounded-lg">
                                <i class="fas fa-arrow-up text-white text-xs"></i>
                            </div>
                            Maximum Loan Amount (RWF)
                        </label>
                        <input type="number" name="max_loan_amount" value="{{ old('max_loan_amount', $settings['max_loan_amount'] ?? 10000000) }}" step="1000" min="0" class="w-full px-5 py-4 border-2 border-red-200 rounded-2xl focus:ring-4 focus:ring-red-300 focus:border-red-500 transition-all font-semibold text-lg shadow-lg" required>
                    </div>

                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-yellow-500 p-2 rounded-lg">
                                <i class="fas fa-chart-line text-white text-xs"></i>
                            </div>
                            Max Loan-to-Savings Ratio (%)
                        </label>
                        <input type="number" name="max_loan_to_savings_ratio" value="{{ old('max_loan_to_savings_ratio', $settings['max_loan_to_savings_ratio'] ?? 300) }}" step="10" min="0" class="w-full px-5 py-4 border-2 border-yellow-200 rounded-2xl focus:ring-4 focus:ring-yellow-300 focus:border-yellow-500 transition-all font-semibold text-lg shadow-lg" required>
                        <p class="text-xs text-gray-600 mt-2 font-medium italic">Maximum percentage of savings a member can borrow</p>
                    </div>
                </div>
            </div>

            <!-- Repayment Period Settings -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-purple-100 hover:border-purple-300 transition-all duration-300 transform hover:scale-105 hover:shadow-purple-200">
                <div class="bg-gradient-to-br from-purple-500 via-purple-600 to-pink-600 p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                            <i class="fas fa-calendar-alt text-white text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-white drop-shadow-lg">Repayment Period</h2>
                            <p class="text-purple-100 text-sm font-medium">Define time frames</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-5 bg-gradient-to-br from-purple-50 to-pink-50">
                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-green-500 p-2 rounded-lg">
                                <i class="fas fa-arrow-down text-white text-xs"></i>
                            </div>
                            Minimum Period (Months)
                        </label>
                        <input type="number" name="min_repayment_months" value="{{ old('min_repayment_months', $settings['min_repayment_months'] ?? 3) }}" min="1" max="120" class="w-full px-5 py-4 border-2 border-green-200 rounded-2xl focus:ring-4 focus:ring-green-300 focus:border-green-500 transition-all font-semibold text-lg shadow-lg" required>
                    </div>

                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-red-500 p-2 rounded-lg">
                                <i class="fas fa-arrow-up text-white text-xs"></i>
                            </div>
                            Maximum Period (Months)
                        </label>
                        <input type="number" name="max_repayment_months" value="{{ old('max_repayment_months', $settings['max_repayment_months'] ?? 60) }}" min="1" max="120" class="w-full px-5 py-4 border-2 border-red-200 rounded-2xl focus:ring-4 focus:ring-red-300 focus:border-red-500 transition-all font-semibold text-lg shadow-lg" required>
                    </div>

                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-blue-500 p-2 rounded-lg">
                                <i class="fas fa-calendar-check text-white text-xs"></i>
                            </div>
                            Default Period (Months)
                        </label>
                        <input type="number" name="default_repayment_months" value="{{ old('default_repayment_months', $settings['default_repayment_months'] ?? 12) }}" min="1" max="120" class="w-full px-5 py-4 border-2 border-blue-200 rounded-2xl focus:ring-4 focus:ring-blue-300 focus:border-blue-500 transition-all font-semibold text-lg shadow-lg" required>
                    </div>
                </div>
            </div>

            <!-- Processing & Fees -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-orange-100 hover:border-orange-300 transition-all duration-300 transform hover:scale-105 hover:shadow-orange-200">
                <div class="bg-gradient-to-br from-orange-500 via-orange-600 to-red-600 p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                            <i class="fas fa-file-invoice-dollar text-white text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-white drop-shadow-lg">Processing & Fees</h2>
                            <p class="text-orange-100 text-sm font-medium">Manage charges</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-5 bg-gradient-to-br from-orange-50 to-red-50">
                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-orange-500 p-2 rounded-lg">
                                <i class="fas fa-percentage text-white text-xs"></i>
                            </div>
                            Processing Fee (%)
                        </label>
                        <input type="number" name="processing_fee_percentage" value="{{ old('processing_fee_percentage', $settings['processing_fee_percentage'] ?? 2) }}" step="0.01" min="0" max="100" class="w-full px-5 py-4 border-2 border-orange-200 rounded-2xl focus:ring-4 focus:ring-orange-300 focus:border-orange-500 transition-all font-semibold text-lg shadow-lg" required>
                    </div>

                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-red-500 p-2 rounded-lg">
                                <i class="fas fa-exclamation-triangle text-white text-xs"></i>
                            </div>
                            Late Payment Penalty (%)
                        </label>
                        <input type="number" name="late_payment_penalty" value="{{ old('late_payment_penalty', $settings['late_payment_penalty'] ?? 5) }}" step="0.01" min="0" max="100" class="w-full px-5 py-4 border-2 border-red-200 rounded-2xl focus:ring-4 focus:ring-red-300 focus:border-red-500 transition-all font-semibold text-lg shadow-lg" required>
                    </div>

                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-yellow-500 p-2 rounded-lg">
                                <i class="fas fa-clock text-white text-xs"></i>
                            </div>
                            Grace Period (Days)
                        </label>
                        <input type="number" name="grace_period_days" value="{{ old('grace_period_days', $settings['grace_period_days'] ?? 7) }}" min="0" max="90" class="w-full px-5 py-4 border-2 border-yellow-200 rounded-2xl focus:ring-4 focus:ring-yellow-300 focus:border-yellow-500 transition-all font-semibold text-lg shadow-lg" required>
                        <p class="text-xs text-gray-600 mt-2 font-medium italic">Days before late payment penalty applies</p>
                    </div>
                </div>
            </div>

            <!-- Approval Settings -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-indigo-100 hover:border-indigo-300 transition-all duration-300 transform hover:scale-105 hover:shadow-indigo-200">
                <div class="bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                            <i class="fas fa-check-circle text-white text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-white drop-shadow-lg">Approval Settings</h2>
                            <p class="text-indigo-100 text-sm font-medium">Configure approvals</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-5 bg-gradient-to-br from-indigo-50 to-purple-50">
                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-indigo-500 p-2 rounded-lg">
                                <i class="fas fa-user-check text-white text-xs"></i>
                            </div>
                            Auto-Approve Below (RWF)
                        </label>
                        <input type="number" name="auto_approve_amount" value="{{ old('auto_approve_amount', $settings['auto_approve_amount'] ?? 0) }}" step="1000" min="0" class="w-full px-5 py-4 border-2 border-indigo-200 rounded-2xl focus:ring-4 focus:ring-indigo-300 focus:border-indigo-500 transition-all font-semibold text-lg shadow-lg">
                        <p class="text-xs text-gray-600 mt-2 font-medium italic">Set to 0 to disable auto-approval</p>
                    </div>

                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-purple-500 p-2 rounded-lg">
                                <i class="fas fa-users text-white text-xs"></i>
                            </div>
                            Require Guarantors
                        </label>
                        <select name="require_guarantors" class="w-full px-5 py-4 border-2 border-purple-200 rounded-2xl focus:ring-4 focus:ring-purple-300 focus:border-purple-500 transition-all font-semibold text-lg shadow-lg appearance-none bg-white">
                            <option value="0" {{ old('require_guarantors', $settings['require_guarantors'] ?? 0) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('require_guarantors', $settings['require_guarantors'] ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>

                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-blue-500 p-2 rounded-lg">
                                <i class="fas fa-hashtag text-white text-xs"></i>
                            </div>
                            Number of Guarantors
                        </label>
                        <input type="number" name="guarantors_required" value="{{ old('guarantors_required', $settings['guarantors_required'] ?? 2) }}" min="0" max="10" class="w-full px-5 py-4 border-2 border-blue-200 rounded-2xl focus:ring-4 focus:ring-blue-300 focus:border-blue-500 transition-all font-semibold text-lg shadow-lg">
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-pink-100 hover:border-pink-300 transition-all duration-300 transform hover:scale-105 hover:shadow-pink-200">
                <div class="bg-gradient-to-br from-pink-500 via-pink-600 to-rose-600 p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                            <i class="fas fa-bell text-white text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-white drop-shadow-lg">Notifications</h2>
                            <p class="text-pink-100 text-sm font-medium">Alert preferences</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-5 bg-gradient-to-br from-pink-50 to-rose-50">
                    <div class="flex items-center justify-between p-5 bg-white rounded-2xl shadow-lg border-2 border-pink-100 hover:border-pink-300 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="bg-pink-500 p-3 rounded-xl">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                            <span class="text-sm font-bold text-gray-800">Email Notifications</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notifications" value="1" {{ old('email_notifications', $settings['email_notifications'] ?? 1) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-pink-500 peer-checked:to-rose-500 shadow-lg"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-5 bg-white rounded-2xl shadow-lg border-2 border-pink-100 hover:border-pink-300 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="bg-pink-500 p-3 rounded-xl">
                                <i class="fas fa-sms text-white"></i>
                            </div>
                            <span class="text-sm font-bold text-gray-800">SMS Notifications</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sms_notifications" value="1" {{ old('sms_notifications', $settings['sms_notifications'] ?? 1) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-pink-500 peer-checked:to-rose-500 shadow-lg"></div>
                        </label>
                    </div>

                    <div class="relative">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-3">
                            <div class="bg-pink-500 p-2 rounded-lg">
                                <i class="fas fa-calendar-day text-white text-xs"></i>
                            </div>
                            Payment Reminder (Days)
                        </label>
                        <input type="number" name="payment_reminder_days" value="{{ old('payment_reminder_days', $settings['payment_reminder_days'] ?? 3) }}" min="0" max="30" class="w-full px-5 py-4 border-2 border-pink-200 rounded-2xl focus:ring-4 focus:ring-pink-300 focus:border-pink-500 transition-all font-semibold text-lg shadow-lg">
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="mt-8 flex justify-center">
            <button type="submit" class="relative group px-12 py-5 bg-gradient-to-r from-yellow-500 via-orange-500 to-red-500 text-white rounded-2xl hover:shadow-2xl transition-all duration-300 text-xl font-black flex items-center gap-4 transform hover:scale-110 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-yellow-600 via-orange-600 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <i class="fas fa-save text-2xl relative z-10"></i>
                <span class="relative z-10">Save All Settings</span>
                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
            </button>
        </div>
    </form>
</div>

<style>
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin-slow {
    animation: spin-slow 3s linear infinite;
}
</style>
@endsection
