@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-teal-50 to-green-50 p-3 md:p-6">
    <div class="mb-6">
        <a href="{{ route('cashier.deposits.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-green-600 rounded-lg hover:shadow-lg transition font-semibold border border-green-200">
            <i class="fas fa-arrow-left"></i>Back to Deposits
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-green-100">
            <div class="bg-gradient-to-r from-green-600 via-teal-600 to-green-600 p-6 md:p-8 relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center ring-4 ring-white/30">
                        <i class="fas fa-arrow-down text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-white mb-1">Deposit Receipt</h1>
                        <p class="text-green-100 text-sm">Receipt #{{ str_pad($deposit->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="px-4 py-2 rounded-full text-sm font-bold bg-white text-green-600 shadow-lg">
                            <i class="fas fa-check-circle mr-1"></i>Completed
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-3xl p-8 mb-8 text-white shadow-xl">
                    <p class="text-sm font-semibold text-green-100 uppercase mb-2">Deposit Amount</p>
                    <p class="text-5xl font-bold mb-2">+{{ number_format($deposit->amount) }}</p>
                    <p class="text-green-100">UGX (Ugandan Shillings)</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Member</p>
                                <p class="text-lg font-bold text-gray-900">{{ $deposit->member->full_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($deposit->member)
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-id-card text-blue-500"></i>
                                <span>{{ $deposit->member->member_id }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-phone text-blue-500"></i>
                                <span>{{ $deposit->member->contact ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-wallet text-blue-500"></i>
                                <span>Balance: {{ number_format($deposit->member->balance ?? 0) }} UGX</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                                <i class="fas fa-calendar text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Transaction Date</p>
                                <p class="text-lg font-bold text-gray-900">{{ $deposit->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-clock text-purple-500"></i>
                                <span>{{ $deposit->created_at->format('h:i A') }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-history text-purple-500"></i>
                                <span>{{ $deposit->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                                <i class="fas fa-hashtag text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Transaction ID</p>
                                <p class="text-lg font-bold text-gray-900">{{ $deposit->transaction_id ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @if($deposit->reference)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-file-invoice text-green-500"></i>
                                <span>Ref: {{ $deposit->reference }}</span>
                            </div>
                            @endif
                            @if($deposit->receipt_number)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-receipt text-green-500"></i>
                                <span>Receipt: {{ $deposit->receipt_number }}</span>
                            </div>
                            @endif
                            @if($deposit->batch_id)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-layer-group text-green-500"></i>
                                <span>Batch: {{ $deposit->batch_id }}</span>
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
                                <p class="text-lg font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $deposit->payment_method ?? 'N/A')) }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @if($deposit->channel)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-broadcast-tower text-orange-500"></i>
                                <span>Channel: {{ ucfirst($deposit->channel) }}</span>
                            </div>
                            @endif
                            @if($deposit->location)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt text-orange-500"></i>
                                <span>{{ $deposit->location }}</span>
                            </div>
                            @endif
                            @if($deposit->priority)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-flag text-orange-500"></i>
                                <span>Priority: {{ ucfirst($deposit->priority) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @php
                    $fee = $deposit->fee ?? 0;
                    $tax = $deposit->tax_amount ?? 0;
                    $commission = $deposit->commission ?? 0;
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

                @if($deposit->category)
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 border border-indigo-100 mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <i class="fas fa-tag text-white"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Category</p>
                            <p class="text-lg font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $deposit->category)) }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    @if($deposit->currency)
                    <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-2xl p-6 border border-teal-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center">
                                <i class="fas fa-money-check text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Currency</p>
                                <p class="text-lg font-bold text-gray-900">{{ $deposit->currency }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($deposit->status)
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Status</p>
                                <p class="text-lg font-bold text-gray-900">{{ ucfirst($deposit->status) }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($deposit->balance_before || $deposit->balance_after)
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                <i class="fas fa-wallet text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Balance Before</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($deposit->balance_before ?? 0) }} UGX</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($deposit->balance_after)
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Balance After</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($deposit->balance_after ?? 0) }} UGX</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($deposit->net_amount)
                    <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-2xl p-6 border border-green-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                                <i class="fas fa-calculator text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Net Amount</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($deposit->net_amount) }} UGX</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($deposit->scheduled_at)
                    <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl p-6 border border-yellow-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-500 to-amber-600 flex items-center justify-center">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Scheduled At</p>
                                <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($deposit->scheduled_at)->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($deposit->completed_at)
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                                <i class="fas fa-check-double text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Completed At</p>
                                <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($deposit->completed_at)->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($deposit->processed_by)
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 border border-indigo-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <i class="fas fa-user-tie text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Processed By</p>
                                <p class="text-lg font-bold text-gray-900">{{ $deposit->processedBy->name ?? 'System' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($deposit->notification_sent)
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl p-6 border border-yellow-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-500 to-orange-600 flex items-center justify-center">
                                <i class="fas fa-bell text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Notification</p>
                                <p class="text-lg font-bold text-gray-900">Sent</p>
                                @if($deposit->notification_sent_at)
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($deposit->notification_sent_at)->format('M d, Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                @if($deposit->description)
                <div class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded-2xl p-6 border border-orange-100 mb-8">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-yellow-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-sticky-note text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Notes</p>
                            <p class="text-gray-900 leading-relaxed">{{ $deposit->description }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="flex gap-3">
                    @if($deposit->member)
                    <a href="{{ route('cashier.members.show', $deposit->member->id) }}" class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-xl hover:shadow-xl transition font-bold text-center">
                        <i class="fas fa-user mr-2"></i>View Member
                    </a>
                    @endif
                    <button onclick="window.print()" class="px-6 py-3 bg-white border-2 border-green-600 text-green-600 rounded-xl hover:bg-green-50 transition font-bold">
                        <i class="fas fa-print mr-2"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
