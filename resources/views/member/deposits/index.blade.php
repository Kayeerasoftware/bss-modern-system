@extends('layouts.member')

@section('title', 'My Deposits')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-green-50 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-green-600 to-emerald-600 p-3 md:p-4 rounded-xl shadow-xl">
                        <i class="fas fa-arrow-down text-white text-2xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-1">My Deposits</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Track and manage your deposit history</p>
                </div>
            </div>
            <a href="{{ route('member.deposits.create') }}" class="px-4 md:px-6 py-2 md:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-xl transition-all font-bold text-sm transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>New Deposit
            </a>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-green-500 to-emerald-600 rounded-full animate-slide-right"></div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4 mb-4 md:mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-3 md:p-4 text-white">
            <i class="fas fa-coins text-2xl mb-2 opacity-80"></i>
            <p class="text-white/80 text-xs font-medium">Total Deposits</p>
            <h3 class="text-xl md:text-3xl font-bold">UGX {{ number_format($deposits->sum('amount')) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-blue-500">
            <i class="fas fa-calendar-alt text-2xl text-blue-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">This Month</p>
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">UGX {{ number_format($deposits->where('created_at', '>=', now()->startOfMonth())->sum('amount')) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-purple-500">
            <i class="fas fa-chart-bar text-2xl text-purple-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Average Deposit</p>
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">UGX {{ $deposits->count() > 0 ? number_format($deposits->avg('amount')) : 0 }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-yellow-500">
            <i class="fas fa-list text-2xl text-yellow-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Total Count</p>
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">{{ $deposits->total() }}</h3>
        </div>
    </div>

    <!-- Deposits List -->
    <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 border border-gray-100">
        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-history text-green-600"></i>
            Deposit History
        </h3>

        <!-- Search and Filter -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-lg border border-green-100 mb-4">
            <form method="GET" action="{{ route('member.deposits.index') }}" class="p-3">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    <div class="md:col-span-5 relative">
                        <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-green-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search deposits..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white">
                    </div>
                    <div class="md:col-span-3 relative">
                        <select name="period" class="w-full px-2 py-1.5 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white" onchange="this.form.submit()">
                            <option value="">All Time</option>
                            <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                            <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>This Year</option>
                        </select>
                    </div>
                    <div class="md:col-span-4 flex gap-1.5">
                        <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-semibold">
                            <i class="fas fa-search mr-1"></i>Search
                        </button>
                        <a href="{{ route('member.deposits.index') }}" class="px-2 py-1.5 text-xs bg-gray-200 text-gray-700 rounded-lg">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Deposits Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-green-600 to-emerald-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-bold">Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-bold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($deposits as $deposit)
                    <tr class="hover:bg-green-50 transition">
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar text-green-600"></i>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $deposit->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $deposit->created_at->format('h:i A') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <i class="fas fa-arrow-down text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $deposit->description ?? 'Deposit' }}</p>
                                    <p class="text-xs text-gray-500">Transaction #{{ $deposit->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <p class="text-lg font-bold text-green-600">+UGX {{ number_format($deposit->amount) }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                {{ $deposit->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $deposit->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $deposit->status == 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($deposit->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center">
                            <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg font-medium mb-2">No deposits yet</p>
                            <p class="text-gray-400 text-sm mb-4">Start saving by making your first deposit</p>
                            <a href="{{ route('member.deposits.create') }}" class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                                <i class="fas fa-plus mr-2"></i>Make First Deposit
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deposits->hasPages())
        <div class="mt-4">{{ $deposits->links() }}</div>
        @endif
    </div>
</div>

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
</style>
@endsection
