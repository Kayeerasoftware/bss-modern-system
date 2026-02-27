@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-teal-50 to-blue-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.loans.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left text-green-600"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 bg-clip-text text-transparent">Loan Details</h2>
                <p class="text-gray-600 text-sm">{{ $loan->loan_id }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.loans.print', $loan->id) }}" class="px-4 py-2 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                <i class="fas fa-file-pdf mr-2"></i>Print PDF
            </a>
            <a href="{{ route('admin.loans.edit', $loan->id) }}" class="px-4 py-2 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
        <!-- Left Column - Member Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 p-6 text-center">
                    @if($loan->member && $loan->member->profile_picture_url)
                        <img src="{{ $loan->member->profile_picture_url }}" class="w-24 h-24 rounded-full mx-auto object-cover ring-4 ring-white shadow-xl" alt="">
                    @else
                        <div class="w-24 h-24 rounded-full bg-white mx-auto flex items-center justify-center ring-4 ring-white shadow-xl">
                            <span class="text-green-600 font-bold text-3xl">{{ substr($loan->member->full_name ?? 'N', 0, 1) }}</span>
                        </div>
                    @endif
                    <h3 class="text-white text-xl font-bold mt-4">{{ $loan->member->full_name ?? 'N/A' }}</h3>
                    <p class="text-white/80 text-sm">{{ $loan->member->member_id ?? '' }}</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-xl">
                        <i class="fas fa-phone text-green-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Contact</p>
                            <p class="font-semibold text-sm">{{ $loan->member->contact ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-teal-50 rounded-xl">
                        <i class="fas fa-envelope text-teal-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Email</p>
                            <p class="font-semibold text-sm">{{ $loan->member->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-xl">
                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Location</p>
                            <p class="font-semibold text-sm">{{ $loan->member->location ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Financial Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-money-bill-wave text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Loan Amount</p>
                    <p class="text-lg font-bold">{{ number_format($loan->amount, 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-percentage text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Interest</p>
                    <p class="text-lg font-bold">{{ number_format($loan->interest, 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-calculator text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Total Repayment</p>
                    <p class="text-lg font-bold">{{ number_format($loan->amount + $loan->interest, 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-calendar-alt text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Monthly</p>
                    <p class="text-lg font-bold">{{ number_format($loan->monthly_payment, 0) }}</p>
                </div>
            </div>

            <!-- Loan Information Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Loan Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Loan ID</p>
                            <p class="font-bold text-gray-900">{{ $loan->loan_id }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Status</p>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'approved' => 'bg-green-100 text-green-800 border-green-200',
                                    'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                    'disbursed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                ];
                                $colorClass = $statusColors[$loan->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $colorClass }}">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Repayment Period</p>
                            <p class="font-bold text-gray-900">{{ $loan->repayment_months }} months</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Application Date</p>
                            <p class="font-bold text-gray-900">{{ $loan->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loan Purpose Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-teal-600 to-blue-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-clipboard"></i>
                        Loan Purpose
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed">{{ $loan->purpose }}</p>
                </div>
            </div>

            <!-- Applicant Comment -->
            @if($loan->applicant_comment)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-comment"></i>
                        Applicant Comment
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed">{{ $loan->applicant_comment }}</p>
                </div>
            </div>
            @endif

            <!-- Guarantors -->
            @if($loan->guarantor_1_name)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-users"></i>
                        Guarantors
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-orange-50 rounded-xl border-2 border-orange-200">
                            <p class="text-xs text-orange-600 font-bold mb-2">Guarantor 1</p>
                            <p class="font-bold text-gray-900 mb-1">{{ $loan->guarantor_1_name }}</p>
                            <p class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-phone text-orange-600"></i>{{ $loan->guarantor_1_phone }}</p>
                        </div>
                        @if($loan->guarantor_2_name)
                        <div class="p-4 bg-red-50 rounded-xl border-2 border-red-200">
                            <p class="text-xs text-red-600 font-bold mb-2">Guarantor 2</p>
                            <p class="font-bold text-gray-900 mb-1">{{ $loan->guarantor_2_name }}</p>
                            <p class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-phone text-red-600"></i>{{ $loan->guarantor_2_phone }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Organizational Settings at Application Time -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-gray-700 to-gray-900 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-cog"></i>
                        Organizational Rules (At Application Time)
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-xs text-blue-600 font-bold">Interest Rate</p>
                            <p class="text-sm font-bold text-gray-900">{{ $loan->interest_rate ?? 'N/A' }}%</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                            <p class="text-xs text-green-600 font-bold">Min Amount</p>
                            <p class="text-sm font-bold text-gray-900">UGX {{ number_format($loan->settings_min_loan_amount ?? 0) }}</p>
                        </div>
                        <div class="p-3 bg-teal-50 rounded-lg border border-teal-200">
                            <p class="text-xs text-teal-600 font-bold">Max Amount</p>
                            <p class="text-sm font-bold text-gray-900">UGX {{ number_format($loan->settings_max_loan_amount ?? 0) }}</p>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                            <p class="text-xs text-purple-600 font-bold">Processing Fee</p>
                            <p class="text-sm font-bold text-gray-900">{{ $loan->processing_fee ?? 'N/A' }}%</p>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-lg border border-orange-200">
                            <p class="text-xs text-orange-600 font-bold">Max Loan/Savings</p>
                            <p class="text-sm font-bold text-gray-900">{{ $loan->settings_max_loan_to_savings_ratio ?? 'N/A' }}%</p>
                        </div>
                        <div class="p-3 bg-pink-50 rounded-lg border border-pink-200">
                            <p class="text-xs text-pink-600 font-bold">Auto Approve</p>
                            <p class="text-sm font-bold text-gray-900">UGX {{ number_format($loan->settings_auto_approve_amount ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons (if pending) -->
            @if($loan->status == 'pending')
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-tasks"></i>
                        Actions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <form action="{{ route('admin.loans.approve', $loan->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                                <i class="fas fa-check mr-2"></i>Approve Loan
                            </button>
                        </form>
                        <form action="{{ route('admin.loans.reject', $loan->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105" onclick="return confirm('Are you sure you want to reject this loan?')">
                                <i class="fas fa-times mr-2"></i>Reject Loan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

