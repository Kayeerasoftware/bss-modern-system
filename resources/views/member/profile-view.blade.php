@extends('layouts.member')

@section('content')
<div x-data="profileView()" class="space-y-6">
    <!-- Profile Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="relative">
                    <img src="{{ $member->profile_picture_url ?? asset('images/default-avatar.svg') }}" 
                         class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                    <span class="absolute bottom-2 right-2 w-6 h-6 bg-green-500 border-4 border-white rounded-full"></span>
                </div>
                <div class="text-center md:text-left text-white flex-1">
                    <h2 class="text-3xl font-bold mb-2">{{ $member->full_name ?? auth()->user()->name }}</h2>
                    <p class="text-blue-100 mb-1">Member ID: {{ $member->member_id ?? 'N/A' }}</p>
                    <p class="text-blue-100">{{ $member->email ?? auth()->user()->email }}</p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm">
                            <i class="fas fa-calendar mr-1"></i>Joined {{ $member->created_at->format('M Y') ?? 'N/A' }}
                        </span>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm">
                            <i class="fas fa-check-circle mr-1"></i>Verified
                        </span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('member.settings.index') }}" class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition">
                        <i class="fas fa-edit mr-2"></i>Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 p-4 rounded-lg">
                    <i class="fas fa-wallet text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Balance</p>
                    <p class="text-xl font-bold text-gray-800">UGX {{ number_format($member->balance ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-4">
                <div class="bg-green-100 p-4 rounded-lg">
                    <i class="fas fa-piggy-bank text-green-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Savings</p>
                    <p class="text-xl font-bold text-gray-800">UGX {{ number_format($member->savings_balance ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-4">
                <div class="bg-orange-100 p-4 rounded-lg">
                    <i class="fas fa-hand-holding-usd text-orange-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Loans</p>
                    <p class="text-xl font-bold text-gray-800">{{ $loansCount ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-4">
                <div class="bg-purple-100 p-4 rounded-lg">
                    <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Shares</p>
                    <p class="text-xl font-bold text-gray-800">{{ $sharesCount ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-blue-600"></i>Personal Information
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between py-3 border-b">
                    <span class="text-gray-600">Full Name</span>
                    <span class="font-medium text-gray-800">{{ $member->full_name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-3 border-b">
                    <span class="text-gray-600">Email</span>
                    <span class="font-medium text-gray-800">{{ $member->email ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-3 border-b">
                    <span class="text-gray-600">Phone</span>
                    <span class="font-medium text-gray-800">{{ $member->contact ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-3 border-b">
                    <span class="text-gray-600">Location</span>
                    <span class="font-medium text-gray-800">{{ $member->location ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600">Occupation</span>
                    <span class="font-medium text-gray-800">{{ $member->occupation ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Account Activity -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-bar text-green-600"></i>Account Activity
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between py-3 border-b">
                    <span class="text-gray-600">Total Deposits</span>
                    <span class="font-medium text-green-600">UGX {{ number_format($totalDeposits ?? 0) }}</span>
                </div>
                <div class="flex justify-between py-3 border-b">
                    <span class="text-gray-600">Total Withdrawals</span>
                    <span class="font-medium text-red-600">UGX {{ number_format($totalWithdrawals ?? 0) }}</span>
                </div>
                <div class="flex justify-between py-3 border-b">
                    <span class="text-gray-600">Active Loans</span>
                    <span class="font-medium text-gray-800">{{ $activeLoansCount ?? 0 }}</span>
                </div>
                <div class="flex justify-between py-3 border-b">
                    <span class="text-gray-600">Total Transactions</span>
                    <span class="font-medium text-gray-800">{{ $transactionsCount ?? 0 }}</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600">Last Activity</span>
                    <span class="font-medium text-gray-800">{{ $lastActivity ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function profileView() {
    return {
        init() {
            console.log('Profile view initialized');
        }
    }
}
</script>
@endsection

