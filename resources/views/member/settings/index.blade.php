@extends('layouts.member')

@section('title', 'Settings')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-gray-50 p-3 md:p-6" x-data="{ activeTab: 'security' }">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-3 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-blue-600 to-indigo-600 p-3 md:p-4 rounded-xl shadow-xl">
                    <i class="fas fa-cog text-white text-2xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-1">Settings</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">Manage your account preferences and security</p>
            </div>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full animate-slide-right"></div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-xl shadow-md">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-2xl mr-3"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <nav class="flex flex-wrap -mb-px">
                <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 md:px-6 py-3 md:py-4 border-b-2 font-semibold text-xs md:text-sm transition flex items-center gap-2">
                    <i class="fas fa-lock"></i><span class="hidden sm:inline">Security</span>
                </button>
                <button @click="activeTab = 'notifications'" :class="activeTab === 'notifications' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 md:px-6 py-3 md:py-4 border-b-2 font-semibold text-xs md:text-sm transition flex items-center gap-2">
                    <i class="fas fa-bell"></i><span class="hidden sm:inline">Notifications</span>
                </button>
                <button @click="activeTab = 'preferences'" :class="activeTab === 'preferences' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 md:px-6 py-3 md:py-4 border-b-2 font-semibold text-xs md:text-sm transition flex items-center gap-2">
                    <i class="fas fa-sliders-h"></i><span class="hidden sm:inline">Preferences</span>
                </button>
            </nav>
        </div>

        <div x-show="activeTab === 'security'" class="p-4 md:p-6">
            <form action="{{ route('member.password.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border-l-4 border-yellow-500 p-4 rounded-xl">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-1"></i>
                            <div>
                                <h4 class="font-bold text-gray-800">Password Security</h4>
                                <p class="text-sm text-gray-600">Use a strong password with at least 8 characters.</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-key text-gray-600"></i>Current Password
                        </label>
                        <input type="password" name="current_password" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-lock text-green-600"></i>New Password
                        </label>
                        <input type="password" name="password" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-check-circle text-blue-600"></i>Confirm New Password
                        </label>
                        <input type="password" name="password_confirmation" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    <div class="flex justify-end pt-4 border-t">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                            <i class="fas fa-shield-alt mr-2"></i>Update Password
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div x-show="activeTab === 'notifications'" class="p-4 md:p-6">
            <form action="{{ route('member.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-100">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-600 p-3 rounded-lg">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">Email Notifications</p>
                                <p class="text-sm text-gray-600">Receive notifications via email</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notifications" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border-2 border-green-100">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-600 p-3 rounded-lg">
                                <i class="fas fa-sms text-white"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">SMS Notifications</p>
                                <p class="text-sm text-gray-600">Receive notifications via SMS</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sms_notifications" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border-2 border-purple-100">
                        <div class="flex items-center gap-3">
                            <div class="bg-purple-600 p-3 rounded-lg">
                                <i class="fas fa-exchange-alt text-white"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">Transaction Alerts</p>
                                <p class="text-sm text-gray-600">Get notified of all transactions</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="transaction_alerts" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-red-50 rounded-xl border-2 border-orange-100">
                        <div class="flex items-center gap-3">
                            <div class="bg-orange-600 p-3 rounded-lg">
                                <i class="fas fa-hand-holding-usd text-white"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">Loan Updates</p>
                                <p class="text-sm text-gray-600">Notifications about loan status</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="loan_updates" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                        </label>
                    </div>
                    <div class="flex justify-end pt-4 border-t">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i>Save Preferences
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div x-show="activeTab === 'preferences'" class="p-4 md:p-6">
            <form action="{{ route('member.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-language text-blue-600"></i>Language
                        </label>
                        <select name="language" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none bg-white">
                            <option value="en">English</option>
                            <option value="sw">Swahili</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-dollar-sign text-green-600"></i>Currency
                        </label>
                        <select name="currency" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all appearance-none bg-white">
                            <option value="UGX">UGX - Ugandan Shilling</option>
                            <option value="USD">USD - US Dollar</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-palette text-purple-600"></i>Theme
                        </label>
                        <select name="theme" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all appearance-none bg-white">
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                            <option value="auto">Auto</option>
                        </select>
                    </div>
                    <div class="flex justify-end pt-4 border-t">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i>Save Preferences
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
</style>
@endsection
