@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-teal-50 to-blue-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.loan-applications.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left text-green-600"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 bg-clip-text text-transparent">Application Details</h2>
                <p class="text-gray-600 text-sm">{{ $application->application_id }}</p>
            </div>
        </div>
        <a href="{{ route('admin.loan-applications.edit', $application->id) }}" class="px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-2 border-green-400 text-green-700 px-6 py-4 rounded-xl mb-6 shadow-lg">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
        <!-- Left Column - Member Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 p-6 text-center">
                    @if($application->member && $application->member->profile_picture_url)
                        <img src="{{ $application->member->profile_picture_url }}" class="w-24 h-24 rounded-full mx-auto object-cover ring-4 ring-white shadow-xl" alt="">
                    @else
                        <div class="w-24 h-24 rounded-full bg-white mx-auto flex items-center justify-center ring-4 ring-white shadow-xl">
                            <span class="text-green-600 font-bold text-3xl">{{ substr($application->member->full_name ?? 'N', 0, 1) }}</span>
                        </div>
                    @endif
                    <h3 class="text-white text-xl font-bold mt-4">{{ $application->member->full_name ?? 'N/A' }}</h3>
                    <p class="text-white/80 text-sm">{{ $application->member->member_id ?? '' }}</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-xl">
                        <i class="fas fa-envelope text-green-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Email</p>
                            <p class="font-semibold text-sm">{{ $application->member->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-teal-50 rounded-xl">
                        <i class="fas fa-phone text-teal-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Phone</p>
                            <p class="font-semibold text-sm">{{ $application->member->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-xl">
                        <i class="fas fa-wallet text-blue-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Balance</p>
                            <p class="font-semibold text-sm">UGX {{ number_format($application->member->balance ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($application->reviewer)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mt-6">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-user-check"></i>
                        Reviewed By
                    </h3>
                </div>
                <div class="p-6">
                    <p class="font-bold text-gray-900">{{ $application->reviewer->name }}</p>
                    <p class="text-sm text-gray-600">{{ $application->reviewer->email }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Financial Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-money-bill-wave text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Loan Amount</p>
                    <p class="text-lg font-bold">{{ number_format($application->amount, 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-percentage text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Interest</p>
                    <p class="text-lg font-bold">{{ number_format($application->interest, 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-calculator text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Total Repayment</p>
                    <p class="text-lg font-bold">{{ number_format($application->total_repayment, 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-calendar-alt text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Monthly</p>
                    <p class="text-lg font-bold">{{ number_format($application->monthly_payment, 0) }}</p>
                </div>
            </div>

            <!-- Application Information Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Application Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Application ID</p>
                            <p class="font-bold text-gray-900">{{ $application->application_id }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Status</p>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'approved' => 'bg-green-100 text-green-800 border-green-200',
                                    'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                    'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
                                ];
                                $colorClass = $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $colorClass }}">
                                {{ ucfirst($application->status) }}
                            </span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Interest Rate</p>
                            <p class="font-bold text-gray-900">{{ $application->interest_rate }}%</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Repayment Period</p>
                            <p class="font-bold text-gray-900">{{ $application->repayment_months }} months</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Application Date</p>
                            <p class="font-bold text-gray-900">{{ $application->created_at->format('M d, Y') }}</p>
                        </div>
                        @if($application->reviewed_at)
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Reviewed Date</p>
                            <p class="font-bold text-gray-900">{{ $application->reviewed_at->format('M d, Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Purpose Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-teal-600 to-blue-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-clipboard"></i>
                        Loan Purpose
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed">{{ $application->purpose }}</p>
                </div>
            </div>

            @if($application->applicant_comment)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-comment"></i>
                        Applicant Comment
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed">{{ $application->applicant_comment }}</p>
                </div>
            </div>
            @endif

            @if($application->rejection_reason)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-red-200">
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        Rejection Reason
                    </h3>
                </div>
                <div class="p-6 bg-red-50">
                    <p class="text-red-700 leading-relaxed">{{ $application->rejection_reason }}</p>
                </div>
            </div>
            @endif

            @if($application->approval_comment)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-green-200">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        Approval Comment
                    </h3>
                </div>
                <div class="p-6 bg-green-50">
                    <p class="text-green-700 leading-relaxed">{{ $application->approval_comment }}</p>
                </div>
            </div>
            @endif

            @if($application->status == 'pending')
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-tasks"></i>
                        Actions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button onclick="document.getElementById('approveModal').classList.remove('hidden')" class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                            <i class="fas fa-check-circle mr-2"></i>Approve Application
                        </button>
                        <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                            <i class="fas fa-times-circle mr-2"></i>Reject Application
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <h3 class="text-2xl font-bold mb-6 bg-gradient-to-r from-green-600 to-teal-600 bg-clip-text text-transparent">Approve Application</h3>
        <form action="{{ route('admin.loan-applications.approve', $application->id) }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-comment text-green-600"></i>
                    Approval Comment (Optional)
                </label>
                <textarea name="approval_comment" rows="4" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm" placeholder="Add any comments or conditions..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('approveModal').classList.add('hidden')" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold">Cancel</button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-xl hover:shadow-xl transition-all font-bold">Approve</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <h3 class="text-2xl font-bold mb-6 bg-gradient-to-r from-red-600 to-pink-600 bg-clip-text text-transparent">Reject Application</h3>
        <form action="{{ route('admin.loan-applications.reject', $application->id) }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                    Rejection Reason *
                </label>
                <textarea name="rejection_reason" rows="4" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold">Cancel</button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition-all font-bold">Reject</button>
            </div>
        </form>
    </div>
</div>
@endsection

