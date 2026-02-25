@extends('layouts.member')

@section('title', 'Transaction History')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-blue-50 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-blue-600 to-purple-600 p-3 md:p-4 rounded-xl shadow-xl">
                        <i class="fas fa-exchange-alt text-white text-2xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-1">Transaction History</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">View all your financial transactions</p>
                </div>
            </div>
            <a href="{{ route('member.transactions.statement') }}" class="px-4 md:px-6 py-2 md:py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-xl transition-all font-bold text-sm transform hover:scale-105">
                <i class="fas fa-file-alt mr-2"></i>Statement
            </a>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-blue-500 to-purple-600 rounded-full animate-slide-right"></div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4 mb-4 md:mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-3 md:p-4 text-white">
            <i class="fas fa-list text-2xl mb-2 opacity-80"></i>
            <p class="text-white/80 text-xs font-medium">Total Transactions</p>
            <h3 class="text-xl md:text-3xl font-bold">{{ $transactions->total() }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-green-500">
            <i class="fas fa-arrow-down text-2xl text-green-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Total Deposits</p>
            <h3 class="text-xl md:text-3xl font-bold text-green-600">UGX {{ number_format($transactions->where('type', 'deposit')->sum('amount')) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-red-500">
            <i class="fas fa-arrow-up text-2xl text-red-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Total Withdrawals</p>
            <h3 class="text-xl md:text-3xl font-bold text-red-600">UGX {{ number_format($transactions->where('type', 'withdrawal')->sum('amount')) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-purple-500">
            <i class="fas fa-balance-scale text-2xl text-purple-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Net Flow</p>
            <h3 class="text-xl md:text-3xl font-bold text-purple-600">
                UGX {{ number_format($transactions->where('type', 'deposit')->sum('amount') - $transactions->where('type', 'withdrawal')->sum('amount')) }}
            </h3>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 border border-gray-100">
        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-receipt text-blue-600"></i>
            All Transactions
        </h3>

        <!-- Search and Filter -->
        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl shadow-lg border border-blue-100 mb-4">
            <form method="GET" action="{{ route('member.transactions.history') }}" class="p-3">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    <div class="md:col-span-4 relative">
                        <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-blue-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transactions..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                    <div class="md:col-span-2 relative">
                        <select name="type" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Deposits</option>
                            <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawals</option>
                            <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfers</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 relative">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-2 py-1.5 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                    <div class="md:col-span-2 relative">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-2 py-1.5 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                    <div class="md:col-span-2 flex gap-1.5">
                        <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold">
                            <i class="fas fa-search mr-1"></i>Search
                        </button>
                        <a href="{{ route('member.transactions.history') }}" class="px-2 py-1.5 text-xs bg-gray-200 text-gray-700 rounded-lg">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold">Date & Time</th>
                        <th class="px-4 py-3 text-left text-xs font-bold">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-bold">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-bold">Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-bold">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar text-blue-600"></i>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $transaction->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->created_at->format('h:i A') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="p-2 rounded-lg {{ $transaction->type == 'deposit' ? 'bg-green-100' : 'bg-red-100' }}">
                                    <i class="fas fa-{{ $transaction->type == 'deposit' ? 'arrow-down' : 'arrow-up' }} {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}"></i>
                                </div>
                                <span class="text-sm font-semibold">{{ ucfirst($transaction->type) }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ $transaction->description ?? 'Transaction' }}</p>
                            <p class="text-xs text-gray-500">ID: #{{ $transaction->id }}</p>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <p class="text-lg font-bold {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type == 'deposit' ? '+' : '-' }}UGX {{ number_format($transaction->amount) }}
                            </p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $transaction->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $transaction->status == 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('member.transactions.show', $transaction->id) }}" class="px-3 py-1.5 bg-blue-100 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-200">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg font-medium">No transactions found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="mt-4">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
</style>
@endsection
