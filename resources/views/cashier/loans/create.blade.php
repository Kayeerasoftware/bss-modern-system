@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-teal-50 to-blue-50 p-3 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('cashier.loans.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-green-600"></i>
        </a>
        <div class="flex-1">
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 bg-clip-text text-transparent">Add New Loan</h2>
            <p class="text-gray-600 text-sm">Complete the form to create a new loan application</p>
        </div>
    </div>

    <form action="{{ route('cashier.loans.store') }}" method="POST" class="max-w-5xl mx-auto" id="loanForm">
        @csrf

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center shadow-2xl">
                        <i class="fas fa-hand-holding-usd text-green-600 text-6xl"></i>
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold">Loan Application</h3>
                <p class="text-white/80 text-sm">Fill in the loan details below</p>
            </div>

            <div class="p-6 md:p-8 space-y-8">
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-green-600 to-teal-600">
                        <div class="bg-gradient-to-r from-green-600 to-teal-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-file-invoice-dollar text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Loan Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-id-card text-indigo-600"></i>
                                Loan ID
                            </label>
                            <input type="text" value="{{ 'LOAN' . str_pad((\App\Models\Loan::withTrashed()->max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT) }}" readonly
                                   class="w-full px-4 py-3 border-2 border-indigo-200 rounded-xl bg-indigo-50 text-indigo-900 font-bold text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user text-green-600"></i>
                                Member *
                            </label>
                            <select name="member_id" id="member_id" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm appearance-none bg-white" onchange="loadMemberData()">
                                <option value="">Select Member</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->member_id }}" 
                                            data-savings="{{ $member->savings }}" 
                                            data-loan="{{ $member->loan }}" 
                                            data-balance="{{ $member->balance }}"
                                            data-photo="{{ $member->profile_picture ? $member->profile_picture_url : '' }}"
                                            {{ old('member_id') == $member->member_id ? 'selected' : '' }}>
                                        {{ $member->full_name }} ({{ $member->member_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2" id="member_info" style="display:none;">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-piggy-bank text-yellow-600"></i>
                                Member Savings
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-yellow-900 font-bold text-sm">UGX</span>
                                <input type="text" id="member_savings" readonly
                                       class="w-full pl-16 pr-4 py-3 border-2 border-yellow-200 rounded-xl bg-yellow-50 text-yellow-900 font-bold text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-money-bill-wave text-teal-600"></i>
                                Loan Amount *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-700 font-bold text-sm">UGX</span>
                                <input type="text" name="amount_display" id="amount_display" value="{{ old('amount') }}" required oninput="formatAndCalculate(this);"
                                       class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                            </div>
                            <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                            @error('amount')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-percentage text-purple-600"></i>
                                Interest Rate (%) *
                            </label>
                            <input type="number" name="interest_rate" id="interest_rate" value="{{ old('interest_rate', $settings->default_interest_rate ?? 10) }}" readonly required step="0.01" min="{{ $settings->min_interest_rate ?? 0 }}" max="{{ $settings->max_interest_rate ?? 100 }}" oninput="calculateLoan()"
                                   class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl bg-purple-50 text-purple-900 font-bold focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                            <p class="text-xs text-gray-500">Range: {{ $settings->min_interest_rate ?? 0 }}% - {{ $settings->max_interest_rate ?? 100 }}%</p>
                            @error('interest_rate')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calendar-alt text-blue-600"></i>
                                Repayment Months *
                            </label>
                            <select name="repayment_months" id="repayment_months" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm appearance-none bg-white" onchange="calculateLoan()">
                                <option value="">Select Duration</option>
                                @for($i = $settings->min_repayment_months ?? 3; $i <= ($settings->max_repayment_months ?? 60); $i += 3)
                                    <option value="{{ $i }}" {{ old('repayment_months') == $i ? 'selected' : '' }}>{{ $i }} Months{{ $i == 12 ? ' (1 Year)' : ($i == 24 ? ' (2 Years)' : ($i == 36 ? ' (3 Years)' : ($i == 48 ? ' (4 Years)' : ($i == 60 ? ' (5 Years)' : '')))) }}</option>
                                @endfor
                            </select>
                            @error('repayment_months')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-clipboard text-orange-600"></i>
                                Purpose *
                            </label>
                            <textarea name="purpose" rows="4" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-comment text-pink-600"></i>
                                Applicant Comment (Optional)
                            </label>
                            <textarea name="applicant_comment" rows="2" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm" placeholder="Additional notes from applicant...">{{ old('applicant_comment') }}</textarea>
                        </div>
                    </div>
                </div>

                @if($settings->require_guarantors)
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-purple-600 to-pink-600">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-users text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Guarantor Information</h3>
                        <span class="ml-auto text-xs bg-purple-100 text-purple-800 px-3 py-1 rounded-full font-bold">{{ $settings->guarantors_required }} Required</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user text-purple-600"></i>
                                Guarantor 1 Name *
                            </label>
                            <input type="text" name="guarantor_1_name" value="{{ old('guarantor_1_name') }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-phone text-purple-600"></i>
                                Guarantor 1 Phone *
                            </label>
                            <input type="text" name="guarantor_1_phone" value="{{ old('guarantor_1_phone') }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                        </div>

                        @if($settings->guarantors_required >= 2)
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user text-pink-600"></i>
                                Guarantor 2 Name *
                            </label>
                            <input type="text" name="guarantor_2_name" value="{{ old('guarantor_2_name') }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-phone text-pink-600"></i>
                                Guarantor 2 Phone *
                            </label>
                            <input type="text" name="guarantor_2_phone" value="{{ old('guarantor_2_phone') }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm">
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <div id="calculations" class="hidden">
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-blue-600 to-indigo-600">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-calculator text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Financial Calculations</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-percentage text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Total Interest</p>
                            <p class="text-2xl font-bold">UGX <span id="display_interest">0.00</span></p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-money-check-alt text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Total Repayment</p>
                            <p class="text-2xl font-bold">UGX <span id="display_total">0.00</span></p>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-calendar-check text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Monthly Payment</p>
                            <p class="text-2xl font-bold">UGX <span id="display_monthly">0.00</span></p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-chart-line text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Loan to Savings Ratio</p>
                            <p class="text-2xl font-bold" id="display_ratio">0%</p>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg" id="warning_box" style="display:none;">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            <p class="text-sm text-yellow-800 font-semibold" id="warning_text"></p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('cashier.loans.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-check mr-2"></i>Create Loan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let memberSavings = 0;

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

function loadMemberData() {
    const select = document.getElementById('member_id');
    const option = select.options[select.selectedIndex];
    
    if (option.value) {
        memberSavings = parseFloat(option.dataset.savings) || 0;
        document.getElementById('member_savings').value = formatNumber(memberSavings);
        document.getElementById('member_info').style.display = 'block';
        calculateLoan();
    } else {
        document.getElementById('member_info').style.display = 'none';
        document.getElementById('calculations').classList.add('hidden');
    }
}

function calculateLoan() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const rate = parseFloat(document.getElementById('interest_rate').value) || 0;
    const monthsSelect = document.getElementById('repayment_months');
    const months = parseInt(monthsSelect.value) || 0;
    const maxRatio = {{ $settings->max_loan_to_savings_ratio ?? 300 }};
    
    if (amount > 0 && rate >= 0 && months > 0) {
        const interest = amount * (rate / 100) * (months / 12);
        const total = amount + interest;
        const monthly = total / months;
        const ratio = memberSavings > 0 ? (amount / memberSavings * 100) : 0;
        
        document.getElementById('display_interest').textContent = formatNumber(interest);
        document.getElementById('display_total').textContent = formatNumber(total);
        document.getElementById('display_monthly').textContent = formatNumber(monthly);
        document.getElementById('display_ratio').textContent = ratio.toFixed(1) + '%';
        document.getElementById('calculations').classList.remove('hidden');
        
        const warningBox = document.getElementById('warning_box');
        const warningText = document.getElementById('warning_text');
        
        if (ratio > maxRatio) {
            warningText.textContent = `Warning: Loan amount exceeds maximum loan-to-savings ratio of ${maxRatio}%!`;
            warningBox.style.display = 'block';
        } else if (amount > memberSavings * 2) {
            warningText.textContent = 'Caution: Loan amount is more than 2x member savings.';
            warningBox.style.display = 'block';
        } else {
            warningBox.style.display = 'none';
        }
    } else {
        document.getElementById('calculations').classList.add('hidden');
    }
}
</script>
@endsection

