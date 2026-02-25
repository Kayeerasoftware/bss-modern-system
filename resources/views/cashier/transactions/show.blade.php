@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 p-3 md:p-6">
    <div class="mb-6">
        <a href="{{ route('cashier.transactions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-purple-600 rounded-lg hover:shadow-lg transition font-semibold border border-purple-200">
            <i class="fas fa-arrow-left"></i>Back to Transactions
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-purple-100">
            <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 p-6 md:p-8 relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center ring-4 ring-white/30">
                        <i class="fas fa-exchange-alt text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-white mb-1">Transaction Details</h1>
                        <p class="text-purple-100 text-sm">ID: #{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="hidden md:block">
                        <span class="px-4 py-2 rounded-full text-sm font-bold {{ $transaction->type == 'deposit' ? 'bg-green-500' : 'bg-red-500' }} text-white shadow-lg">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Member</p>
                                <p class="text-lg font-bold text-gray-900">{{ $transaction->member->full_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($transaction->member)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-id-card text-purple-500"></i>
                            <span>{{ $transaction->member->member_id }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 mt-2">
                            <i class="fas fa-phone text-purple-500"></i>
                            <span>{{ $transaction->member->contact ?? 'N/A' }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="bg-gradient-to-br {{ $transaction->type == 'deposit' ? 'from-green-50 to-teal-50 border-green-100' : 'from-red-50 to-pink-50 border-red-100' }} rounded-2xl p-6 border">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $transaction->type == 'deposit' ? 'from-green-500 to-teal-600' : 'from-red-500 to-pink-600' }} flex items-center justify-center">
                                <i class="fas fa-{{ $transaction->type == 'deposit' ? 'arrow-down' : 'arrow-up' }} text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Amount</p>
                                <p class="text-3xl font-bold {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount) }}
                                </p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">UGX (Ugandan Shillings)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center">
                                <i class="fas fa-calendar text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Date & Time</p>
                                <p class="text-lg font-bold text-gray-900">{{ $transaction->created_at->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-600">{{ $transaction->created_at->format('h:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded-2xl p-6 border border-orange-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-yellow-600 flex items-center justify-center">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Time Ago</p>
                                <p class="text-lg font-bold text-gray-900">{{ $transaction->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($transaction->description)
                <div class="mt-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-500 to-gray-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Description</p>
                            <p class="text-gray-900 leading-relaxed">{{ $transaction->description }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-8 flex gap-3">
                    @if($transaction->member)
                    <a href="{{ route('cashier.members.show', $transaction->member->id) }}" class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition font-bold text-center">
                        <i class="fas fa-user mr-2"></i>View Member
                    </a>
                    @endif
                    <button onclick="window.print()" class="px-6 py-3 bg-white border-2 border-purple-600 text-purple-600 rounded-xl hover:bg-purple-50 transition font-bold">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
