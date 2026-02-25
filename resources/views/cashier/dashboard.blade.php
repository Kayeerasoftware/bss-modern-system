@extends('layouts.cashier')

@section('title', 'Cashier Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-50 p-3 md:p-6" x-data="{ activeTab: 'overview' }">
    <!-- Header -->
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-blue-600 to-cyan-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                    <i class="fas fa-cash-register text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 via-cyan-600 to-blue-600 bg-clip-text text-transparent mb-1 md:mb-2">Cashier Dashboard</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">Welcome, {{ auth()->user()->name ?? 'Cashier' }} - Daily transaction management</p>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-blue-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Transaction data...</span>
    </div>

    <!-- Key Metrics -->
    @include('cashier.partials.stats-cards')

    <!-- Charts Section with Tabs -->
    @include('cashier.partials.charts')

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl md:rounded-2xl shadow-xl p-4 md:p-6 hover:shadow-2xl transition border border-gray-100">
        <div class="flex justify-between items-center mb-3 md:mb-4">
            <h3 class="text-base md:text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-history text-blue-600 mr-2 text-sm md:text-base"></i>
                <span class="text-sm md:text-base">Recent Transactions</span>
            </h3>
            <a href="{{ route('cashier.transactions.index') }}" class="text-blue-600 text-xs md:text-sm hover:underline font-semibold">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentTransactions as $transaction)
                    <tr class="hover:bg-blue-50 transition cursor-pointer">
                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm">{{ $transaction->created_at->format('H:i') }}</td>
                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm font-semibold">{{ $transaction->member->full_name ?? 'N/A' }}</td>
                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $transaction->type == 'deposit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap text-xs md:text-sm font-bold {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 0) }}
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full font-semibold bg-blue-100 text-blue-800">
                                {{ ucfirst($transaction->status ?? 'completed') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">No transactions today</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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

@push('scripts')
@include('cashier.partials.scripts')
@endpush
