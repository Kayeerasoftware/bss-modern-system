@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-teal-50 to-blue-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.loans.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-green-600"></i>
        </a>
        <div class="flex-1">
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 bg-clip-text text-transparent">Add New Loan</h2>
            <p class="text-gray-600 text-sm">Complete the form to create a new loan application</p>
        </div>
    </div>

    <form action="{{ route('admin.loans.store') }}" method="POST" class="max-w-5xl mx-auto" id="loanForm">
        @csrf

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <!-- Loan Icon Section -->
            <div class="bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center shadow-2xl">
                        <i class="fas fa-hand-holding-usd text-green-600 text-6xl"></i>
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold">Loan Application</h3>
                <p class="text-white/80 text-sm">Fill in the loan details below</p>
            </div>

            <!-- Form Content -->
            <div class="p-6 md:p-8 space-y-8">
                <!-- Loan Information -->
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
                                            data-photo="{{ $member->profile_picture ? asset('storage/' . $member->profile_picture) : '' }}"
                                            data-photo="{{ $member->profile_picture ? asset('storage/' . $member->profile_picture) : '' }}"
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
                            @error('applicant_comment')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hidden Settings Fields -->
                        <input type="hidden" name="settings_min_interest_rate" value="{{ $settings->min_interest_rate ?? 5 }}">
                        <input type="hidden" name="settings_max_interest_rate" value="{{ $settings->max_interest_rate ?? 30 }}">
                        <input type="hidden" name="settings_min_loan_amount" value="{{ $settings->min_loan_amount ?? 10000 }}">
                        <input type="hidden" name="settings_max_loan_amount" value="{{ $settings->max_loan_amount ?? 10000000 }}">
                        <input type="hidden" name="settings_max_loan_to_savings_ratio" value="{{ $settings->max_loan_to_savings_ratio ?? 300 }}">
                        <input type="hidden" name="settings_min_repayment_months" value="{{ $settings->min_repayment_months ?? 3 }}">
                        <input type="hidden" name="settings_max_repayment_months" value="{{ $settings->max_repayment_months ?? 60 }}">
                        <input type="hidden" name="settings_default_repayment_months" value="{{ $settings->default_repayment_months ?? 12 }}">
                        <input type="hidden" name="settings_processing_fee_percentage" value="{{ $settings->processing_fee_percentage ?? 2 }}">
                        <input type="hidden" name="settings_late_payment_penalty" value="{{ $settings->late_payment_penalty ?? 5 }}">
                        <input type="hidden" name="settings_grace_period_days" value="{{ $settings->grace_period_days ?? 7 }}">
                        <input type="hidden" name="settings_auto_approve_amount" value="{{ $settings->auto_approve_amount ?? 0 }}">
                        <input type="hidden" name="settings_require_guarantors" value="{{ $settings->require_guarantors ?? 0 }}">
                        <input type="hidden" name="settings_guarantors_required" value="{{ $settings->guarantors_required ?? 2 }}">
                        <input type="hidden" name="settings_email_notifications" value="{{ $settings->email_notifications ?? 1 }}">
                        <input type="hidden" name="settings_sms_notifications" value="{{ $settings->sms_notifications ?? 1 }}">
                        <input type="hidden" name="settings_payment_reminder_days" value="{{ $settings->payment_reminder_days ?? 3 }}">
                    </div>
                </div>

                <!-- Organisational Current Loan Rules, Terms and Conditions (View Only) -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-yellow-600 to-orange-600">
                        <div class="bg-gradient-to-r from-yellow-600 to-orange-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-file-contract text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Organisational Current Loan Rules, Terms and Conditions</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-percent text-blue-600"></i>
                                Min Interest Rate (%)
                            </label>
                            <input type="text" value="{{ $settings->min_interest_rate ?? 5 }}" readonly class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl bg-blue-50 text-blue-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-percent text-red-600"></i>
                                Max Interest Rate (%)
                            </label>
                            <input type="text" value="{{ $settings->max_interest_rate ?? 30 }}" readonly class="w-full px-4 py-3 border-2 border-red-200 rounded-xl bg-red-50 text-red-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-money-bill text-green-600"></i>
                                Min Loan Amount
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-green-900 font-bold text-sm">UGX</span>
                                <input type="text" value="{{ number_format($settings->min_loan_amount ?? 10000) }}" readonly class="w-full pl-16 pr-4 py-3 border-2 border-green-200 rounded-xl bg-green-50 text-green-900 font-bold text-sm">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-money-bill text-red-600"></i>
                                Max Loan Amount
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-red-900 font-bold text-sm">UGX</span>
                                <input type="text" value="{{ number_format($settings->max_loan_amount ?? 10000000) }}" readonly class="w-full pl-16 pr-4 py-3 border-2 border-red-200 rounded-xl bg-red-50 text-red-900 font-bold text-sm">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-chart-line text-pink-600"></i>
                                Max Loan-to-Savings Ratio (%)
                            </label>
                            <input type="text" value="{{ $settings->max_loan_to_savings_ratio ?? 300 }}" readonly class="w-full px-4 py-3 border-2 border-pink-200 rounded-xl bg-pink-50 text-pink-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calendar text-purple-600"></i>
                                Min Repayment Months
                            </label>
                            <input type="text" value="{{ $settings->min_repayment_months ?? 3 }}" readonly class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl bg-purple-50 text-purple-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calendar text-indigo-600"></i>
                                Max Repayment Months
                            </label>
                            <input type="text" value="{{ $settings->max_repayment_months ?? 60 }}" readonly class="w-full px-4 py-3 border-2 border-indigo-200 rounded-xl bg-indigo-50 text-indigo-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calendar-check text-blue-600"></i>
                                Default Repayment Months
                            </label>
                            <input type="text" value="{{ $settings->default_repayment_months ?? 12 }}" readonly class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl bg-blue-50 text-blue-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-file-invoice text-orange-600"></i>
                                Processing Fee (%)
                            </label>
                            <input type="text" value="{{ $settings->processing_fee_percentage ?? 2 }}" readonly class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl bg-orange-50 text-orange-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                                Late Payment Penalty (%)
                            </label>
                            <input type="text" value="{{ $settings->late_payment_penalty ?? 5 }}" readonly class="w-full px-4 py-3 border-2 border-red-200 rounded-xl bg-red-50 text-red-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-clock text-yellow-600"></i>
                                Grace Period (Days)
                            </label>
                            <input type="text" value="{{ $settings->grace_period_days ?? 7 }}" readonly class="w-full px-4 py-3 border-2 border-yellow-200 rounded-xl bg-yellow-50 text-yellow-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-check-circle text-teal-600"></i>
                                Auto Approve Amount
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-teal-900 font-bold text-sm">UGX</span>
                                <input type="text" value="{{ ($settings->auto_approve_amount ?? 0) > 0 ? number_format($settings->auto_approve_amount) : 'Disabled' }}" readonly class="w-full pl-16 pr-4 py-3 border-2 border-teal-200 rounded-xl bg-teal-50 text-teal-900 font-bold text-sm">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-users text-purple-600"></i>
                                Require Guarantors
                            </label>
                            <input type="text" value="{{ ($settings->require_guarantors ?? false) ? 'Yes' : 'No' }}" readonly class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl bg-purple-50 text-purple-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-hashtag text-indigo-600"></i>
                                Guarantors Required
                            </label>
                            <input type="text" value="{{ $settings->guarantors_required ?? 2 }}" readonly class="w-full px-4 py-3 border-2 border-indigo-200 rounded-xl bg-indigo-50 text-indigo-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-envelope text-blue-600"></i>
                                Email Notifications
                            </label>
                            <input type="text" value="{{ ($settings->email_notifications ?? true) ? 'Enabled' : 'Disabled' }}" readonly class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl bg-blue-50 text-blue-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-sms text-green-600"></i>
                                SMS Notifications
                            </label>
                            <input type="text" value="{{ ($settings->sms_notifications ?? true) ? 'Enabled' : 'Disabled' }}" readonly class="w-full px-4 py-3 border-2 border-green-200 rounded-xl bg-green-50 text-green-900 font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-bell text-pink-600"></i>
                                Payment Reminder (Days)
                            </label>
                            <input type="text" value="{{ $settings->payment_reminder_days ?? 3 }}" readonly class="w-full px-4 py-3 border-2 border-pink-200 rounded-xl bg-pink-50 text-pink-900 font-bold text-sm">
                        </div>
                    </div>
                </div>

                <!-- Guarantor Information -->
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
                            @error('guarantor_1_name')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-phone text-purple-600"></i>
                                Guarantor 1 Phone *
                            </label>
                            <input type="text" name="guarantor_1_phone" value="{{ old('guarantor_1_phone') }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                            @error('guarantor_1_phone')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        @if($settings->guarantors_required >= 2)
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user text-pink-600"></i>
                                Guarantor 2 Name *
                            </label>
                            <input type="text" name="guarantor_2_name" value="{{ old('guarantor_2_name') }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm">
                            @error('guarantor_2_name')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-phone text-pink-600"></i>
                                Guarantor 2 Phone *
                            </label>
                            <input type="text" name="guarantor_2_phone" value="{{ old('guarantor_2_phone') }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm">
                            @error('guarantor_2_phone')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Financial Calculations -->
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
                    
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-file-invoice text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Processing Fee ({{ $settings->processing_fee_percentage ?? 2 }}%)</p>
                            <p class="text-2xl font-bold">UGX <span id="display_processing_fee">0.00</span></p>
                        </div>
                        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
                            <i class="fas fa-exclamation-circle text-2xl mb-2 opacity-80"></i>
                            <p class="text-xs opacity-80">Late Payment Penalty ({{ $settings->late_payment_penalty ?? 5 }}%)</p>
                            <p class="text-2xl font-bold">{{ $settings->grace_period_days ?? 7 }} Days Grace</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('admin.loans.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="button" onclick="previewLoan()" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-eye mr-2"></i>Preview Application
                    </button>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-check mr-2"></i>Create Loan
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
        <div class="text-center border-b-4 border-green-600 pb-6 mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-24 h-24 bg-gradient-to-br from-green-600 to-teal-600 rounded-full flex items-center justify-center shadow-xl">
                    <i class="fas fa-building text-white text-4xl"></i>
                </div>
            </div>
            <h1 class="text-4xl font-black text-green-600 mb-2">BSS INVESTMENT GROUP</h1>
            <p class="text-gray-600 font-semibold">Business Support System</p>
            <p class="text-sm text-gray-500">Kigali, Rwanda | info@bssinvestment.rw | +250 XXX XXX XXX</p>
            <p class="text-2xl font-bold text-green-600 mt-4">LOAN APPLICATION FORM</p>
        </div>

        <div class="space-y-6">
            <!-- Applicant Information with Photo -->
            <div class="border-2 border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-3 font-bold text-lg">APPLICANT INFORMATION</div>
                <div class="p-6">
                    <div class="flex gap-6 mb-4">
                        <div class="flex-shrink-0">
                            <div id="preview_member_photo" class="w-32 h-32 bg-gray-200 rounded-xl flex items-center justify-center overflow-hidden">
                                <i class="fas fa-user text-gray-400 text-4xl"></i>
                            </div>
                        </div>
                        <div class="flex-1 grid grid-cols-2 gap-4">
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-bold">Member ID:</span>
                                <span id="preview_member_id"></span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-bold">Full Name:</span>
                                <span id="preview_member_name"></span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-bold">Member Savings:</span>
                                <span id="preview_member_savings_display"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loan Information -->
            <div class="border-2 border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-3 font-bold text-lg">LOAN INFORMATION</div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Loan ID:</span>
                        <span id="preview_loan_id"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Application Date:</span>
                        <span id="preview_date"></span>
                    </div>
                </div>
            </div>

            <!-- Loan Details -->
            <div class="border-2 border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-3 font-bold text-lg">LOAN DETAILS</div>
                <div class="p-6 space-y-3">
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Loan Amount:</span>
                        <span class="text-green-600 font-bold text-xl" id="preview_amount"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Interest Rate:</span>
                        <span id="preview_interest_rate"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Repayment Period:</span>
                        <span id="preview_months"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Total Interest:</span>
                        <span id="preview_total_interest"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Processing Fee:</span>
                        <span id="preview_processing"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Total Repayment:</span>
                        <span class="text-green-600 font-bold text-xl" id="preview_total_repayment"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Monthly Payment:</span>
                        <span class="font-bold text-lg" id="preview_monthly_payment"></span>
                    </div>
                </div>
            </div>

            <!-- Purpose -->
            <div class="border-2 border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-3 font-bold text-lg">LOAN PURPOSE</div>
                <div class="p-6 bg-gray-50">
                    <p id="preview_purpose"></p>
                </div>
            </div>

            <!-- Applicant Comment -->
            <div id="preview_comment_section" class="border-2 border-gray-200 rounded-xl overflow-hidden" style="display:none;">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-3 font-bold text-lg">APPLICANT COMMENT</div>
                <div class="p-6 bg-yellow-50">
                    <p id="preview_comment"></p>
                </div>
            </div>

            <!-- Guarantors -->
            <div id="preview_guarantors_section" class="border-2 border-gray-200 rounded-xl overflow-hidden" style="display:none;">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-3 font-bold text-lg">GUARANTOR INFORMATION</div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Guarantor 1 Name:</span>
                        <span id="preview_guarantor1_name"></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-bold">Guarantor 1 Phone:</span>
                        <span id="preview_guarantor1_phone"></span>
                    </div>
                    <div id="preview_guarantor2_section" style="display:none;">
                        <div class="flex justify-between border-b pb-2">
                            <span class="font-bold">Guarantor 2 Name:</span>
                            <span id="preview_guarantor2_name"></span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="font-bold">Guarantor 2 Phone:</span>
                            <span id="preview_guarantor2_phone"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print Button -->
            <div class="text-center mt-8 no-print">
                <button onclick="window.print()" class="px-8 py-4 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold text-lg">
                    <i class="fas fa-print mr-2"></i>Print Loan Application
                </button>
            </div>
        </div>
    </div>
</div>

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

<script>
let memberSavings = 0;

function formatNumber(num) {
    return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function formatCurrency(input) {
    let value = input.value.replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
        let num = parseFloat(value);
        input.value = num.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2});
    }
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
        const loan = parseFloat(option.dataset.loan) || 0;
        const balance = parseFloat(option.dataset.balance) || 0;
        
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
    const processingFeeRate = {{ $settings->processing_fee_percentage ?? 2 }};
    const maxRatio = {{ $settings->max_loan_to_savings_ratio ?? 300 }};
    
    if (amount > 0 && rate >= 0 && months > 0) {
        const interest = amount * (rate / 100) * (months / 12);
        const processingFee = amount * (processingFeeRate / 100);
        const total = amount + interest;
        const monthly = total / months;
        const ratio = memberSavings > 0 ? (amount / memberSavings * 100) : 0;
        
        document.getElementById('display_interest').textContent = formatNumber(interest);
        document.getElementById('display_total').textContent = formatNumber(total);
        document.getElementById('display_monthly').textContent = formatNumber(monthly);
        document.getElementById('display_ratio').textContent = ratio.toFixed(1) + '%';
        document.getElementById('display_processing_fee').textContent = formatNumber(processingFee);
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

function previewLoan() {
    const memberSelect = document.getElementById('member_id');
    const memberOption = memberSelect.options[memberSelect.selectedIndex];
    const amount = document.getElementById('amount').value;
    const amountDisplay = document.getElementById('amount_display').value;
    const rate = document.getElementById('interest_rate').value;
    const monthsSelect = document.getElementById('repayment_months');
    const months = monthsSelect.value;
    const purpose = document.querySelector('textarea[name="purpose"]').value;
    const comment = document.querySelector('textarea[name="applicant_comment"]').value;
    
    if (!memberOption.value || !amount || !months || !purpose) {
        alert('Please fill in all required fields');
        return;
    }
    
    const amountNum = parseFloat(amount);
    const rateNum = parseFloat(rate);
    const monthsNum = parseInt(months);
    const interest = amountNum * (rateNum / 100) * (monthsNum / 12);
    const processingFee = amountNum * ({{ $settings->processing_fee_percentage ?? 2 }} / 100);
    const total = amountNum + interest;
    const monthly = total / monthsNum;
    
    document.getElementById('preview_loan_id').textContent = '{{ 'LOAN' . str_pad((\App\Models\Loan::withTrashed()->max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT) }}';
    document.getElementById('preview_date').textContent = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('preview_member_id').textContent = memberOption.value;
    document.getElementById('preview_member_name').textContent = memberOption.text.split('(')[0].trim();
    document.getElementById('preview_member_savings_display').textContent = 'UGX ' + formatNumber(memberSavings);
    
    // Member photo placeholder
    const photoDiv = document.getElementById('preview_member_photo');
    const photoUrl = memberOption.dataset.photo;
    if (photoUrl) {
        photoDiv.innerHTML = '<img src="' + photoUrl + '" class="w-full h-full object-cover" alt="Member Photo">';
    } else {
        photoDiv.innerHTML = '<div class="w-full h-full bg-gradient-to-br from-green-100 to-teal-100 flex items-center justify-center"><span class="text-green-600 font-bold text-5xl">' + memberOption.text.charAt(0) + '</span></div>';
    }
    
    document.getElementById('preview_amount').textContent = 'UGX ' + amountDisplay;
    document.getElementById('preview_interest_rate').textContent = rate + '%';
    document.getElementById('preview_months').textContent = months + ' Months';
    document.getElementById('preview_total_interest').textContent = 'UGX ' + formatNumber(interest);
    document.getElementById('preview_processing').textContent = 'UGX ' + formatNumber(processingFee);
    document.getElementById('preview_total_repayment').textContent = 'UGX ' + formatNumber(total);
    document.getElementById('preview_monthly_payment').textContent = 'UGX ' + formatNumber(monthly);
    document.getElementById('preview_purpose').textContent = purpose;
    
    if (comment) {
        document.getElementById('preview_comment').textContent = comment;
        document.getElementById('preview_comment_section').style.display = 'block';
    } else {
        document.getElementById('preview_comment_section').style.display = 'none';
    }
    
    const guarantor1Name = document.querySelector('input[name="guarantor_1_name"]');
    if (guarantor1Name && guarantor1Name.value) {
        document.getElementById('preview_guarantor1_name').textContent = guarantor1Name.value;
        document.getElementById('preview_guarantor1_phone').textContent = document.querySelector('input[name="guarantor_1_phone"]').value;
        document.getElementById('preview_guarantors_section').style.display = 'block';
        
        const guarantor2Name = document.querySelector('input[name="guarantor_2_name"]');
        if (guarantor2Name && guarantor2Name.value) {
            document.getElementById('preview_guarantor2_name').textContent = guarantor2Name.value;
            document.getElementById('preview_guarantor2_phone').textContent = document.querySelector('input[name="guarantor_2_phone"]').value;
            document.getElementById('preview_guarantor2_section').style.display = 'block';
        }
    } else {
        document.getElementById('preview_guarantors_section').style.display = 'none';
    }
    
    document.getElementById('previewSection').style.display = 'block';
    document.getElementById('previewSection').scrollIntoView({ behavior: 'smooth' });
}
</script>
@endsection
