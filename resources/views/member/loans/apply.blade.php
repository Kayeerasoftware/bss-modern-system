@extends('layouts.member')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('shareholder.loans') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-purple-600"></i>
        </a>
        <div class="flex-1">
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">Apply for Loan</h2>
            <p class="text-gray-600 text-sm">Complete the form to submit your loan application</p>
        </div>
    </div>

    <form action="{{ route('shareholder.loans.store') }}" method="POST" class="max-w-5xl mx-auto" id="loanForm">
        @csrf

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <!-- Loan Icon Section -->
            <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center shadow-2xl">
                        <i class="fas fa-hand-holding-usd text-purple-600 text-6xl"></i>
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold">Loan Application</h3>
                <p class="text-white/80 text-sm">Fill in the loan details below</p>
            </div>

            <!-- Form Content -->
            <div class="p-6 md:p-8 space-y-8">
                <!-- Loan Information -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-purple-600 to-pink-600">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-file-invoice-dollar text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Loan Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-money-bill-wave text-purple-600"></i>
                                Loan Amount *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-700 font-bold text-sm">UGX</span>
                                <input type="text" name="amount_display" id="amount_display" required oninput="formatAndCalculate(this);" class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm" placeholder="Enter amount">
                            </div>
                            <input type="hidden" name="amount" id="amount">
                            @error('amount')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calendar-alt text-pink-600"></i>
                                Duration (months) *
                            </label>
                            <select name="duration" id="duration" required onchange="calculateLoan()" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Duration</option>
                                <option value="3">3 Months</option>
                                <option value="6">6 Months</option>
                                <option value="12">12 Months (1 Year)</option>
                                <option value="18">18 Months</option>
                                <option value="24">24 Months (2 Years)</option>
                                <option value="36">36 Months (3 Years)</option>
                            </select>
                            @error('duration')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-list text-indigo-600"></i>
                                Loan Type *
                            </label>
                            <select name="loan_type" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Loan Type</option>
                                <option value="personal">Personal Loan</option>
                                <option value="business">Business Loan</option>
                                <option value="emergency">Emergency Loan</option>
                                <option value="education">Education Loan</option>
                                <option value="medical">Medical Loan</option>
                                <option value="agriculture">Agriculture Loan</option>
                            </select>
                            @error('loan_type')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-briefcase text-orange-600"></i>
                                Employment Status *
                            </label>
                            <select name="employment_status" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Employment Status</option>
                                <option value="employed">Employed</option>
                                <option value="self_employed">Self Employed</option>
                                <option value="business_owner">Business Owner</option>
                                <option value="unemployed">Unemployed</option>
                                <option value="retired">Retired</option>
                                <option value="student">Student</option>
                            </select>
                            @error('employment_status')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-money-check text-green-600"></i>
                                Monthly Income
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-700 font-bold text-sm">UGX</span>
                                <input type="text" name="monthly_income" class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm" placeholder="Enter monthly income">
                            </div>
                            @error('monthly_income')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-building text-blue-600"></i>
                                Employer/Business Name
                            </label>
                            <input type="text" name="employer_name" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm" placeholder="Enter employer or business name">
                            @error('employer_name')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-clipboard text-indigo-600"></i>
                                Purpose *
                            </label>
                            <textarea name="purpose" rows="4" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm" placeholder="Describe the purpose of this loan"></textarea>
                            @error('purpose')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user text-purple-600"></i>
                                Emergency Contact Name
                            </label>
                            <input type="text" name="emergency_contact_name" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm" placeholder="Enter emergency contact name">
                            @error('emergency_contact_name')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-phone text-pink-600"></i>
                                Emergency Contact Phone
                            </label>
                            <input type="tel" name="emergency_contact_phone" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm" placeholder="Enter emergency contact phone">
                            @error('emergency_contact_phone')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Financial Calculations -->
                <div id="calculations" class="hidden">
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-blue-600 to-indigo-600">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-calculator text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Loan Calculations</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-percentage text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Interest Rate</p>
                            <p class="text-2xl font-bold"><span id="display_rate">10</span>%</p>
                        </div>
                        <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-money-check-alt text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Total Interest</p>
                            <p class="text-2xl font-bold">UGX <span id="display_interest">0</span></p>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-calendar-check text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Monthly Payment</p>
                            <p class="text-2xl font-bold">UGX <span id="display_monthly">0</span></p>
                        </div>
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-chart-line text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Total Repayment</p>
                            <p class="text-2xl font-bold">UGX <span id="display_total">0</span></p>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-yellow-600 to-orange-600">
                        <div class="bg-gradient-to-r from-yellow-600 to-orange-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-file-contract text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Terms and Conditions</h3>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-6 border border-yellow-200">
                        <div class="space-y-3 text-sm text-gray-700">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                <p>I understand that this is a loan application and approval is subject to review.</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                <p>I agree to repay the loan according to the agreed terms and schedule.</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                <p>I understand that late payments may incur additional fees and penalties.</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                <p>All information provided in this application is accurate and complete.</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="terms" required class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <label for="terms" class="text-sm font-semibold text-gray-700">I agree to the terms and conditions above *</label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('shareholder.loans') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="button" onclick="previewLoan()" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-eye mr-2"></i>Preview Application
                    </button>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Application
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Preview Section -->
<div id="previewSection" class="min-h-screen bg-white p-6 mt-6" style="display:none;">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="text-center border-b-4 border-purple-600 pb-6 mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-24 h-24 bg-gradient-to-br from-purple-600 to-pink-600 rounded-full flex items-center justify-center shadow-xl">
                    <i class="fas fa-building text-white text-4xl"></i>
                </div>
            </div>
            <h1 class="text-4xl font-black text-purple-600 mb-2">BSS INVESTMENT GROUP</h1>
            <p class="text-gray-600 font-semibold">Business Support System</p>
            <p class="text-sm text-gray-500">Kampala, Uganda | info@bss.com | +256 XXX XXX XXX</p>
            <p class="text-2xl font-bold text-purple-600 mt-4">LOAN APPLICATION FORM</p>
        </div>

        <div class="space-y-6">
            <!-- Applicant Information -->
            <div class="border-2 border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 font-bold text-lg">APPLICANT INFORMATION</div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex justify-between border-b pb-2">
                            <span class="font-bold">Member ID:</span>
                            <span>{{ auth()->user()->member->member_id ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="font-bold">Full Name:</span>
                            <span>{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="font-bold">Email:</span>
                            <span>{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="font-bold">Application Date:</span>
                            <span id="preview_date"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loan Details -->
            <div class="border-2 border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 font-bold text-lg">LOAN DETAILS</div>
                <div class="p-6 space-y-3">
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Loan Amount:</span>
                        <span class="text-purple-600 font-bold text-xl" id="preview_amount"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Interest Rate:</span>
                        <span id="preview_interest_rate">10%</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Duration:</span>
                        <span id="preview_months"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Total Interest:</span>
                        <span id="preview_total_interest"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Total Repayment:</span>
                        <span class="text-purple-600 font-bold text-xl" id="preview_total_repayment"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Monthly Payment:</span>
                        <span class="font-bold text-lg" id="preview_monthly_payment"></span>
                    </div>
                </div>
            </div>

            <!-- Purpose -->
            <div class="border-2 border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 font-bold text-lg">LOAN PURPOSE</div>
                <div class="p-6 bg-gray-50">
                    <p id="preview_purpose"></p>
                </div>
            </div>

            <!-- Print Button -->
            <div class="text-center mt-8 no-print">
                <button onclick="window.print()" class="px-8 py-4 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold text-lg">
                    <i class="fas fa-print mr-2"></i>Print Application
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function formatNumber(num) {
    return num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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
    const rate = 10; // Fixed 10% interest rate
    const months = parseInt(document.getElementById('duration').value) || 0;
    
    if (amount > 0 && months > 0) {
        const interest = amount * (rate / 100) * (months / 12);
        const total = amount + interest;
        const monthly = total / months;
        
        document.getElementById('display_interest').textContent = formatNumber(interest);
        document.getElementById('display_total').textContent = formatNumber(total);
        document.getElementById('display_monthly').textContent = formatNumber(monthly);
        document.getElementById('calculations').classList.remove('hidden');
    } else {
        document.getElementById('calculations').classList.add('hidden');
    }
}

function previewLoan() {
    const amount = document.getElementById('amount').value;
    const amountDisplay = document.getElementById('amount_display').value;
    const months = document.getElementById('duration').value;
    const purpose = document.querySelector('textarea[name="purpose"]').value;
    
    if (!amount || !months || !purpose) {
        alert('Please fill in all required fields');
        return;
    }
    
    const amountNum = parseFloat(amount);
    const rate = 10;
    const monthsNum = parseInt(months);
    const interest = amountNum * (rate / 100) * (monthsNum / 12);
    const total = amountNum + interest;
    const monthly = total / monthsNum;
    
    document.getElementById('preview_date').textContent = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('preview_amount').textContent = 'UGX ' + amountDisplay;
    document.getElementById('preview_months').textContent = months + ' Months';
    document.getElementById('preview_total_interest').textContent = 'UGX ' + formatNumber(interest);
    document.getElementById('preview_total_repayment').textContent = 'UGX ' + formatNumber(total);
    document.getElementById('preview_monthly_payment').textContent = 'UGX ' + formatNumber(monthly);
    document.getElementById('preview_purpose').textContent = purpose;
    
    document.getElementById('previewSection').style.display = 'block';
    document.getElementById('previewSection').scrollIntoView({ behavior: 'smooth' });
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #previewSection, #previewSection * {
        visibility: visible;
    }
    #previewSection {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
    @page {
        margin: 1cm;
    }
}
</style>
@endsection

