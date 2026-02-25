@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-teal-50 to-blue-50 p-3 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('cashier.deposits.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-green-600"></i>
        </a>
        <div class="flex-1">
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 bg-clip-text text-transparent">New Deposit</h2>
            <p class="text-gray-600 text-sm">Process a new deposit transaction</p>
        </div>
    </div>

    <form action="{{ route('cashier.deposits.store') }}" method="POST" class="max-w-4xl mx-auto">
        @csrf

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center shadow-2xl">
                        <i class="fas fa-arrow-down text-green-600 text-6xl"></i>
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold">Deposit Transaction</h3>
                <p class="text-white/80 text-sm">Fill in the deposit details below</p>
            </div>

            <div class="p-6 md:p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-user text-green-600"></i>
                            Member *
                        </label>
                        <select name="member_id" id="member_id" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm appearance-none bg-white" onchange="loadMemberInfo()">
                            <option value="">Select Member</option>
                            @foreach($members as $member)
                                <option value="{{ $member->member_id }}" 
                                        data-savings="{{ $member->savings }}" 
                                        data-balance="{{ $member->balance }}"
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
                            Current Savings
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
                            Deposit Amount *
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-700 font-bold text-sm">UGX</span>
                            <input type="text" name="amount_display" id="amount_display" value="{{ old('amount') }}" required oninput="formatAmount(this);"
                                   class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                        </div>
                        <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                        @error('amount')
                            <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-credit-card text-indigo-600"></i>
                            Payment Method *
                        </label>
                        <select name="payment_method" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm appearance-none bg-white">
                            <option value="">Select Method</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                        @error('payment_method')
                            <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-hashtag text-purple-600"></i>
                            Reference Number
                        </label>
                        <input type="text" name="reference" value="{{ old('reference') }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm" placeholder="Transaction reference">
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-tag text-orange-600"></i>
                            Category
                        </label>
                        <select name="category" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm appearance-none bg-white">
                            <option value="">Select Category</option>
                            <option value="savings" {{ old('category') == 'savings' ? 'selected' : '' }}>Savings</option>
                            <option value="shares" {{ old('category') == 'shares' ? 'selected' : '' }}>Shares</option>
                            <option value="loan_repayment" {{ old('category') == 'loan_repayment' ? 'selected' : '' }}>Loan Repayment</option>
                            <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="space-y-2" id="new_balance_info" style="display:none;">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-wallet text-blue-600"></i>
                            New Balance
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-900 font-bold text-sm">UGX</span>
                            <input type="text" id="new_balance" readonly
                                   class="w-full pl-16 pr-4 py-3 border-2 border-blue-200 rounded-xl bg-blue-50 text-blue-900 font-bold text-sm">
                        </div>
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-comment text-pink-600"></i>
                            Description
                        </label>
                        <textarea name="description" rows="3" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm" placeholder="Add any notes about this deposit...">{{ old('description') }}</textarea>
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-sticky-note text-gray-600"></i>
                            Additional Notes
                        </label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all text-sm" placeholder="Internal notes...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('cashier.deposits.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-check mr-2"></i>Process Deposit
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

function formatAmount(input) {
    let value = input.value.replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
        document.getElementById('amount').value = value;
        let num = parseFloat(value);
        input.value = num.toLocaleString('en-US');
        calculateNewBalance();
    } else {
        document.getElementById('amount').value = '';
    }
}

function loadMemberInfo() {
    const select = document.getElementById('member_id');
    const option = select.options[select.selectedIndex];
    
    if (option.value) {
        memberSavings = parseFloat(option.dataset.savings) || 0;
        document.getElementById('member_savings').value = formatNumber(memberSavings);
        document.getElementById('member_info').style.display = 'block';
        calculateNewBalance();
    } else {
        document.getElementById('member_info').style.display = 'none';
        document.getElementById('new_balance_info').style.display = 'none';
    }
}

function calculateNewBalance() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    
    if (amount > 0 && memberSavings >= 0) {
        const newBalance = memberSavings + amount;
        document.getElementById('new_balance').value = formatNumber(newBalance);
        document.getElementById('new_balance_info').style.display = 'block';
    } else {
        document.getElementById('new_balance_info').style.display = 'none';
    }
}
</script>
@endsection
