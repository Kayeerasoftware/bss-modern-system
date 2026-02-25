@extends('layouts.member')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 md:gap-4">
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 p-2 md:p-4 rounded-xl shadow-xl">
                        <i class="fas fa-exchange-alt text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-1">Financial Transactions</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">View and manage all financial transactions</p>
                </div>
            </div>
            <div class="flex gap-1.5 md:gap-2 w-full md:w-auto">
                <a href="{{ route('shareholder.transactions.create') }}" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-plus"></i><span class="hidden sm:inline">New Transaction</span><span class="sm:hidden">New</span>
                </a>
                <button class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-white text-gray-700 rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 shadow-md border border-gray-200 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-file-export"></i><span class="hidden sm:inline">Export</span>
                </button>
            </div>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-6">
        <div class="h-full bg-gradient-to-r from-purple-500 to-pink-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl text-purple-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Transactions data...</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-green-100 text-[10px] font-medium mb-0.5">Total Deposits</p>
            <h3 class="text-xl font-bold">{{ number_format($transactions->where('type', 'deposit')->sum('amount'), 0) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-red-100 text-[10px] font-medium mb-0.5">Total Withdrawals</p>
            <h3 class="text-xl font-bold">{{ number_format($transactions->where('type', 'withdrawal')->sum('amount'), 0) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-blue-100 text-[10px] font-medium mb-0.5">Total Transfers</p>
            <h3 class="text-xl font-bold">{{ number_format($transactions->where('type', 'transfer')->sum('amount'), 0) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-purple-100 text-[10px] font-medium mb-0.5">Total Transactions</p>
            <h3 class="text-xl font-bold">{{ count($\1) }}</h3>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-6">
        <form method="GET" action="{{ route('shareholder.transactions') }}">
            <div class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 md:p-3 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transactions..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="type" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white">
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
                        <div class="md:col-span-2 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('shareholder.transactions') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
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
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20 w-24">ID</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20 w-32">Date</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20 w-48">Member</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20 w-28">Type</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20 w-32">Category</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20 w-32">Amount</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20 w-32">Charges</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20 w-32">Net Amount</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20 w-28">Channel</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20 w-24">Priority</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20 w-28">Status</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider w-20">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($transactions as $transaction)
                    <tr class="transition-all duration-200 {{ $loop->iteration % 2 == 0 ? 'bg-purple-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 border-l-4 border-purple-500">
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <span class="font-bold text-purple-600">{{ $transaction->transaction_id ?? '#' . $transaction->id }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <p class="text-sm text-gray-900">{{ ($transaction->transaction_date ?? $transaction->created_at)->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ ($transaction->transaction_date ?? $transaction->created_at)->format('H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200">
                            <div class="flex items-center gap-3">
                                @if($transaction->member && $transaction->member->profile_picture)
                                    <img src="{{ asset('storage/' . $transaction->member->profile_picture) }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-purple-500 ring-offset-2" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center ring-2 ring-purple-500 ring-offset-2">
                                        <span class="text-white font-bold text-sm">{{ substr($transaction->member->full_name ?? 'N', 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transaction->member->full_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->member->member_id ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            @if($transaction->type == 'deposit')
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full border bg-green-100 text-green-800 border-green-200">
                                    <i class="fas fa-arrow-down text-[8px] mr-1"></i>Deposit
                                </span>
                            @elseif($transaction->type == 'withdrawal')
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full border bg-red-100 text-red-800 border-red-200">
                                    <i class="fas fa-arrow-up text-[8px] mr-1"></i>Withdrawal
                                </span>
                            @else
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full border bg-blue-100 text-blue-800 border-blue-200">
                                    <i class="fas fa-exchange-alt text-[8px] mr-1"></i>Transfer
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-purple-50 text-purple-700 border border-purple-200">
                                {{ ucfirst(str_replace('_', ' ', $transaction->category ?? 'N/A')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <span class="font-bold text-lg {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                UGX {{ number_format($transaction->amount, 0) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            @php
                                $totalCharges = ($transaction->fee ?? 0) + ($transaction->tax_amount ?? 0) + ($transaction->commission ?? 0);
                            @endphp
                            @if($totalCharges > 0)
                                <span class="text-sm font-semibold text-orange-600">
                                    UGX {{ number_format($totalCharges, 0) }}
                                </span>
                                <p class="text-xs text-gray-500">
                                    @if($transaction->fee > 0)Fee: {{ number_format($transaction->fee, 0) }}@endif
                                    @if($transaction->tax_amount > 0) | Tax: {{ number_format($transaction->tax_amount, 0) }}@endif
                                    @if($transaction->commission > 0) | Com: {{ number_format($transaction->commission, 0) }}@endif
                                </p>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <span class="font-bold text-base text-indigo-600">
                                UGX {{ number_format($transaction->net_amount ?? $transaction->amount, 0) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            @if($transaction->channel)
                                <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-teal-50 text-teal-700 border border-teal-200">
                                    <i class="fas fa-{{ $transaction->channel == 'branch' ? 'building' : ($transaction->channel == 'online' ? 'globe' : ($transaction->channel == 'mobile_app' ? 'mobile-alt' : 'broadcast-tower')) }} text-[8px] mr-1"></i>
                                    {{ ucfirst($transaction->channel) }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            @php
                                $priorityColors = [
                                    'low' => 'bg-gray-50 text-gray-600 border-gray-200',
                                    'normal' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'high' => 'bg-orange-50 text-orange-700 border-orange-200',
                                    'urgent' => 'bg-red-50 text-red-700 border-red-200',
                                ];
                                $priority = $transaction->priority ?? 'normal';
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-lg border {{ $priorityColors[$priority] }}">
                                {{ ucfirst($priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            @if($transaction->status == 'completed')
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-circle text-[8px] mr-1"></i>Completed
                                </span>
                            @elseif($transaction->status == 'pending')
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-circle text-[8px] mr-1"></i>Pending
                                </span>
                            @elseif($transaction->status == 'reversed')
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-circle text-[8px] mr-1"></i>Reversed
                                </span>
                            @else
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-circle text-[8px] mr-1"></i>Failed
                                </span>
                            @endif
                            @if($transaction->reconciled)
                                <p class="text-xs text-green-600 mt-1"><i class="fas fa-check-double text-[8px]"></i> Reconciled</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('shareholder.transactions.show', $transaction->id) }}" class="p-2 bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-200 transition-all duration-200 group inline-block" title="View Transaction">
                                <i class="fas fa-eye group-hover:scale-110 transition-transform"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-exchange-alt text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg font-medium">No transactions found</p>
                                <p class="text-gray-400 text-sm mt-2">Transactions will appear here once created</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6">
            {{ $transactions->links() }}
        </div>
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

