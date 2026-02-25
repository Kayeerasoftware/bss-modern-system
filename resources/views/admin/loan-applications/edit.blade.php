@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-teal-50 to-blue-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.loan-applications.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-green-600"></i>
        </a>
        <div>
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 bg-clip-text text-transparent">Edit Application</h2>
            <p class="text-gray-600 text-sm">Update application details - ID: {{ $application->application_id }}</p>
        </div>
    </div>

    <form action="{{ route('admin.loan-applications.update', $application->id) }}" method="POST" class="max-w-5xl mx-auto">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center shadow-2xl">
                        <i class="fas fa-file-invoice text-green-600 text-6xl"></i>
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold">{{ $application->application_id }}</h3>
                <p class="text-white/80 text-sm">Update application information</p>
            </div>

            <!-- Form Content -->
            <div class="p-6 md:p-8 space-y-8">
                <!-- Application Information -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-green-600 to-teal-600">
                        <div class="bg-gradient-to-r from-green-600 to-teal-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-file-invoice-dollar text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Application Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user text-green-600"></i>
                                Member *
                            </label>
                            <select name="member_id" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Member</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->member_id }}" {{ old('member_id', $application->member_id) == $member->member_id ? 'selected' : '' }}>
                                        {{ $member->full_name }} ({{ $member->member_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-money-bill-wave text-teal-600"></i>
                                Loan Amount *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="hidden" name="amount" id="amount" value="{{ old('amount', $application->amount) }}">
                                <input type="text" id="amount_display" value="{{ number_format(old('amount', $application->amount)) }}" required oninput="formatAndCalculate(this)"
                                       class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                            </div>
                            @error('amount')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-percentage text-purple-600"></i>
                                Interest Rate (%) *
                            </label>
                            <input type="number" name="interest_rate" id="interest_rate" value="{{ old('interest_rate', $application->interest_rate) }}" step="0.01" required onchange="calculateLoan()"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                            @error('interest_rate')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calendar-alt text-blue-600"></i>
                                Repayment Months *
                            </label>
                            <input type="number" name="repayment_months" id="repayment_months" value="{{ old('repayment_months', $application->repayment_months) }}" min="1" required onchange="calculateLoan()"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                            @error('repayment_months')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-circle text-orange-600 text-[8px]"></i>
                                Status *
                            </label>
                            <select name="status" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm appearance-none bg-white">
                                <option value="pending" {{ old('status', $application->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status', $application->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ old('status', $application->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="cancelled" {{ old('status', $application->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-clipboard text-pink-600"></i>
                                Purpose *
                            </label>
                            <textarea name="purpose" rows="4" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm">{{ old('purpose', $application->purpose) }}</textarea>
                            @error('purpose')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-comment text-indigo-600"></i>
                                Applicant Comment
                            </label>
                            <textarea name="applicant_comment" rows="3" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm" placeholder="Additional notes from applicant...">{{ old('applicant_comment', $application->applicant_comment) }}</textarea>
                            @error('applicant_comment')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        @if($application->status == 'approved' || $application->status == 'rejected')
                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-comment-dots text-gray-600"></i>
                                Review Comment
                            </label>
                            <textarea readonly class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm" rows="2">{{ $application->approval_comment ?? $application->rejection_reason }}</textarea>
                            <p class="text-xs text-gray-500 flex items-center gap-1"><i class="fas fa-info-circle"></i>Review comments cannot be edited here. Use approve/reject actions to update.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Financial Calculations -->
                <div id="calculations">
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-blue-600 to-indigo-600">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-calculator text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Financial Calculations</h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-percentage text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Total Interest</p>
                            <p class="text-lg font-bold">UGX <span id="display_interest">0.00</span></p>
                        </div>
                        <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-calculator text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Total Repayment</p>
                            <p class="text-lg font-bold">UGX <span id="display_total">0.00</span></p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-calendar-check text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Monthly Payment</p>
                            <p class="text-lg font-bold">UGX <span id="display_monthly">0.00</span></p>
                        </div>
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-calendar-alt text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Repayment Period</p>
                            <p class="text-lg font-bold"><span id="display_months">0</span> Months</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('admin.loan-applications.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Update Application
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function formatNumber(num) {
    return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function formatAndCalculate(input) {
    let value = input.value.replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
        document.getElementById('amount').value = value;
        let num = parseFloat(value);
        input.value = num.toLocaleString('en-US');
    } else {
        document.getElementById('amount').value = '';
    }
    calculateLoan();
}

function calculateLoan() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const rate = parseFloat(document.getElementById('interest_rate').value) || 0;
    const months = parseInt(document.getElementById('repayment_months').value) || 0;
    
    if (amount > 0 && rate >= 0 && months > 0) {
        const interest = amount * (rate / 100) * (months / 12);
        const total = amount + interest;
        const monthly = total / months;
        
        document.getElementById('display_interest').textContent = formatNumber(interest);
        document.getElementById('display_total').textContent = formatNumber(total);
        document.getElementById('display_monthly').textContent = formatNumber(monthly);
        document.getElementById('display_months').textContent = months;
    }
}

// Calculate on page load
window.addEventListener('DOMContentLoaded', calculateLoan);
</script>
@endsection
