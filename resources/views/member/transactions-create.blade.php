@extends('layouts.member')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 p-3 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('shareholder.transactions') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-purple-600"></i>
        </a>
        <div>
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 bg-clip-text text-transparent">New Transaction</h2>
            <p class="text-gray-600 text-sm">Create a new financial transaction</p>
        </div>
    </div>

    <form action="{{ route('shareholder.transactions.store') }}" method="POST" class="max-w-6xl mx-auto">
        @csrf
        <input type="hidden" name="member_id" value="{{ $member->member_id }}">

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center shadow-2xl">
                        <i class="fas fa-exchange-alt text-purple-600 text-6xl"></i>
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold">New Financial Transaction</h3>
                <p class="text-white/80 text-sm">{{ $member->full_name }} ({{ $member->member_id }})</p>
            </div>

            <div class="p-6 md:p-8 space-y-8">
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-info-circle text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Transaction Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-filter text-pink-600"></i>
                                Transaction Type *
                            </label>
                            <select name="type" id="type" required onchange="calculateTransaction()" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Type</option>
                                <option value="deposit">Deposit</option>
                                <option value="withdrawal">Withdrawal</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-tag text-indigo-600"></i>
                                Category *
                            </label>
                            <select name="category" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Category</option>
                                <option value="savings">Savings</option>
                                <option value="loan_repayment">Loan Repayment</option>
                                <option value="shares">Shares</option>
                                <option value="dividend">Dividend</option>
                                <option value="emergency">Emergency</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-money-bill-wave text-green-600"></i>
                                Amount *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="hidden" name="amount" id="amount">
                                <input type="text" id="amount_display" required placeholder="0" oninput="formatAmount(this)" class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-receipt text-orange-600"></i>
                                Transaction Fee
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="hidden" name="fee" id="fee" value="0">
                                <input type="text" id="fee_display" placeholder="0" oninput="formatFee(this)" class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-percentage text-red-600"></i>
                                Tax Amount
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="hidden" name="tax_amount" id="tax_amount" value="0">
                                <input type="text" id="tax_display" placeholder="0" oninput="formatTax(this)" class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-hand-holding-usd text-yellow-600"></i>
                                Commission
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="hidden" name="commission" id="commission" value="0">
                                <input type="text" id="commission_display" placeholder="0" oninput="formatCommission(this)" class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calculator text-indigo-600"></i>
                                Net Amount
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="text" id="net_amount_display" readonly class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-coins text-amber-600"></i>
                                Total Charges
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="text" id="total_charges_display" readonly class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-money-check text-emerald-600"></i>
                                Currency
                            </label>
                            <select name="currency" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-sm appearance-none bg-white">
                                <option value="UGX" selected>UGX - Ugandan Shilling</option>
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-credit-card text-pink-600"></i>
                                Payment Method *
                            </label>
                            <select name="payment_method" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="cheque">Cheque</option>
                                <option value="card">Card</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-broadcast-tower text-violet-600"></i>
                                Channel
                            </label>
                            <select name="channel" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Channel</option>
                                <option value="branch">Branch</option>
                                <option value="atm">ATM</option>
                                <option value="online">Online</option>
                                <option value="mobile_app">Mobile App</option>
                                <option value="ussd">USSD</option>
                                <option value="agent">Agent</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-flag text-rose-600"></i>
                                Priority
                            </label>
                            <select name="priority" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all text-sm appearance-none bg-white">
                                <option value="normal" selected>Normal</option>
                                <option value="low">Low</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-hashtag text-teal-600"></i>
                                Reference Number
                            </label>
                            <input type="text" name="reference" placeholder="TXN-REF-XXXX" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-file-invoice text-purple-600"></i>
                                Receipt Number
                            </label>
                            <input type="text" name="receipt_number" placeholder="RCP-XXXX" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-layer-group text-slate-600"></i>
                                Batch ID
                            </label>
                            <input type="text" name="batch_id" placeholder="BATCH-XXXX" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-map-marker-alt text-red-600"></i>
                                Location
                            </label>
                            <input type="text" name="location" placeholder="Branch/Location" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calendar text-blue-600"></i>
                                Transaction Date *
                            </label>
                            <input type="datetime-local" name="transaction_date" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-clock text-indigo-600"></i>
                                Scheduled Date
                            </label>
                            <input type="datetime-local" name="scheduled_at" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-bell text-yellow-600"></i>
                                Send Notification
                            </label>
                            <div class="flex items-center gap-3 px-4 py-3 border-2 border-gray-200 rounded-xl">
                                <input type="checkbox" name="notification_sent" id="notification_sent" value="1" class="w-5 h-5 text-yellow-600 rounded focus:ring-2 focus:ring-yellow-500">
                                <label for="notification_sent" class="text-sm text-gray-700 cursor-pointer">Send SMS/Email notification</label>
                            </div>
                        </div>

                        <div class="space-y-2 md:col-span-3">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-comment text-green-600"></i>
                                Description
                            </label>
                            <textarea name="description" rows="2" placeholder="Transaction description..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm"></textarea>
                        </div>

                        <div class="space-y-2 md:col-span-3">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-sticky-note text-yellow-600"></i>
                                Internal Notes
                            </label>
                            <textarea name="notes" rows="2" placeholder="Internal notes..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('shareholder.transactions') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-check mr-2"></i>Create Transaction
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function formatNumber(num) {
    return parseFloat(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function formatAmount(input) {
    let value = input.value.replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
        document.getElementById('amount').value = value;
        input.value = parseFloat(value).toLocaleString('en-US');
    } else {
        document.getElementById('amount').value = '';
    }
    calculateTransaction();
}

function formatFee(input) {
    let value = input.value.replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
        document.getElementById('fee').value = value;
        input.value = parseFloat(value).toLocaleString('en-US');
    } else {
        document.getElementById('fee').value = '0';
    }
    calculateTransaction();
}

function formatTax(input) {
    let value = input.value.replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
        document.getElementById('tax_amount').value = value;
        input.value = parseFloat(value).toLocaleString('en-US');
    } else {
        document.getElementById('tax_amount').value = '0';
    }
    calculateTransaction();
}

function formatCommission(input) {
    let value = input.value.replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
        document.getElementById('commission').value = value;
        input.value = parseFloat(value).toLocaleString('en-US');
    } else {
        document.getElementById('commission').value = '0';
    }
    calculateTransaction();
}

function calculateTransaction() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const fee = parseFloat(document.getElementById('fee').value) || 0;
    const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
    const commission = parseFloat(document.getElementById('commission').value) || 0;
    const type = document.getElementById('type').value;
    
    const totalCharges = fee + tax + commission;
    let netAmount = amount;
    
    if (type === 'withdrawal') {
        netAmount = amount - totalCharges;
    }
    
    document.getElementById('net_amount_display').value = formatNumber(netAmount);
    document.getElementById('total_charges_display').value = formatNumber(totalCharges);
}
</script>
@endsection

