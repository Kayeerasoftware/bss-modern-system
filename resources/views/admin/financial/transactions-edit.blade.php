@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-cyan-50 via-blue-50 to-indigo-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.financial.transactions') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-cyan-600"></i>
        </a>
        <div>
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 bg-clip-text text-transparent">Edit Transaction</h2>
            <p class="text-gray-600 text-sm">Update transaction details - #{{ $transaction->id }}</p>
        </div>
    </div>

    <form action="{{ route('admin.financial.transactions.update', $transaction->id) }}" method="POST" class="max-w-5xl mx-auto">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center shadow-2xl">
                        <i class="fas fa-exchange-alt text-cyan-600 text-6xl"></i>
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold">Transaction #{{ $transaction->id }}</h3>
                <p class="text-white/80 text-sm">Update transaction information</p>
            </div>

            <!-- Form Content -->
            <div class="p-6 md:p-8 space-y-8">
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2">
                        <div class="bg-gradient-to-r from-cyan-600 to-blue-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-info-circle text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Transaction Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-filter text-cyan-600"></i>
                                Type *
                            </label>
                            <select name="type" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm appearance-none bg-white">
                                <option value="deposit" {{ $transaction->type == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                <option value="withdrawal" {{ $transaction->type == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                                <option value="transfer" {{ $transaction->type == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-tag text-purple-600"></i>
                                Category *
                            </label>
                            <select name="category" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm appearance-none bg-white">
                                <option value="savings" {{ $transaction->category == 'savings' ? 'selected' : '' }}>Savings</option>
                                <option value="loan_repayment" {{ $transaction->category == 'loan_repayment' ? 'selected' : '' }}>Loan Repayment</option>
                                <option value="shares" {{ $transaction->category == 'shares' ? 'selected' : '' }}>Shares</option>
                                <option value="dividend" {{ $transaction->category == 'dividend' ? 'selected' : '' }}>Dividend</option>
                                <option value="emergency" {{ $transaction->category == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                <option value="other" {{ $transaction->category == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-money-bill-wave text-blue-600"></i>
                                Amount *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="number" name="amount" value="{{ old('amount', $transaction->amount) }}" required step="0.01"
                                       class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-receipt text-orange-600"></i>
                                Transaction Fee
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="number" name="fee" value="{{ old('fee', $transaction->fee ?? 0) }}" step="0.01"
                                       class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-percentage text-red-600"></i>
                                Tax Amount
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="number" name="tax_amount" value="{{ old('tax_amount', $transaction->tax_amount ?? 0) }}" step="0.01"
                                       class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-hand-holding-usd text-yellow-600"></i>
                                Commission
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="number" name="commission" value="{{ old('commission', $transaction->commission ?? 0) }}" step="0.01"
                                       class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-credit-card text-pink-600"></i>
                                Payment Method *
                            </label>
                            <select name="payment_method" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm appearance-none bg-white">
                                <option value="cash" {{ $transaction->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank_transfer" {{ $transaction->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mobile_money" {{ $transaction->payment_method == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="cheque" {{ $transaction->payment_method == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="card" {{ $transaction->payment_method == 'card' ? 'selected' : '' }}>Card</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-broadcast-tower text-violet-600"></i>
                                Channel
                            </label>
                            <select name="channel" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Channel</option>
                                <option value="branch" {{ $transaction->channel == 'branch' ? 'selected' : '' }}>Branch</option>
                                <option value="atm" {{ $transaction->channel == 'atm' ? 'selected' : '' }}>ATM</option>
                                <option value="online" {{ $transaction->channel == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="mobile_app" {{ $transaction->channel == 'mobile_app' ? 'selected' : '' }}>Mobile App</option>
                                <option value="ussd" {{ $transaction->channel == 'ussd' ? 'selected' : '' }}>USSD</option>
                                <option value="agent" {{ $transaction->channel == 'agent' ? 'selected' : '' }}>Agent</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-flag text-rose-600"></i>
                                Priority
                            </label>
                            <select name="priority" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all text-sm appearance-none bg-white">
                                <option value="low" {{ ($transaction->priority ?? 'normal') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="normal" {{ ($transaction->priority ?? 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ ($transaction->priority ?? 'normal') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ ($transaction->priority ?? 'normal') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-hashtag text-teal-600"></i>
                                Reference Number
                            </label>
                            <input type="text" name="reference" value="{{ old('reference', $transaction->reference) }}" placeholder="TXN-REF-XXXX" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-file-invoice text-purple-600"></i>
                                Receipt Number
                            </label>
                            <input type="text" name="receipt_number" value="{{ old('receipt_number', $transaction->receipt_number) }}" placeholder="RCP-XXXX" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-map-marker-alt text-red-600"></i>
                                Location
                            </label>
                            <input type="text" name="location" value="{{ old('location', $transaction->location) }}" placeholder="Branch/Location" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-circle text-green-600 text-[8px]"></i>
                                Status *
                            </label>
                            <select name="status" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm appearance-none bg-white">
                                <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ $transaction->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ $transaction->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="reversed" {{ $transaction->status == 'reversed' ? 'selected' : '' }}>Reversed</option>
                            </select>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-comment text-green-600"></i>
                                Description
                            </label>
                            <textarea name="description" rows="2" placeholder="Transaction description..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm">{{ old('description', $transaction->description) }}</textarea>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-sticky-note text-yellow-600"></i>
                                Internal Notes
                            </label>
                            <textarea name="notes" rows="2" placeholder="Internal notes (not visible to member)..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all text-sm">{{ old('notes', $transaction->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('admin.financial.transactions') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Update Transaction
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
