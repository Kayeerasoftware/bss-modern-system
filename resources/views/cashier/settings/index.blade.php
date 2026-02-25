@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-zinc-50 p-3 md:p-6">
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
                <p class="text-gray-600 text-xs md:text-sm font-medium">View system preferences and parameters</p>
            </div>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-slate-500 to-gray-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-slate-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Settings...</span>
    </div>

    <div class="max-w-7xl mx-auto space-y-6">
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
                            System Name
                        </label>
                        <input type="text" value="{{ cache('system_name', 'BSS Investment Group') }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-envelope text-gray-600"></i>
                            System Email
                        </label>
                        <input type="email" value="{{ cache('system_email', 'info@bss.com') }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-phone text-zinc-600"></i>
                            System Phone
                        </label>
                        <input type="text" value="{{ cache('system_phone', '') }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-globe text-blue-600"></i>
                            Website
                        </label>
                        <input type="url" value="{{ cache('website', '') }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-map-marker-alt text-red-600"></i>
                            Address
                        </label>
                        <textarea rows="2" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">{{ cache('address', '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

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
                            Currency
                        </label>
                        <input type="text" value="{{ cache('currency', 'UGX') }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-percentage text-green-600"></i>
                            Default Interest Rate (%)
                        </label>
                        <input type="text" value="{{ cache('default_interest_rate', 10) }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-hand-holding-usd text-emerald-600"></i>
                            Withdrawal Fee (%)
                        </label>
                        <input type="text" value="{{ cache('withdrawal_fee', 2) }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-receipt text-orange-600"></i>
                            Transaction Fee (%)
                        </label>
                        <input type="text" value="{{ cache('transaction_fee', 1) }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-wallet text-blue-600"></i>
                            Minimum Balance
                        </label>
                        <input type="text" value="{{ number_format(cache('minimum_balance', 10000)) }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-piggy-bank text-pink-600"></i>
                            Minimum Savings
                        </label>
                        <input type="text" value="{{ number_format(cache('minimum_savings', 5000)) }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                </div>
            </div>
        </div>

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
                        <input type="text" value="{{ number_format(cache('max_loan_amount', 10000000)) }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-money-bill text-purple-600"></i>
                            Min Loan Amount
                        </label>
                        <input type="text" value="{{ number_format(cache('min_loan_amount', 50000)) }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-calendar-alt text-blue-600"></i>
                            Max Loan Period (months)
                        </label>
                        <input type="text" value="{{ cache('max_loan_period', 36) }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-user-check text-green-600"></i>
                            Require Guarantors
                        </label>
                        <input type="text" value="{{ cache('require_guarantors', 1) == 1 ? 'Yes' : 'No' }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-users text-teal-600"></i>
                            Number of Guarantors
                        </label>
                        <input type="text" value="{{ cache('number_of_guarantors', 2) }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-check-circle text-emerald-600"></i>
                            Auto Approve Loans
                        </label>
                        <input type="text" value="{{ cache('auto_approve_loans', 0) == 1 ? 'Yes' : 'No' }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                </div>
            </div>
        </div>

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
                        <input type="text" value="{{ cache('email_notifications', 1) == 1 ? 'Enabled' : 'Disabled' }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-sms text-orange-600"></i>
                            SMS Notifications
                        </label>
                        <input type="text" value="{{ cache('sms_notifications', 1) == 1 ? 'Enabled' : 'Disabled' }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                </div>
            </div>
        </div>

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
                        <input type="text" value="{{ cache('session_timeout', 30) }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-key text-pink-600"></i>
                            Password Min Length
                        </label>
                        <input type="text" value="{{ cache('password_min_length', 8) }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-user-shield text-purple-600"></i>
                            Two-Factor Auth
                        </label>
                        <input type="text" value="{{ cache('two_factor_auth', 0) == 1 ? 'Enabled' : 'Disabled' }}" readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('cashier.dashboard') }}" class="px-8 py-3 bg-gradient-to-r from-slate-600 via-gray-600 to-zinc-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
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
