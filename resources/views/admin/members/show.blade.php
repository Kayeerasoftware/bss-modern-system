@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.members.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left text-blue-600"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">Member Details</h2>
                <p class="text-gray-600 text-sm">Complete member information</p>
            </div>
        </div>
        <a href="{{ route('admin.members.edit', $member->id) }}" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 p-8 text-center">
                    <div class="flex justify-center mb-4">
                        @php
                            $hasProfilePicture = $member->profile_picture || ($member->user && $member->user->profile_picture);
                        @endphp
                        @if($hasProfilePicture)
                            <img src="{{ $member->profile_picture_url }}" alt="{{ $member->full_name }}" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-2xl">
                        @else
                            <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center border-4 border-white shadow-2xl">
                                <span class="text-5xl font-bold text-gray-400">{{ substr($member->full_name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-1">{{ $member->full_name }}</h3>
                    <p class="text-white/80 text-sm mb-3">{{ $member->member_id }}</p>
                    @php
                        $roleColors = [
                            'client' => 'bg-blue-100 text-blue-800',
                            'shareholder' => 'bg-purple-100 text-purple-800',
                            'cashier' => 'bg-green-100 text-green-800',
                            'td' => 'bg-orange-100 text-orange-800',
                            'ceo' => 'bg-red-100 text-red-800',
                        ];
                        $colorClass = $roleColors[$member->role] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-block px-4 py-2 rounded-full {{ $colorClass }} font-bold text-sm">
                        {{ ucfirst($member->role) }}
                    </span>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i class="fas fa-envelope text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold">Email</p>
                            <p class="text-sm font-bold text-gray-800">{{ $member->email }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <i class="fas fa-phone text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold">Contact</p>
                            <p class="text-sm font-bold text-gray-800">{{ $member->contact }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="bg-red-100 p-2 rounded-lg">
                            <i class="fas fa-map-marker-alt text-red-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold">Location</p>
                            <p class="text-sm font-bold text-gray-800">{{ $member->location }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="bg-orange-100 p-2 rounded-lg">
                            <i class="fas fa-briefcase text-orange-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold">Occupation</p>
                            <p class="text-sm font-bold text-gray-800">{{ $member->occupation }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="bg-purple-100 p-2 rounded-lg">
                            <i class="fas fa-calendar text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold">Member Since</p>
                            <p class="text-sm font-bold text-gray-800">{{ $member->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Section -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Financial Summary -->
            <div class="bg-white rounded-3xl shadow-2xl p-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-gradient-to-r from-green-600 to-teal-600 p-3 rounded-xl">
                        <i class="fas fa-coins text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Financial Summary</h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 text-white text-center transform hover:scale-105 transition-all">
                        <i class="fas fa-piggy-bank text-3xl mb-2 opacity-80"></i>
                        <p class="text-2xl font-bold">{{ number_format($member->savings, 2) }}</p>
                        <p class="text-xs opacity-80">Savings</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 text-white text-center transform hover:scale-105 transition-all">
                        <i class="fas fa-wallet text-3xl mb-2 opacity-80"></i>
                        <p class="text-2xl font-bold">{{ number_format($member->savings_balance, 2) }}</p>
                        <p class="text-xs opacity-80">Savings Balance</p>
                    </div>
                    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-4 text-white text-center transform hover:scale-105 transition-all">
                        <i class="fas fa-hand-holding-usd text-3xl mb-2 opacity-80"></i>
                        <p class="text-2xl font-bold">{{ number_format($member->loan, 2) }}</p>
                        <p class="text-xs opacity-80">Loan</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 text-white text-center transform hover:scale-105 transition-all">
                        <i class="fas fa-money-check-alt text-3xl mb-2 opacity-80"></i>
                        <p class="text-2xl font-bold">{{ number_format($member->balance, 2) }}</p>
                        <p class="text-xs opacity-80">Balance</p>
                    </div>
                </div>
            </div>

            <!-- Activity Summary -->
            <div class="bg-white rounded-3xl shadow-2xl p-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-gradient-to-r from-orange-600 to-red-600 p-3 rounded-xl">
                        <i class="fas fa-chart-line text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Activity Summary</h3>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl p-6 text-center transform hover:scale-105 transition-all">
                        <i class="fas fa-file-invoice-dollar text-4xl text-orange-600 mb-2"></i>
                        <p class="text-3xl font-bold text-orange-700">{{ $member->loans->count() }}</p>
                        <p class="text-sm text-orange-600 font-semibold">Total Loans</p>
                    </div>
                    <div class="bg-gradient-to-br from-teal-100 to-teal-200 rounded-2xl p-6 text-center transform hover:scale-105 transition-all">
                        <i class="fas fa-exchange-alt text-4xl text-teal-600 mb-2"></i>
                        <p class="text-3xl font-bold text-teal-700">{{ $member->transactions->count() }}</p>
                        <p class="text-sm text-teal-600 font-semibold">Transactions</p>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-2xl p-6 text-center transform hover:scale-105 transition-all">
                        <i class="fas fa-chart-pie text-4xl text-indigo-600 mb-2"></i>
                        <p class="text-3xl font-bold text-indigo-700">{{ $member->shares->count() }}</p>
                        <p class="text-sm text-indigo-600 font-semibold">Shares</p>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-3xl shadow-2xl p-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-3 rounded-xl">
                        <i class="fas fa-history text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Recent Transactions</h3>
                </div>
                <div class="space-y-3">
                    @forelse($member->transactions->take(5) as $transaction)
                    <div class="flex justify-between items-center p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 p-2 rounded-lg">
                                <i class="fas fa-arrow-right text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">{{ $transaction->type }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        <p class="text-lg font-bold text-green-600">{{ number_format($transaction->amount, 2) }}</p>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 font-medium">No transactions yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

