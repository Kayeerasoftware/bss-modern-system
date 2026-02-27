@extends('layouts.shareholder')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('shareholder.transactions') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left text-purple-600"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 bg-clip-text text-transparent">Transaction Details</h2>
                <p class="text-gray-600 text-sm">#{{ $transaction->id }}</p>
            </div>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
            <i class="fas fa-print mr-2"></i>Print
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
        <!-- Left Column - Member Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 p-6 text-center">
                    @if($transaction->member && $transaction->member->profile_picture_url)
                        <img src="{{ $transaction->member->profile_picture_url }}" class="w-24 h-24 rounded-full mx-auto object-cover ring-4 ring-white shadow-xl" alt="">
                    @else
                        <div class="w-24 h-24 rounded-full bg-white mx-auto flex items-center justify-center ring-4 ring-white shadow-xl">
                            <span class="text-purple-600 font-bold text-3xl">{{ substr($transaction->member->name ?? 'N', 0, 1) }}</span>
                        </div>
                    @endif
                    <h3 class="text-white text-xl font-bold mt-4">{{ $transaction->member->full_name ?? 'N/A' }}</h3>
                    <p class="text-white/80 text-sm">{{ $transaction->member->member_id ?? '' }}</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-xl">
                        <i class="fas fa-envelope text-purple-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Email</p>
                            <p class="font-semibold text-sm">{{ $transaction->member->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-pink-50 rounded-xl">
                        <i class="fas fa-phone text-pink-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Phone</p>
                            <p class="font-semibold text-sm">{{ $transaction->member->contact ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-indigo-50 rounded-xl">
                        <i class="fas fa-wallet text-indigo-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Current Balance</p>
                            <p class="font-semibold text-sm">UGX {{ number_format($transaction->member->balance ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Transaction Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-money-bill-wave text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Amount</p>
                    <p class="text-lg font-bold">{{ number_format($transaction->amount, 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-exchange-alt text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Type</p>
                    <p class="text-lg font-bold">{{ ucfirst($transaction->type) }}</p>
                </div>
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-check-circle text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Status</p>
                    <p class="text-lg font-bold">{{ ucfirst($transaction->status) }}</p>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-calculator text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Net Amount</p>
                    <p class="text-lg font-bold">{{ number_format($transaction->net_amount ?? $transaction->amount, 0) }}</p>
                </div>
            </div>

            <!-- Transaction Information Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        Transaction Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Transaction ID</p>
                            <p class="font-bold text-gray-900">{{ $transaction->transaction_id ?? '#' . $transaction->id }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Date & Time</p>
                            <p class="font-bold text-gray-900">{{ $transaction->transaction_date ? $transaction->transaction_date->format('M d, Y H:i') : $transaction->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Transaction Type</p>
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
                                    <i class="fas fa-exchange-alt text-[8px] mr-1"></i>Transfer
                                </span>
                            @endif
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Category</p>
                            <p class="font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->category ?? 'N/A')) }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Status</p>
                            @if($transaction->status == 'completed')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-check text-[8px] mr-1"></i>Completed
                                </span>
                            @elseif($transaction->status == 'pending')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    <i class="fas fa-clock text-[8px] mr-1"></i>Pending
                                </span>
                            @elseif($transaction->status == 'reversed')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                                    <i class="fas fa-undo text-[8px] mr-1"></i>Reversed
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200">
                                    <i class="fas fa-times text-[8px] mr-1"></i>Failed
                                </span>
                            @endif
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Priority</p>
                            @php
                                $priorityColors = [
                                    'low' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    'normal' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'high' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'urgent' => 'bg-red-100 text-red-800 border-red-200',
                                ];
                                $priority = $transaction->priority ?? 'normal';
                            @endphp
                            <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $priorityColors[$priority] }}">
                                {{ ucfirst($priority) }}
                            </span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Amount</p>
                            <p class="font-bold text-lg {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                UGX {{ number_format($transaction->amount, 2) }}
                            </p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Transaction Fee</p>
                            <p class="font-bold text-gray-900">UGX {{ number_format($transaction->fee ?? 0, 2) }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Tax Amount</p>
                            <p class="font-bold text-gray-900">UGX {{ number_format($transaction->tax_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Commission</p>
                            <p class="font-bold text-gray-900">UGX {{ number_format($transaction->commission ?? 0, 2) }}</p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-200">
                            <p class="text-xs text-gray-600 mb-1">Net Amount</p>
                            <p class="font-bold text-lg text-green-600">UGX {{ number_format($transaction->net_amount ?? $transaction->amount, 2) }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Payment Method</p>
                            <p class="font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method ?? 'N/A')) }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Channel</p>
                            <p class="font-bold text-gray-900">{{ ucfirst($transaction->channel ?? 'N/A') }}</p>
                        </div>
                        @if($transaction->reference)
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Reference Number</p>
                            <p class="font-bold text-gray-900">{{ $transaction->reference }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            @if($transaction->description)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-pink-600 to-indigo-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-comment"></i>
                        Description
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed">{{ $transaction->description }}</p>
                </div>
            </div>
            @endif

            <!-- Balance Tracking -->
            @if($transaction->balance_before !== null || $transaction->balance_after !== null)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-balance-scale"></i>
                        Balance Tracking
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-2">Balance Before</p>
                            <p class="text-xl font-bold text-blue-600">UGX {{ number_format($transaction->balance_before ?? 0, 2) }}</p>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-2">Transaction</p>
                            <p class="text-xl font-bold {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type == 'deposit' ? '+' : '-' }} UGX {{ number_format($transaction->net_amount ?? $transaction->amount, 2) }}
                            </p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-2">Balance After</p>
                            <p class="text-xl font-bold text-green-600">UGX {{ number_format($transaction->balance_after ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, button, a[href*="transactions"] {
        display: none !important;
    }
}
</style>
@endsection

