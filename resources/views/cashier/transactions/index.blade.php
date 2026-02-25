@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 p-2 md:p-4 rounded-xl shadow-xl">
                        <i class="fas fa-exchange-alt text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-1">All Transactions</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Complete transaction history</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('cashier.deposits.create') }}" class="px-4 py-2 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:shadow-xl transition text-sm font-bold">
                    <i class="fas fa-arrow-down mr-1"></i>Deposit
                </a>
                <a href="{{ route('cashier.withdrawals.create') }}" class="px-4 py-2 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg hover:shadow-xl transition text-sm font-bold">
                    <i class="fas fa-arrow-up mr-1"></i>Withdraw
                </a>
            </div>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4">
        <div class="h-full bg-gradient-to-r from-purple-500 to-pink-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl text-purple-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Transactions data...</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-purple-100 text-[10px] font-medium mb-0.5">Total Transactions</p>
            <h3 class="text-xl font-bold">{{ $transactions->total() }}</h3>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-green-100 text-[10px] font-medium mb-0.5">Total Deposits</p>
            <h3 class="text-xl font-bold">{{ number_format($transactions->where('type', 'deposit')->sum('amount')/1000000, 1) }}M</h3>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-red-100 text-[10px] font-medium mb-0.5">Total Withdrawals</p>
            <h3 class="text-xl font-bold">{{ number_format($transactions->where('type', 'withdrawal')->sum('amount')/1000000, 1) }}M</h3>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-blue-100 text-[10px] font-medium mb-0.5">Net Amount</p>
            <h3 class="text-xl font-bold">{{ number_format(($transactions->where('type', 'deposit')->sum('amount') - $transactions->where('type', 'withdrawal')->sum('amount'))/1000000, 1) }}M</h3>
        </div>
    </div>

    <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
        <form method="GET" action="{{ route('cashier.transactions.index') }}" x-data="{ showAdvanced: false }">
            <div class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search member..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="type" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Types</option>
                                <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Deposits</option>
                                <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawals</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Amount (High-Low)</option>
                                <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Amount (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-md transition flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('cashier.transactions.index') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>

                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <div class="flex gap-1">
                                <input type="number" name="amount_min" value="{{ request('amount_min') }}" placeholder="Min Amount" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white" @change="$el.form.submit()">
                                <input type="number" name="amount_max" value="{{ request('amount_max') }}" placeholder="Max" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white" @change="$el.form.submit()">
                            </div>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y-0">
                <thead class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600">
                    <tr class="border-b-2 border-white/20">
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Member</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Description</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($transactions as $transaction)
                    <tr class="transition-all duration-200 {{ $loop->iteration % 2 == 0 ? 'bg-purple-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 border-l-4 border-purple-500">
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200 text-sm">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <div class="flex items-center gap-3">
                                @if($transaction->member)
                                @if($transaction->member->profile_picture)
                                    <img src="{{ asset('storage/' . $transaction->member->profile_picture) }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-purple-500 ring-offset-2">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center ring-2 ring-purple-500 ring-offset-2">
                                        <span class="text-white font-bold">{{ substr($transaction->member->full_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transaction->member->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->member->member_id }}</p>
                                </div>
                                @else
                                <span class="text-sm text-gray-500">N/A</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-full {{ $transaction->type == 'deposit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-arrow-{{ $transaction->type == 'deposit' ? 'down' : 'up' }} mr-1"></i>
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200 text-sm font-bold {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 border-r border-gray-200">{{ $transaction->description ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('cashier.transactions.show', $transaction->id) }}" class="p-2 bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-200 transition inline-block">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-exchange-alt text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg font-medium">No transactions found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $transactions->links() }}</div>
    </div>
</div>

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
@keyframes slide-text { 0% { left: 0%; opacity: 1; } 95% { opacity: 1; } 100% { left: 100%; opacity: 0; } }
.animate-slide-text { animation: slide-text 5s ease-out forwards; }
tr { position: relative; }
tr::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to right, transparent, #a855f7, #ec4899, transparent);
    opacity: 0.3;
}
tr:hover::after { opacity: 0.6; }
</style>
@endsection
