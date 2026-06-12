@extends('layouts.member')

@section('title', 'My Savings')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-green-50 p-3 md:p-6">
    <!-- Header -->
    <div class="mb-4 md:mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-green-600 to-emerald-600 p-3 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                        <i class="fas fa-piggy-bank text-white text-2xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-green-600 bg-clip-text text-transparent mb-1 md:mb-2">My Savings</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Track and manage your savings</p>
                </div>
            </div>
            <a href="{{ route('member.deposits.create') }}" class="px-4 md:px-6 py-2 md:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-xl transition-all font-bold text-sm md:text-base transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>Add Savings
            </a>
        </div>
    </div>

    <!-- Animated Separator -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-green-500 to-emerald-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-green-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading savings data...</span>
    </div>

    <!-- Savings Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 mb-4 md:mb-6">
        <!-- Net Savings Balance -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-4 md:p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-piggy-bank text-3xl md:text-4xl opacity-80"></i>
                <span class="text-xs md:text-sm bg-white/20 px-3 py-1 rounded-full font-semibold">Deposits - Withdrawals</span>
            </div>
            <p class="text-white/80 text-sm md:text-base font-medium mb-1">Net Savings Balance</p>
            <h3 class="text-2xl md:text-4xl font-bold mb-2">UGX {{ number_format($financialSummary['net_savings'] ?? 0) }}</h3>
            <p class="text-white/70 text-xs md:text-sm">
                <i class="fas fa-chart-line mr-1"></i>
                Available: UGX {{ number_format($financialSummary['available_balance'] ?? 0) }}
            </p>
        </div>

        <!-- This Month -->
        <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-calendar-alt text-3xl md:text-4xl text-blue-500"></i>
                <span class="text-xs md:text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-semibold">This Month</span>
            </div>
            <p class="text-gray-600 text-sm md:text-base font-medium mb-1">Monthly Deposits</p>
            <h3 class="text-2xl md:text-4xl font-bold text-gray-900 mb-2">
                UGX {{ number_format($summary['monthly_deposits'] ?? 0) }}
            </h3>
            <p class="text-gray-500 text-xs md:text-sm">
                {{ number_format($summary['monthly_count'] ?? 0) }} completed transactions
            </p>
        </div>

        <!-- Average -->
        <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-chart-bar text-3xl md:text-4xl text-purple-500"></i>
                <span class="text-xs md:text-sm bg-purple-100 text-purple-800 px-3 py-1 rounded-full font-semibold">Average</span>
            </div>
            <p class="text-gray-600 text-sm md:text-base font-medium mb-1">Avg. Deposit</p>
            <h3 class="text-2xl md:text-4xl font-bold text-gray-900 mb-2">
                UGX {{ number_format($summary['average_deposit'] ?? 0) }}
            </h3>
            <p class="text-gray-500 text-xs md:text-sm">
                Completed: {{ number_format($summary['completed_count'] ?? 0) }} | Pending: {{ number_format($summary['pending_count'] ?? 0) }}
            </p>
        </div>
    </div>

    <!-- Savings History -->
    <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-4 md:mb-6">
            <h3 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-green-600"></i>
                Savings History
            </h3>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 text-xs md:text-sm bg-green-100 text-green-800 rounded-lg font-semibold hover:bg-green-200 transition">
                    <i class="fas fa-download mr-1"></i>Export
                </button>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-gradient-to-br from-green-50 via-emerald-50 to-green-50 rounded-xl shadow-lg border border-green-100 overflow-hidden mb-4">
            <form method="GET" action="{{ route('member.savings') }}">
                <div class="bg-white/60 backdrop-blur-sm p-3">
                    <div class="bg-white/80 rounded-xl p-2.5 border border-green-100">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                            <div class="md:col-span-5 relative">
                                <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-green-400 text-xs"></i>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transactions..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all bg-white">
                            </div>
                            <div class="md:col-span-3 relative">
                                <i class="fas fa-calendar absolute left-2.5 top-1/2 transform -translate-y-1/2 text-emerald-400 text-xs"></i>
                                <select name="period" class="w-full pl-8 pr-2 py-1.5 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all appearance-none bg-white" onchange="this.form.submit()">
                                    <option value="">All Time</option>
                                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This Week</option>
                                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                                    <option value="3months" {{ request('period') == '3months' ? 'selected' : '' }}>Last 3 Months</option>
                                    <option value="6months" {{ request('period') == '6months' ? 'selected' : '' }}>Last 6 Months</option>
                                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>This Year</option>
                                </select>
                            </div>
                            <div class="md:col-span-4 flex gap-1.5">
                                <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                    <i class="fas fa-search mr-1"></i>Search
                                </button>
                                <a href="{{ route('member.savings') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-green-600 to-emerald-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs md:text-sm font-bold">Date</th>
                        <th class="px-4 py-3 text-left text-xs md:text-sm font-bold">Description</th>
                        <th class="px-4 py-3 text-right text-xs md:text-sm font-bold">Amount</th>
                        <th class="px-4 py-3 text-center text-xs md:text-sm font-bold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($savingsHistory as $transaction)
                    <tr class="hover:bg-green-50 transition">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar text-green-600"></i>
                                {{ $transaction->created_at->format('M d, Y') }}
                            </div>
                            <p class="text-xs text-gray-500">{{ $transaction->created_at->format('h:i A') }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <div class="flex items-center gap-2">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <i class="fas fa-arrow-down text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $transaction->description ?? 'Savings Deposit' }}</p>
                                    <p class="text-xs text-gray-500">Transaction #{{ $transaction->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <p class="text-lg font-bold text-green-600">+UGX {{ number_format($transaction->amount) }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center">
                            <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg font-medium mb-2">No savings history yet</p>
                            <p class="text-gray-400 text-sm mb-4">Start saving today to see your history here</p>
                            <a href="{{ route('member.deposits.create') }}" class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                                <i class="fas fa-plus mr-2"></i>Make First Deposit
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($savingsHistory->hasPages())
        <div class="mt-4">
            {{ $savingsHistory->links() }}
        </div>
        @endif
    </div>

    <!-- Savings Tips -->
    <div class="mt-4 md:mt-6 grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border-l-4 border-blue-500">
            <i class="fas fa-lightbulb text-2xl text-blue-600 mb-2"></i>
            <h4 class="font-bold text-gray-900 mb-1">Save Regularly</h4>
            <p class="text-sm text-gray-600">Set up automatic deposits to build your savings consistently</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border-l-4 border-purple-500">
            <i class="fas fa-target text-2xl text-purple-600 mb-2"></i>
            <h4 class="font-bold text-gray-900 mb-1">Set Goals</h4>
            <p class="text-sm text-gray-600">Define savings goals to stay motivated and track progress</p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border-l-4 border-green-500">
            <i class="fas fa-chart-line text-2xl text-green-600 mb-2"></i>
            <h4 class="font-bold text-gray-900 mb-1">Watch It Grow</h4>
            <p class="text-sm text-gray-600">Monitor your savings growth and celebrate milestones</p>
        </div>
    </div>
</div>

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
@keyframes slide-text { 0% { left: 0%; opacity: 1; } 95% { opacity: 1; } 100% { left: 100%; opacity: 0; } }
.animate-slide-text { animation: slide-text 5s ease-out forwards; }
</style>
@endsection
