@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 via-pink-50 to-red-50 p-3 md:p-6">
    <div class="mb-6">
        <a href="{{ route('cashier.withdrawals.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-red-600 rounded-lg hover:shadow-lg transition font-semibold border border-red-200">
            <i class="fas fa-arrow-left"></i>Back to Withdrawals
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-red-100">
            <div class="bg-gradient-to-r from-red-600 via-pink-600 to-red-600 p-6 md:p-8 relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center ring-4 ring-white/30">
                        <i class="fas fa-arrow-up text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-white mb-1">Withdrawal Receipt</h1>
                        <p class="text-red-100 text-sm">Receipt #{{ str_pad($withdrawal->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="px-4 py-2 rounded-full text-sm font-bold bg-white text-red-600 shadow-lg">
                            <i class="fas fa-check-circle mr-1"></i>Completed
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-3xl p-8 mb-8 text-white shadow-xl">
                    <p class="text-sm font-semibold text-red-100 uppercase mb-2">Withdrawal Amount</p>
                    <p class="text-5xl font-bold mb-2">-{{ number_format($withdrawal->amount) }}</p>
                    <p class="text-red-100">UGX (Ugandan Shillings)</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Member</p>
                                <p class="text-lg font-bold text-gray-900">{{ $withdrawal->member->full_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($withdrawal->member)
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-id-card text-blue-500"></i>
                                <span>{{ $withdrawal->member->member_id }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-phone text-blue-500"></i>
                                <span>{{ $withdrawal->member->contact ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-wallet text-blue-500"></i>
                                <span>Balance: {{ number_format($withdrawal->member->balance ?? 0) }} UGX</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl p-6 border border-purple-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                <i class="fas fa-calendar text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Transaction Date</p>
                                <p class="text-lg font-bold text-gray-900">{{ $withdrawal->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-clock text-purple-500"></i>
                                <span>{{ $withdrawal->created_at->format('h:i A') }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-history text-purple-500"></i>
                                <span>{{ $withdrawal->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-6 border border-red-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center">
                                <i class="fas fa-hashtag text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Transaction ID</p>
                                <p class="text-lg font-bold text-gray-900">{{ $withdrawal->transaction_id ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @if($withdrawal->reference)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-file-invoice text-red-500"></i>
                                <span>Ref: {{ $withdrawal->reference }}</span>
                            </div>
                            @endif
                            @if($withdrawal->receipt_number)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-receipt text-red-500"></i>
                                <span>Receipt: {{ $withdrawal->receipt_number }}</span>
                            </div>
                            @endif
                            @if($withdrawal->batch_id)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-layer-group text-red-500"></i>
                                <span>Batch: {{ $withdrawal->batch_id }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-6 border border-orange-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center">
                                <i class="fas fa-credit-card text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Payment Method</p>
                                <p class="text-lg font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $withdrawal->payment_method ?? 'N/A')) }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @if($withdrawal->channel)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-broadcast-tower text-orange-500"></i>
                                <span>Channel: {{ ucfirst($withdrawal->channel) }}</span>
                            </div>
                            @endif
                            @if($withdrawal->location)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt text-orange-500"></i>
                                <span>{{ $withdrawal->location }}</span>
                            </div>
                            @endif
                            @if($withdrawal->priority)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-flag text-orange-500"></i>
                                <span>Priority: {{ ucfirst($withdrawal->priority) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @php
                    $fee = $withdrawal->fee ?? 0;
                    $tax = $withdrawal->tax_amount ?? 0;
                    $commission = $withdrawal->commission ?? 0;
                    $totalCharges = $fee + $tax + $commission;
                @endphp

                @if($totalCharges > 0)
                <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-6 border border-red-100 mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center">
                            <i class="fas fa-coins text-white"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Transaction Charges</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($totalCharges) }} UGX</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        @if($fee > 0)
                        <div class="text-center p-3 bg-white rounded-xl">
                            <p class="text-xs text-gray-500 mb-1">Fee</p>
                            <p class="text-sm font-bold text-gray-900">{{ number_format($fee) }}</p>
                        </div>
                        @endif
                        @if($tax > 0)
                        <div class="text-center p-3 bg-white rounded-xl">
                            <p class="text-xs text-gray-500 mb-1">Tax</p>
                            <p class="text-sm font-bold text-gray-900">{{ number_format($tax) }}</p>
                        </div>
                        @endif
                        @if($commission > 0)
                        <div class="text-center p-3 bg-white rounded-xl">
                            <p class="text-xs text-gray-500 mb-1">Commission</p>
                            <p class="text-sm font-bold text-gray-900">{{ number_format($commission) }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($withdrawal->category)
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 border border-indigo-100 mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <i class="fas fa-tag text-white"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Category</p>
                            <p class="text-lg font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $withdrawal->category)) }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    @if($withdrawal->currency)
                    <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-2xl p-6 border border-teal-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center">
                                <i class="fas fa-money-check text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Currency</p>
                                <p class="text-lg font-bold text-gray-900">{{ $withdrawal->currency }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($withdrawal->status)
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Status</p>
                                <p class="text-lg font-bold text-gray-900">{{ ucfirst($withdrawal->status) }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($withdrawal->balance_before || $withdrawal->balance_after)
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                <i class="fas fa-wallet text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Balance Before</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($withdrawal->balance_before ?? 0) }} UGX</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($withdrawal->balance_after)
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Balance After</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($withdrawal->balance_after ?? 0) }} UGX</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($withdrawal->net_amount)
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-6 border border-red-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center">
                                <i class="fas fa-calculator text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Net Amount</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($withdrawal->net_amount) }} UGX</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($withdrawal->scheduled_at)
                    <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl p-6 border border-yellow-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-500 to-amber-600 flex items-center justify-center">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Scheduled At</p>
                                <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($withdrawal->scheduled_at)->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($withdrawal->completed_at)
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                                <i class="fas fa-check-double text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Completed At</p>
                                <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($withdrawal->completed_at)->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($withdrawal->processed_by)
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 border border-indigo-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <i class="fas fa-user-tie text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Processed By</p>
                                <p class="text-lg font-bold text-gray-900">{{ $withdrawal->processedBy->name ?? 'System' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($withdrawal->notification_sent)
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl p-6 border border-yellow-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-500 to-orange-600 flex items-center justify-center">
                                <i class="fas fa-bell text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Notification</p>
                                <p class="text-lg font-bold text-gray-900">Sent</p>
                                @if($withdrawal->notification_sent_at)
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($withdrawal->notification_sent_at)->format('M d, Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                @if($withdrawal->description)
                <div class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded-2xl p-6 border border-orange-100 mb-8">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-yellow-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-sticky-note text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Notes</p>
                            <p class="text-gray-900 leading-relaxed">{{ $withdrawal->description }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="flex gap-3">
                    @if($withdrawal->member)
                    <a href="{{ route('cashier.members.show', $withdrawal->member->id) }}" class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition font-bold text-center">
                        <i class="fas fa-user mr-2"></i>View Member
                    </a>
                    @endif
                    <button onclick="window.print()" class="px-6 py-3 bg-white border-2 border-red-600 text-red-600 rounded-xl hover:bg-red-50 transition font-bold">
                        <i class="fas fa-print mr-2"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
