@extends('layouts.member')

@section('title', 'My Loans')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-50 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-red-600 rounded-xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-orange-600 to-red-600 p-3 md:p-4 rounded-xl shadow-xl">
                        <i class="fas fa-file-invoice-dollar text-white text-2xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-1">My Loans</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Manage your loan applications and repayments</p>
                </div>
            </div>
            <a href="{{ route('member.loans.apply') }}" class="px-4 md:px-6 py-2 md:py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl hover:shadow-xl transition-all font-bold text-sm transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>Apply for Loan
            </a>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-orange-500 to-red-600 rounded-full animate-slide-right"></div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4 mb-4 md:mb-6">
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-3 md:p-4 text-white">
            <i class="fas fa-hand-holding-usd text-2xl mb-2 opacity-80"></i>
            <p class="text-white/80 text-xs font-medium">Total Borrowed</p>
            <h3 class="text-xl md:text-3xl font-bold">UGX {{ number_format($loans->where('status', 'approved')->sum('amount')) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-red-500">
            <i class="fas fa-exclamation-circle text-2xl text-red-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Outstanding</p>
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">UGX {{ number_format($loans->where('status', 'approved')->sum('balance')) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-green-500">
            <i class="fas fa-check-circle text-2xl text-green-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Active Loans</p>
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">{{ $loans->where('status', 'approved')->count() }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-yellow-500">
            <i class="fas fa-clock text-2xl text-yellow-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Pending</p>
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">{{ $loans->where('status', 'pending')->count() }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 border border-gray-100">
        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-list text-orange-600"></i>
            Loan Applications
        </h3>

        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl shadow-lg border border-orange-100 mb-4">
            <form method="GET" class="p-3">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    <div class="md:col-span-6 relative">
                        <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-orange-400 text-xs"></i>
                        <input type="text" name="search" placeholder="Search loans..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-500 bg-white">
                    </div>
                    <div class="md:col-span-3 relative">
                        <select name="status" class="w-full px-2 py-1.5 text-xs border border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-500 bg-white" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="repaid" {{ request('status') === 'repaid' ? 'selected' : '' }}>Repaid</option>
                        </select>
                    </div>
                    <div class="md:col-span-3 flex gap-1.5">
                        <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg font-semibold">
                            <i class="fas fa-search mr-1"></i>Search
                        </button>
                        <a href="{{ route('member.loans.my-loans') }}" class="px-2 py-1.5 text-xs bg-gray-200 text-gray-700 rounded-lg">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="space-y-3">
            @forelse($loans as $loan)
            <div class="border border-gray-200 rounded-xl p-4 hover:shadow-lg transition">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-bold text-gray-900">Loan #{{ $loan->id }}</h4>
                        <p class="text-xs text-gray-500">Applied: {{ $loan->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                        {{ $loan->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $loan->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $loan->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                        {{ ($loan->status === 'approved' && $loan->remaining_balance <= 0) ? 'bg-blue-100 text-blue-800' : '' }}">
                        {{ $loan->status_label }}
                    </span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                    <div>
                        <p class="text-xs text-gray-500">Amount</p>
                        <p class="font-bold text-orange-600">UGX {{ number_format($loan->amount) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Duration</p>
                        <p class="font-bold">{{ $loan->repayment_months }} months</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Interest</p>
                        <p class="font-bold">UGX {{ number_format($loan->interest) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Balance</p>
                        <p class="font-bold text-red-600">UGX {{ number_format($loan->remaining_balance) }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('member.loans.show', $loan->id) }}" class="px-3 py-1.5 bg-blue-100 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-200">
                        <i class="fas fa-eye mr-1"></i>View Details
                    </a>
                    @if($loan->status == 'approved' && $loan->remaining_balance > 0)
                    <a href="{{ route('member.loans.repay', $loan->id) }}" class="px-3 py-1.5 bg-green-100 text-green-600 rounded-lg text-xs font-semibold hover:bg-green-200">
                        <i class="fas fa-money-bill mr-1"></i>Make Payment
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <i class="fas fa-file-invoice-dollar text-5xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 text-lg font-medium mb-2">No loans yet</p>
                <a href="{{ route('member.loans.apply') }}" class="inline-block px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold">
                    <i class="fas fa-plus mr-2"></i>Apply for Your First Loan
                </a>
            </div>
            @endforelse
        </div>

        @if($loans->hasPages())
        <div class="mt-4">{{ $loans->links() }}</div>
        @endif
    </div>
</div>

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
</style>
@endsection
