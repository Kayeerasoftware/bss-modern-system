@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6" x-data="{ activeTab: 'all' }">
    <!-- Header Section -->
    <div class="mb-4 md:mb-6">
        <div class="flex items-center justify-between gap-2 md:gap-4">
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-600 to-blue-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-cyan-600 to-blue-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                        <i class="fas fa-exchange-alt text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 bg-clip-text text-transparent mb-1 md:mb-2">Financial Transactions</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">View all financial transactions</p>
                </div>
            </div>
            <a href="{{ route('cashier.financial.create') }}" class="px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-lg hover:shadow-xl transition-all text-sm font-bold flex items-center gap-2 transform hover:scale-105">
                <i class="fas fa-plus"></i><span class="hidden sm:inline">New Transaction</span>
            </a>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-cyan-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Transactions data...</span>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-3 md:mb-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Deposits</p>
                    <h3 class="text-base md:text-xl font-bold">{{ number_format($transactions->where('type', 'deposit')->sum('amount'), 0) }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-arrow-down text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Withdrawals</p>
                    <h3 class="text-base md:text-xl font-bold">{{ number_format($transactions->where('type', 'withdrawal')->sum('amount'), 0) }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-arrow-up text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Transfers</p>
                    <h3 class="text-base md:text-xl font-bold">{{ number_format($transactions->where('type', 'transfer')->sum('amount'), 0) }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-exchange-alt text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Transactions</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $transactions->total() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-list text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-gradient-to-br from-cyan-50 via-blue-50 to-indigo-50 rounded-2xl shadow-lg border border-cyan-100 overflow-hidden mb-4">
        <form method="GET" action="{{ route('cashier.financial.index') }}">
            <div class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-cyan-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-3 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-cyan-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transactions..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-cyan-200 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-blue-400 text-xs"></i>
                            <select name="type" class="w-full pl-8 pr-2 py-1.5 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none bg-white">
                                <option value="">All Types</option>
                                <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-calendar absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-calendar absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white">
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Filter
                            </button>
                            <a href="{{ route('cashier.financial.index') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabs -->
    <div class="flex gap-2 mb-4 overflow-x-auto">
        <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-gradient-to-r from-cyan-600 to-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200'" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap">
            <i class="fas fa-list mr-1"></i>All Transactions
        </button>
        <button @click="activeTab = 'deposits'" :class="activeTab === 'deposits' ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white' : 'bg-white text-gray-700 border border-gray-200'" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap">
            <i class="fas fa-arrow-down mr-1"></i>Deposits
        </button>
        <button @click="activeTab = 'withdrawals'" :class="activeTab === 'withdrawals' ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white' : 'bg-white text-gray-700 border border-gray-200'" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap">
            <i class="fas fa-arrow-up mr-1"></i>Withdrawals
        </button>
        <button @click="activeTab = 'transfers'" :class="activeTab === 'transfers' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-200'" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap">
            <i class="fas fa-exchange-alt mr-1"></i>Transfers
        </button>
    </div>

    <!-- All Transactions Tab -->
    <div x-show="activeTab === 'all'" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-cyan-50 via-blue-50 to-indigo-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gradient-to-r hover:from-cyan-50 hover:to-blue-50 transition-all duration-200">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-cyan-600">{{ $transaction->transaction_id ?? '#' . $transaction->id }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <i class="fas fa-calendar text-blue-500 mr-1"></i>{{ $transaction->created_at->format('M d, Y') }}
                            <p class="text-xs text-gray-400">{{ $transaction->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($transaction->member && $transaction->member->profile_picture)
                                    <img src="{{ asset('storage/' . $transaction->member->profile_picture) }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-cyan-500 ring-offset-2" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center ring-2 ring-cyan-500 ring-offset-2">
                                        <span class="text-white font-bold text-sm">{{ substr($transaction->member->full_name ?? 'N', 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transaction->member->full_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->member->member_id ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($transaction->type == 'deposit')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-arrow-down text-[8px] mr-1"></i>Deposit
                                </span>
                            @elseif($transaction->type == 'withdrawal')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200">
                                    <i class="fas fa-arrow-up text-[8px] mr-1"></i>Withdrawal
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                    <i class="fas fa-exchange-alt text-[8px] mr-1"></i>{{ ucfirst($transaction->type) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-lg {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                UGX {{ number_format($transaction->amount, 0) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-600">{{ Str::limit($transaction->description ?? 'N/A', 50) }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gradient-to-br from-cyan-100 to-blue-100 p-6 rounded-full mb-4">
                                    <i class="fas fa-exchange-alt text-cyan-400 text-5xl"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-semibold">No transactions found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Deposits Tab -->
    <div x-show="activeTab === 'deposits'" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions->where('type', 'deposit') as $transaction)
                    <tr class="hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all duration-200">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-green-600">{{ $transaction->transaction_id ?? '#' . $transaction->id }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <i class="fas fa-calendar text-green-500 mr-1"></i>{{ $transaction->created_at->format('M d, Y') }}
                            <p class="text-xs text-gray-400">{{ $transaction->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($transaction->member && $transaction->member->profile_picture)
                                    <img src="{{ asset('storage/' . $transaction->member->profile_picture) }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-green-500 ring-offset-2" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center ring-2 ring-green-500 ring-offset-2">
                                        <span class="text-white font-bold text-sm">{{ substr($transaction->member->full_name ?? 'N', 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transaction->member->full_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->member->member_id ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-lg text-green-600">UGX {{ number_format($transaction->amount, 0) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-600">{{ Str::limit($transaction->description ?? 'N/A', 50) }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gradient-to-br from-green-100 to-emerald-100 p-6 rounded-full mb-4">
                                    <i class="fas fa-arrow-down text-green-400 text-5xl"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-semibold">No deposits found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Withdrawals Tab -->
    <div x-show="activeTab === 'withdrawals'" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-red-50 via-rose-50 to-pink-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions->where('type', 'withdrawal') as $transaction)
                    <tr class="hover:bg-gradient-to-r hover:from-red-50 hover:to-rose-50 transition-all duration-200">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-red-600">{{ $transaction->transaction_id ?? '#' . $transaction->id }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <i class="fas fa-calendar text-red-500 mr-1"></i>{{ $transaction->created_at->format('M d, Y') }}
                            <p class="text-xs text-gray-400">{{ $transaction->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($transaction->member && $transaction->member->profile_picture)
                                    <img src="{{ asset('storage/' . $transaction->member->profile_picture) }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-red-500 ring-offset-2" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center ring-2 ring-red-500 ring-offset-2">
                                        <span class="text-white font-bold text-sm">{{ substr($transaction->member->full_name ?? 'N', 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transaction->member->full_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->member->member_id ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-lg text-red-600">UGX {{ number_format($transaction->amount, 0) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-600">{{ Str::limit($transaction->description ?? 'N/A', 50) }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gradient-to-br from-red-100 to-rose-100 p-6 rounded-full mb-4">
                                    <i class="fas fa-arrow-up text-red-400 text-5xl"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-semibold">No withdrawals found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transfers Tab -->
    <div x-show="activeTab === 'transfers'" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions->where('type', 'transfer') as $transaction)
                    <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-blue-600">{{ $transaction->transaction_id ?? '#' . $transaction->id }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <i class="fas fa-calendar text-blue-500 mr-1"></i>{{ $transaction->created_at->format('M d, Y') }}
                            <p class="text-xs text-gray-400">{{ $transaction->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($transaction->member && $transaction->member->profile_picture)
                                    <img src="{{ asset('storage/' . $transaction->member->profile_picture) }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-blue-500 ring-offset-2" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center ring-2 ring-blue-500 ring-offset-2">
                                        <span class="text-white font-bold text-sm">{{ substr($transaction->member->full_name ?? 'N', 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transaction->member->full_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->member->member_id ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-lg text-blue-600">UGX {{ number_format($transaction->amount, 0) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-600">{{ Str::limit($transaction->description ?? 'N/A', 50) }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gradient-to-br from-blue-100 to-indigo-100 p-6 rounded-full mb-4">
                                    <i class="fas fa-exchange-alt text-blue-400 text-5xl"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-semibold">No transfers found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $transactions->links() }}
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
