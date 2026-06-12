@extends('layouts.member')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 p-3 md:p-6">
    <!-- Organization Header (Print Only) -->
    <div class="print-only mb-8">
        <div class="text-center border-b-2 border-purple-600 pb-4">
            <h1 class="text-3xl font-bold text-purple-600 mb-2">BSS Investment Group</h1>
            <p class="text-gray-600">Business Support System - Financial Services</p>
            <p class="text-sm text-gray-500">Email: info@bss.com | Phone: +256 XXX XXX XXX</p>
            <p class="text-sm text-gray-500">Address: Kampala, Uganda</p>
        </div>
        <div class="text-center mt-4">
            <h2 class="text-2xl font-bold text-gray-800">LOAN DETAILS STATEMENT</h2>
            <p class="text-gray-600">Generated on {{ now()->format('F d, Y') }}</p>
        </div>
    </div>
    <div class="mb-4 md:mb-6 no-print">
        <div class="flex items-center justify-between gap-2 md:gap-4">
            <div class="flex items-center gap-2 md:gap-4">
                <a href="{{ route('member.loans.my-loans') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                    <i class="fas fa-arrow-left text-purple-600"></i>
                </a>
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 p-2 md:p-4 rounded-xl shadow-xl">
                        <i class="fas fa-file-invoice-dollar text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-1">Loan Details</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Loan #{{ $loan->id }} - {{ $loan->purpose ?? 'General Purpose' }}</p>
                </div>
            </div>
            <button onclick="window.print()" class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-lg transition-all text-sm font-semibold">
                <i class="fas fa-print mr-2"></i>Print
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto">
        <!-- Loan Overview Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-6">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    Loan Overview
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-money-bill text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-semibold">Loan Amount</p>
                                <p class="text-xl font-bold text-gray-900">UGX {{ number_format($loan->amount) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-pink-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-percentage text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-semibold">Interest Rate</p>
                                <p class="text-xl font-bold text-gray-900">{{ $loan->interest_rate ?? 0 }}%</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calendar text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-semibold">Duration</p>
                                <p class="text-xl font-bold text-gray-900">{{ $loan->repayment_months ?? 0 }} months</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-chart-line text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-semibold">Status</p>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $loan->status == 'approved' ? 'bg-green-100 text-green-800' : ($loan->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $loan->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                @php
                    $progress = $loan->amount > 0 ? (($loan->paid_amount ?? 0) / $loan->amount) * 100 : 0;
                @endphp
                <div class="mt-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-semibold text-gray-700">Repayment Progress</span>
                        <span class="text-sm font-semibold text-gray-700">{{ number_format($progress, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-4 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="flex justify-between items-center mt-2 text-sm text-gray-600">
                        <span>Paid: UGX {{ number_format($loan->paid_amount ?? 0) }}</span>
                        <span>Remaining: UGX {{ number_format($loan->remaining_balance) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loan Details -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-clipboard-list"></i>
                    Loan Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Purpose</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $loan->purpose ?? 'Not specified' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Application Date</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $loan->created_at->format('F d, Y') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Monthly Payment</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg font-semibold">UGX {{ number_format($loan->monthly_payment ?? 0) }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Total Interest</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg font-semibold">UGX {{ number_format($loan->interest ?? 0) }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Processing Fee</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">UGX {{ number_format($loan->processing_fee ?? 0) }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Total Payable</label>
                            <p class="text-gray-900 bg-purple-50 p-3 rounded-lg font-bold text-lg text-purple-700">UGX {{ number_format($loan->amount + ($loan->interest ?? 0) + ($loan->processing_fee ?? 0)) }}</p>
                        </div>
                    </div>
                </div>
                
                @if($loan->applicant_comment)
                <div class="mt-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Comments</label>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-gray-700">{{ $loan->applicant_comment }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; }
    .print-only { display: block !important; }
    .no-print { display: none !important; }
    .bg-gradient-to-br { background: white !important; }
    .shadow-xl, .shadow-lg { box-shadow: none !important; }
    .rounded-xl, .rounded-2xl { border-radius: 8px !important; }
    .bg-gradient-to-r { background: #6b46c1 !important; color: white !important; }
    .text-purple-600 { color: #6b46c1 !important; }
    .border-gray-100 { border: 1px solid #e5e7eb !important; }
    .p-3, .p-6 { padding: 1rem !important; }
    .mb-4, .mb-6 { margin-bottom: 1rem !important; }
    .gap-6 { gap: 1rem !important; }
}

@media screen {
    .print-only { display: none; }
}
</style>
@endsection

