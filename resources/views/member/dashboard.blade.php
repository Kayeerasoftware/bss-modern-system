@extends('layouts.member')

@section('title', 'Client Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-green-50 p-3 md:p-6">
    <!-- Header -->
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 md:gap-4">
            <div class="flex items-center gap-3 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-blue-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                    <div class="relative w-14 h-14 md:w-16 md:h-16 rounded-xl md:rounded-2xl overflow-hidden shadow-xl bg-white">
                        <img src="{{ auth()->user()->profile_picture_url }}" alt="{{ $member->full_name ?? auth()->user()->name }}" class="w-full h-full object-cover">
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-600 via-blue-600 to-green-600 bg-clip-text text-transparent mb-1 md:mb-2">My Dashboard</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Welcome back, {{ $member->full_name ?? auth()->user()->name }}</p>
                </div>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <a href="{{ route('member.bio-data.view') }}" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg md:rounded-xl hover:shadow-xl transition-all text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2">
                    <i class="fas fa-eye"></i><span>View Bio Data</span>
                </a>
                <a href="{{ route('member.bio-data.create') }}" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg md:rounded-xl hover:shadow-xl transition-all text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2">
                    <i class="fas fa-id-card"></i><span>Edit Bio Data</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Animated Separator -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-green-500 to-blue-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-green-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading your financial data...</span>
    </div>

    <!-- Key Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4 mb-4 md:mb-6">
        <!-- Net Savings -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-3 md:p-4 text-white hover:shadow-2xl transition-all transform hover:scale-105">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-piggy-bank text-2xl md:text-3xl opacity-80"></i>
                <span class="text-xs md:text-sm bg-white/20 px-2 py-1 rounded-full">Deposits - Withdrawals</span>
            </div>
            <p class="text-white/80 text-xs md:text-sm font-medium">Net Savings</p>
            <h3 class="text-xl md:text-3xl font-bold">UGX {{ number_format($stats['mySavings']) }}</h3>
            <p class="mt-1 text-[10px] md:text-xs text-white/80">
                D: {{ number_format($stats['totalDeposits'] ?? 0) }} | W: {{ number_format($stats['totalWithdrawals'] ?? 0) }}
            </p>
        </div>

        <!-- Available Balance -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-3 md:p-4 text-white hover:shadow-2xl transition-all transform hover:scale-105">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-wallet text-2xl md:text-3xl opacity-80"></i>
                <span class="text-xs md:text-sm bg-white/20 px-2 py-1 rounded-full">Synced</span>
            </div>
            <p class="text-white/80 text-xs md:text-sm font-medium">Available Balance</p>
            <h3 class="text-xl md:text-3xl font-bold">UGX {{ number_format($stats['myBalance']) }}</h3>
            <p class="mt-1 text-[10px] md:text-xs text-white/80">
                After Loans: UGX {{ number_format($stats['availableAfterLoans'] ?? 0) }}
            </p>
        </div>

        <!-- Loan Outstanding -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-3 md:p-4 text-white hover:shadow-2xl transition-all transform hover:scale-105">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-hand-holding-usd text-2xl md:text-3xl opacity-80"></i>
                <span class="text-xs md:text-sm bg-white/20 px-2 py-1 rounded-full">{{ $stats['myLoanCount'] }}</span>
            </div>
            <p class="text-white/80 text-xs md:text-sm font-medium">Loan Outstanding</p>
            <h3 class="text-xl md:text-3xl font-bold">UGX {{ number_format($stats['myLoans']) }}</h3>
        </div>

        <!-- Completed Transactions -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-3 md:p-4 text-white hover:shadow-2xl transition-all transform hover:scale-105">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-exchange-alt text-2xl md:text-3xl opacity-80"></i>
                <span class="text-xs md:text-sm bg-white/20 px-2 py-1 rounded-full">Completed</span>
            </div>
            <p class="text-white/80 text-xs md:text-sm font-medium">Transactions</p>
            <h3 class="text-xl md:text-3xl font-bold">{{ $stats['completedTransactions'] ?? 0 }}</h3>
            <p class="mt-1 text-[10px] md:text-xs text-white/80">
                All Statuses: {{ number_format($stats['totalTransactions'] ?? 0) }}
            </p>
        </div>
    </div>

    <div class="mb-4 md:mb-6 rounded-xl border border-emerald-100 bg-white p-3 md:p-4 shadow-sm">
        <p class="text-sm font-bold text-slate-800">How These Values Are Calculated</p>
        <p class="mt-1 text-xs text-slate-600">
            Net Savings = Completed Deposits - Completed Withdrawals. Available Balance = Net Savings - Transfers - Loan Payments. Loan Outstanding is remaining approved loan principal.
        </p>
        <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
            <div class="rounded-lg bg-emerald-50 px-2 py-1.5 text-emerald-800">
                Deposits: UGX {{ number_format($financialSummary['total_deposits'] ?? 0) }}
            </div>
            <div class="rounded-lg bg-rose-50 px-2 py-1.5 text-rose-800">
                Withdrawals: UGX {{ number_format($financialSummary['total_withdrawals'] ?? 0) }}
            </div>
            <div class="rounded-lg bg-indigo-50 px-2 py-1.5 text-indigo-800">
                Transfers: UGX {{ number_format($financialSummary['total_transfers'] ?? 0) }}
            </div>
            <div class="rounded-lg bg-amber-50 px-2 py-1.5 text-amber-800">
                Loan Payments: UGX {{ number_format($financialSummary['total_loan_payments'] ?? 0) }}
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-4 md:mb-6">
        <a href="{{ route('member.deposits.create') }}" class="bg-white rounded-xl shadow-md p-3 md:p-4 hover:shadow-xl transition-all transform hover:scale-105 border-l-4 border-green-500">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 p-2 md:p-3 rounded-lg">
                    <i class="fas fa-plus-circle text-green-600 text-xl md:text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-gray-600 font-medium">Make</p>
                    <p class="text-sm md:text-base font-bold text-gray-900">Deposit</p>
                </div>
            </div>
        </a>

        <a href="{{ route('member.withdrawals.create') }}" class="bg-white rounded-xl shadow-md p-3 md:p-4 hover:shadow-xl transition-all transform hover:scale-105 border-l-4 border-red-500">
            <div class="flex items-center gap-3">
                <div class="bg-red-100 p-2 md:p-3 rounded-lg">
                    <i class="fas fa-minus-circle text-red-600 text-xl md:text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-gray-600 font-medium">Request</p>
                    <p class="text-sm md:text-base font-bold text-gray-900">Withdrawal</p>
                </div>
            </div>
        </a>

        <a href="{{ route('member.loans.apply') }}" class="bg-white rounded-xl shadow-md p-3 md:p-4 hover:shadow-xl transition-all transform hover:scale-105 border-l-4 border-blue-500">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 p-2 md:p-3 rounded-lg">
                    <i class="fas fa-file-invoice-dollar text-blue-600 text-xl md:text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-gray-600 font-medium">Apply for</p>
                    <p class="text-sm md:text-base font-bold text-gray-900">Loan</p>
                </div>
            </div>
        </a>

        <a href="{{ route('member.transactions.history') }}" class="bg-white rounded-xl shadow-md p-3 md:p-4 hover:shadow-xl transition-all transform hover:scale-105 border-l-4 border-purple-500">
            <div class="flex items-center gap-3">
                <div class="bg-purple-100 p-2 md:p-3 rounded-lg">
                    <i class="fas fa-history text-purple-600 text-xl md:text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-gray-600 font-medium">View</p>
                    <p class="text-sm md:text-base font-bold text-gray-900">History</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        <!-- Recent Transactions -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl p-4 md:p-6 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-receipt text-blue-600"></i>
                    Recent Transactions
                </h3>
                <a href="{{ route('member.transactions.history') }}" class="text-blue-600 text-sm hover:underline font-semibold">View All →</a>
            </div>
            <div class="space-y-2 md:space-y-3">
                @forelse($recentTransactions as $transaction)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition border-l-4 {{ $transaction->type == 'deposit' ? 'border-green-500' : 'border-red-500' }}">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg {{ $transaction->type == 'deposit' ? 'bg-green-100' : 'bg-red-100' }}">
                            <i class="fas fa-{{ $transaction->type == 'deposit' ? 'arrow-down' : 'arrow-up' }} {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ ucfirst($transaction->type) }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type == 'deposit' ? '+' : '-' }}UGX {{ number_format($transaction->amount) }}
                        </p>
                        <span class="text-xs px-2 py-1 rounded-full {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No transactions yet</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Active Loans -->
        <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-invoice text-orange-600"></i>
                    My Loans
                </h3>
                <a href="{{ route('member.loans.my-loans') }}" class="text-blue-600 text-sm hover:underline font-semibold">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($activeLoans as $loan)
                <div class="p-3 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg border border-orange-200">
                    <div class="flex justify-between items-start mb-2">
                        <p class="text-sm font-semibold text-gray-900">Loan #{{ $loan->id }}</p>
                        <span class="text-xs px-2 py-1 rounded-full bg-orange-200 text-orange-800 font-semibold">Active</span>
                    </div>
                    <p class="text-lg font-bold text-orange-600 mb-1">UGX {{ number_format($loan->amount) }}</p>
                    <div class="flex justify-between text-xs text-gray-600">
                        <span>Duration: {{ $loan->duration }} months</span>
                        <span>Balance: {{ number_format($loan->balance) }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-hand-holding-usd text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 text-sm mb-3">No active loans</p>
                    <a href="{{ route('member.loans.apply') }}" class="text-blue-600 text-sm hover:underline font-semibold">Apply for a loan →</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Monthly Activity Chart -->
    @if(!empty($monthlyData))
    <div class="mt-4 md:mt-6 bg-white rounded-2xl shadow-xl p-4 md:p-6 border border-gray-100">
        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-chart-line text-green-600"></i>
            Monthly Activity (Last 6 Months)
        </h3>
        <canvas id="monthlyChart" height="80"></canvas>
    </div>
    @endif
</div>

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
@keyframes slide-text { 0% { left: 0%; opacity: 1; } 95% { opacity: 1; } 100% { left: 100%; opacity: 0; } }
.animate-slide-text { animation: slide-text 5s ease-out forwards; }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if(!empty($monthlyData))
const ctx = document.getElementById('monthlyChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
        datasets: [{
            label: 'Deposits',
            data: {!! json_encode(array_column($monthlyData, 'deposits')) !!},
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4
        }, {
            label: 'Withdrawals',
            data: {!! json_encode(array_column($monthlyData, 'withdrawals')) !!},
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
@endif
</script>
@endpush
@endsection
