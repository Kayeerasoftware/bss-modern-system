@php
    $backRoute = $backRoute ?? route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.transactions');
    $formAction = $formAction ?? route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.transactions.store');
    $cancelRoute = $cancelRoute ?? route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.transactions');
    $memberSummaries = $memberSummaries ?? [];
@endphp
<div class="min-h-screen bg-gradient-to-br from-cyan-50 via-blue-50 to-indigo-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ $backRoute }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-cyan-600"></i>
        </a>
        <div>
            <h2 id="transaction_page_title" class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 bg-clip-text text-transparent">New Transaction</h2>
            <p id="transaction_page_subtitle" class="text-gray-600 text-sm">Create a new financial transaction</p>
        </div>
    </div>

    <form action="{{ $formAction }}" method="POST" class="max-w-6xl mx-auto">
        @csrf

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <!-- Header Section -->
            <div id="transaction_header" class="bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center shadow-2xl">
                        <i id="transaction_icon" class="fas fa-exchange-alt text-cyan-600 text-6xl"></i>
                    </div>
                </div>
                <h3 id="transaction_form_title" class="text-white text-xl font-bold">New Financial Transaction</h3>
                <p id="transaction_form_subtitle" class="text-white/80 text-sm">Record a new transaction with complete details</p>
                <div class="mt-4 flex flex-wrap justify-center gap-2 text-xs">
                    <span id="transaction_badge_type" class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-white/90 font-semibold uppercase tracking-wide">Type: Select</span>
                    <span id="transaction_badge_category" class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-white/90 font-semibold uppercase tracking-wide">Category: Select</span>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-6 md:p-8 space-y-8">
                <!-- Transaction Information -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2">
                        <div id="transaction_info_bar" class="bg-gradient-to-r from-cyan-600 to-blue-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-info-circle text-white text-lg"></i>
                        </div>
                        <h3 id="transaction_info_title" class="text-xl font-bold text-gray-800">Transaction Information</h3>
                    </div>
                    <div id="category_hint" class="mb-6 rounded-2xl border border-cyan-100 bg-cyan-50/70 p-4 text-sm text-gray-700">
                        <div class="flex items-center gap-2 font-semibold text-cyan-700">
                            <i class="fas fa-bolt"></i>
                            <span id="category_hint_title">Select a type and category</span>
                        </div>
                        <p id="category_hint_body" class="mt-1 text-gray-600">The form will adapt to the selected transaction type and category.</p>
                    </div>
                    <div id="transfer_preview" class="hidden mb-6 rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50 via-blue-50 to-cyan-50 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-indigo-600 font-semibold">Transfer Summary</p>
                                <p class="text-lg font-bold text-gray-900" id="transfer_preview_route">Select members to preview</p>
                                <p class="text-sm text-gray-600" id="transfer_preview_statement">Choose sender and recipient.</p>
                                <p class="text-xs text-gray-500" id="transfer_preview_balance">Sender balance: UGX 0.00</p>
                            </div>
                            <div class="flex items-center gap-2 text-indigo-600 font-semibold">
                                <i class="fas fa-arrow-right"></i>
                                <span id="transfer_preview_amount">UGX 0.00</span>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="rounded-xl border border-blue-100 bg-blue-50 p-3">
                                <p class="text-xs uppercase text-blue-600 font-semibold">Sender Balance</p>
                                <p class="text-[11px] text-blue-700/70">Money the sender can use</p>
                                <p class="text-lg font-bold text-blue-900">UGX <span id="transfer_available_balance">0.00</span></p>
                            </div>
                            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
                                <p class="text-xs uppercase text-emerald-600 font-semibold">Transfer Amount</p>
                                <p class="text-[11px] text-emerald-700/70">How much will be sent</p>
                                <p class="text-lg font-bold text-emerald-900">UGX <span id="transfer_amount_value">0.00</span></p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-white p-3">
                                <p class="text-xs uppercase text-slate-600 font-semibold">Transfer Check</p>
                                <p class="text-[11px] text-slate-500">Enough balance-</p>
                                <p id="transfer_balance_status" class="text-sm font-semibold text-slate-700">Select amount</p>
                            </div>
                        </div>
                    </div>
                    <div id="member_snapshot" class="hidden mb-6 rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase text-slate-500 font-semibold">Member Snapshot</p>
                                <p class="text-lg font-bold text-gray-900" id="snapshot_member_name">Select a member</p>
                                <p class="text-xs text-gray-500" id="snapshot_member_id">Member ID: --</p>
                            </div>
                            <div class="text-sm text-slate-600" id="snapshot_status">Select a transaction type and category.</div>
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div class="rounded-xl border border-blue-100 bg-blue-50 p-3">
                                <p class="text-xs uppercase text-blue-600 font-semibold">Balance</p>
                                <p class="text-lg font-bold text-blue-900">UGX <span id="snapshot_balance">0.00</span></p>
                            </div>
                            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
                                <p class="text-xs uppercase text-emerald-600 font-semibold">Savings</p>
                                <p class="text-lg font-bold text-emerald-900">UGX <span id="snapshot_savings">0.00</span></p>
                            </div>
                            <div class="rounded-xl border border-amber-100 bg-amber-50 p-3">
                                <p class="text-xs uppercase text-amber-600 font-semibold">Loan Balance</p>
                                <p class="text-lg font-bold text-amber-900">UGX <span id="snapshot_loan_balance">0.00</span></p>
                                <p class="text-[11px] text-amber-700/70" id="snapshot_loan_status">No active loan</p>
                            </div>
                            <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-3">
                                <p class="text-xs uppercase text-indigo-600 font-semibold">Shares Value</p>
                                <p class="text-lg font-bold text-indigo-900">UGX <span id="snapshot_share_value">0.00</span></p>
                                <p class="text-[11px] text-indigo-700/70" id="snapshot_share_units">0 shares</p>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="rounded-xl border border-purple-100 bg-purple-50 p-3">
                                <p class="text-xs uppercase text-purple-600 font-semibold">Latest Dividend</p>
                                <p class="text-lg font-bold text-purple-900">UGX <span id="snapshot_dividend_amount">0.00</span></p>
                                <p class="text-[11px] text-purple-700/70" id="snapshot_dividend_meta">No dividend history</p>
                            </div>
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                                <p class="text-xs uppercase text-slate-600 font-semibold">Last Savings Activity</p>
                                <p class="text-lg font-bold text-slate-900">UGX <span id="snapshot_savings_amount">0.00</span></p>
                                <p class="text-[11px] text-slate-600/70" id="snapshot_savings_meta">No savings history</p>
                            </div>
                        </div>
                    </div>
                    <div id="loan_panel" class="hidden mb-6 rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase text-emerald-600 font-semibold">Loan Repayment</p>
                                <p class="text-lg font-bold text-gray-900" id="loan_member_name">Select a member</p>
                                <p class="text-xs text-gray-500" id="loan_status_message">Loan details will appear here.</p>
                            </div>
                            <div class="text-sm text-emerald-700 font-semibold" id="loan_balance_badge">Outstanding: UGX 0.00</div>
                        </div>
                        <div id="loan_details" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div class="rounded-xl border border-emerald-100 bg-white p-3">
                                <p class="text-xs uppercase text-emerald-600 font-semibold">Loan ID</p>
                                <p class="text-sm font-bold text-gray-900" id="loan_id_display">--</p>
                            </div>
                            <div class="rounded-xl border border-emerald-100 bg-white p-3">
                                <p class="text-xs uppercase text-emerald-600 font-semibold">Remaining Balance</p>
                                <p class="text-lg font-bold text-emerald-900">UGX <span id="loan_remaining_display">0.00</span></p>
                            </div>
                            <div class="rounded-xl border border-emerald-100 bg-white p-3">
                                <p class="text-xs uppercase text-emerald-600 font-semibold">Monthly Payment</p>
                                <p class="text-lg font-bold text-emerald-900">UGX <span id="loan_monthly_display">0.00</span></p>
                            </div>
                            <div class="rounded-xl border border-emerald-100 bg-white p-3">
                                <p class="text-xs uppercase text-emerald-600 font-semibold">Original Amount</p>
                                <p class="text-lg font-bold text-emerald-900">UGX <span id="loan_amount_display">0.00</span></p>
                            </div>
                        </div>
                        <div id="loan_allocation" class="hidden mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="rounded-xl border border-emerald-100 bg-white p-3">
                                <p class="text-xs uppercase text-emerald-600 font-semibold">Applied To Loan</p>
                                <p class="text-lg font-bold text-emerald-900">UGX <span id="loan_applied_display">0.00</span></p>
                            </div>
                            <div class="rounded-xl border border-blue-100 bg-blue-50 p-3">
                                <p class="text-xs uppercase text-blue-600 font-semibold">Excess To Savings</p>
                                <p class="text-lg font-bold text-blue-900">UGX <span id="loan_excess_display">0.00</span></p>
                            </div>
                        </div>
                        <input type="hidden" name="metadata[loan_id]" id="loan_id">
                        <input type="hidden" name="metadata[loan_remaining_balance]" id="loan_remaining_balance">
                        <input type="hidden" name="metadata[loan_monthly_payment]" id="loan_monthly_payment">
                        <input type="hidden" name="metadata[loan_amount]" id="loan_amount">
                        <input type="hidden" name="metadata[loan_applied_amount]" id="loan_applied_amount">
                        <input type="hidden" name="metadata[loan_excess_to_savings]" id="loan_excess_to_savings">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2" data-field="member_id">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user text-cyan-600"></i>
                                <span id="member_label_text">Member</span> *
                            </label>
                            <select name="member_id" id="member_id" required onchange="loadMemberBalance(); syncTransferMembers();" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Member</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" data-balance="{{ $member->balance ?? 0 }}" data-savings="{{ $member->savings ?? 0 }}">
                                        {{ $member->full_name }} ({{ $member->member_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2 hidden md:col-span-2" data-field="transfer_to_member">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user-friends text-indigo-600"></i>
                                To Member *
                            </label>
                            <select name="metadata[transfer_to_member_id]" id="transfer_to_member_id" onchange="syncTransferMembers()" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Recipient</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" data-name="{{ $member->full_name }}">
                                        {{ $member->full_name }} ({{ $member->member_id }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="metadata[transfer_to_member_name]" id="transfer_to_member_name">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-filter text-blue-600"></i>
                                Transaction Type *
                            </label>
                            <select name="type" id="type" required onchange="applyTransactionMode(); calculateTransaction()" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Type</option>
                                <option value="deposit">Deposit</option>
                                <option value="withdrawal">Withdrawal</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-tag text-purple-600"></i>
                                Category *
                            </label>
                            <select name="category" id="category" required onchange="applyTransactionMode()" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Category</option>
                                <option value="savings">Savings</option>
                                <option value="loan_repayment">Loan Repayment</option>
                                <option value="shares">Shares</option>
                                <option value="dividend">Dividend</option>
                                <option value="emergency">Emergency</option>
                                <option value="other">Other</option>
                            </select>
                            <p id="withdrawal_category_note" class="hidden text-xs text-rose-600 font-semibold">
                                Withdrawal categories are limited to savings, dividends, emergency, or other.
                            </p>
                        </div>

                        <div class="space-y-2" data-field="amount">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-money-bill-wave text-green-600"></i>
                                Amount *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                            <input type="hidden" name="amount" id="amount">
                            <input type="text" id="amount_display" required placeholder="0" oninput="formatAmount(this)" class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm">
                            </div>
                            <p id="transfer_amount_hint" class="hidden text-xs text-gray-500">Available to transfer: UGX 0.00</p>
                            <p id="transfer_amount_error" class="hidden text-xs text-red-600 font-semibold">Insufficient balance to send this amount.</p>
                            <p id="withdrawal_amount_error" class="hidden text-xs text-red-600 font-semibold">No funds available to withdraw.</p>
                            <p id="loan_amount_hint" class="hidden text-xs text-emerald-700 font-semibold">Loan outstanding: UGX 0.00</p>
                            <p id="loan_amount_error" class="hidden text-xs text-red-600 font-semibold">No loan available, no payment needed.</p>
                        </div>

                        <div class="space-y-2" data-field="fee">
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

                        <div class="space-y-2" data-field="tax_amount">
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

                        <div class="space-y-2" data-field="commission">
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

                        <div class="space-y-2" data-field="net_amount">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calculator text-indigo-600"></i>
                                Net Amount
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="text" id="net_amount_display" readonly class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                            </div>
                        </div>

                        <div class="space-y-2" data-field="total_charges">
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

                        <div class="space-y-2" data-field="payment_method">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-credit-card text-pink-600"></i>
                                Payment Method *
                            </label>
                            <select name="payment_method" id="payment_method" required onchange="applyPaymentMethod()" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="cheque">Cheque</option>
                                <option value="card">Card</option>
                            </select>
                        </div>

                        <div class="space-y-2" data-field="channel">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-broadcast-tower text-violet-600"></i>
                                Channel
                            </label>
                            <select name="channel" id="channel" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all text-sm appearance-none bg-white">
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

                        <div class="space-y-2" data-field="reference">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-hashtag text-teal-600"></i>
                                Reference Number
                            </label>
                            <input type="text" name="reference" placeholder="TXN-REF-XXXX" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2" data-field="receipt_number">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-file-invoice text-purple-600"></i>
                                Receipt Number
                            </label>
                            <input type="text" name="receipt_number" placeholder="RCP-XXXX" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2" data-field="batch_id">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-layer-group text-slate-600"></i>
                                Batch ID
                            </label>
                            <input type="text" name="batch_id" placeholder="BATCH-XXXX" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2" data-field="location">
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

                        <div class="space-y-2" data-field="scheduled_at">
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
                                <label for="notification_sent" class="text-sm text-gray-700 cursor-pointer">Send SMS/Email notification to member</label>
                            </div>
                        </div>

                        <div class="space-y-2 md:col-span-3" data-field="description">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-comment text-green-600"></i>
                                Description
                            </label>
                            <textarea name="description" id="description" rows="2" placeholder="Transaction description..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm"></textarea>
                        </div>

                        <div class="space-y-2 md:col-span-3" data-field="notes">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-sticky-note text-yellow-600"></i>
                                Internal Notes
                            </label>
                            <textarea name="notes" id="notes" rows="2" placeholder="Internal notes (not visible to member)..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Member Balance Info -->
                <div id="balance_info" class="hidden grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
                        <p class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-wallet text-blue-600"></i>
                            Current Balance
                        </p>
                        <p class="text-2xl font-bold text-blue-600">UGX <span id="current_balance">0</span></p>
                    </div>
                    <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border-2 border-green-200">
                        <p class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-piggy-bank text-green-600"></i>
                            Total Savings
                        </p>
                        <p class="text-2xl font-bold text-green-600">UGX <span id="total_savings">0</span></p>
                    </div>
                    <div class="p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border-2 border-orange-200">
                        <p class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-orange-600"></i>
                            Transaction Count
                        </p>
                        <p class="text-2xl font-bold text-orange-600"><span id="transaction_count">0</span></p>
                    </div>
                    <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border-2 border-purple-200">
                        <p class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Balance After
                        </p>
                        <p class="text-2xl font-bold text-purple-600">UGX <span id="balance_after">0</span></p>
                    </div>
                </div>

                <!-- Transaction Summary -->
                <div id="transaction_summary" class="hidden">
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b-2">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-chart-pie text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Transaction Summary</h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="p-4 bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl border border-cyan-200">
                            <p class="text-xs text-gray-600 mb-1">Gross Amount</p>
                            <p class="text-lg font-bold text-cyan-600">UGX <span id="summary_amount">0</span></p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-red-50 to-orange-50 rounded-xl border border-red-200">
                            <p class="text-xs text-gray-600 mb-1">Total Charges</p>
                            <p class="text-lg font-bold text-red-600">UGX <span id="summary_charges">0</span></p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-200">
                            <p class="text-xs text-gray-600 mb-1">Net Amount</p>
                            <p class="text-lg font-bold text-green-600">UGX <span id="summary_net">0</span></p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border border-purple-200">
                            <p class="text-xs text-gray-600 mb-1">New Balance</p>
                            <p class="text-lg font-bold text-purple-600">UGX <span id="summary_balance">0</span></p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between gap-4 pt-6 border-t-2 border-gray-100">
                    <button id="transaction_preview_button" type="button" class="px-8 py-3 border-2 border-indigo-300 text-indigo-700 rounded-xl hover:bg-indigo-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-eye mr-2"></i>Preview Details
                    </button>
                    <a href="{{ $cancelRoute }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button id="transaction_submit_button" type="submit" class="px-8 py-3 bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-check mr-2"></i>Create Transaction
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="transaction_preview_modal" class="hidden fixed inset-0 z-50">
    <div id="preview_overlay" class="absolute inset-0 bg-slate-900/60"></div>
    <div class="relative mx-auto my-8 w-full max-w-5xl px-4">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-white">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="System Logo" class="w-12 h-12 rounded-xl bg-slate-50 p-2 border border-slate-200">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Transaction Summary</p>
                            <p class="text-xl font-bold text-slate-900">{{ config('app.name') }}</p>
                            <p class="text-xs text-slate-500" id="preview_contact_line">
                                Email: {{ config('mail.from.address') ?? (auth()->user()->email ?? 'N/A') }} |
                                Phone: {{ auth()->user()->phone ?? auth()->user()->member?->contact ?? '+256 700 000 000' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <p class="text-sm font-semibold text-slate-700" id="preview_title">Review Details</p>
                        <button type="button" id="preview_close_top" class="w-10 h-10 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto" id="print_body">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print-block">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Type</p>
                        <p class="text-lg font-bold text-slate-900" id="preview_type">--</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Category</p>
                        <p class="text-lg font-bold text-slate-900" id="preview_category">--</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Member</p>
                        <div class="flex items-center gap-3 mt-2">
                            <img id="preview_member_photo" src="{{ asset('images/default-avatar.svg') }}" data-default="{{ asset('images/default-avatar.svg') }}" alt="Member photo" class="w-12 h-12 rounded-xl object-cover border border-slate-200 bg-white">
                            <div>
                                <p class="text-sm font-semibold text-slate-900" id="preview_member">--</p>
                                <p class="text-xs text-slate-500" id="preview_member_id">Member ID: --</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 print-block">
                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                        <p class="text-xs uppercase text-emerald-600 font-semibold">Amount</p>
                        <p class="text-lg font-bold text-emerald-900" id="preview_amount">UGX 0.00</p>
                    </div>
                    <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
                        <p class="text-xs uppercase text-amber-600 font-semibold">Charges</p>
                        <p class="text-lg font-bold text-amber-900" id="preview_charges">UGX 0.00</p>
                    </div>
                    <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4">
                        <p class="text-xs uppercase text-indigo-600 font-semibold">Net</p>
                        <p class="text-lg font-bold text-indigo-900" id="preview_net">UGX 0.00</p>
                    </div>
                    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
                        <p class="text-xs uppercase text-blue-600 font-semibold">Balance After</p>
                        <p class="text-lg font-bold text-blue-900" id="preview_balance_after">UGX 0.00</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 print-block">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold mb-2">Transfer / Loan Details</p>
                        <p class="text-sm text-slate-700" id="preview_special">No special details.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold mb-2">Payment</p>
                        <p class="text-sm text-slate-700" id="preview_payment">--</p>
                        <p class="text-xs text-slate-500 mt-2">Channel: <span id="preview_channel">--</span></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 print-block">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Currency</p>
                        <p class="text-sm text-slate-700" id="preview_currency">--</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Priority</p>
                        <p class="text-sm text-slate-700" id="preview_priority">--</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Transaction Date</p>
                        <p class="text-sm text-slate-700" id="preview_transaction_date">--</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Scheduled Date</p>
                        <p class="text-sm text-slate-700" id="preview_scheduled_date">--</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print-block">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Reference</p>
                        <p class="text-sm text-slate-700" id="preview_reference">--</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Receipt</p>
                        <p class="text-sm text-slate-700" id="preview_receipt">--</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Batch ID</p>
                        <p class="text-sm text-slate-700" id="preview_batch">--</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print-block">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Location</p>
                        <p class="text-sm text-slate-700" id="preview_location">--</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Notification</p>
                        <p class="text-sm text-slate-700" id="preview_notification">--</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase text-slate-500 font-semibold">Channel</p>
                        <p class="text-sm text-slate-700" id="preview_channel_dup">--</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 print-block">
                    <p class="text-xs uppercase text-slate-500 font-semibold">Description</p>
                    <p class="text-sm text-slate-700" id="preview_description">--</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 print-block">
                    <p class="text-xs uppercase text-slate-500 font-semibold">Internal Notes</p>
                    <p class="text-sm text-slate-700" id="preview_notes">--</p>
                </div>
            </div>

            <div class="flex flex-wrap justify-between gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50">
                <button type="button" id="preview_print_button" class="px-6 py-2 border-2 border-indigo-300 text-indigo-700 rounded-xl hover:bg-white transition font-semibold">
                    <i class="fas fa-print mr-2"></i>Print Preview
                </button>
                <button type="button" id="preview_close_bottom" class="px-6 py-2 border-2 border-slate-300 text-slate-700 rounded-xl hover:bg-white transition font-semibold">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div id="print_root" class="hidden"></div>

<style>
@media print {
    @page {
        margin: 10mm;
    }
    html, body {
        margin: 0 !important;
        padding: 0 !important;
    }
    body * {
        display: none !important;
        visibility: hidden !important;
    }
    #print_root,
    #print_root * {
        display: block !important;
        visibility: visible !important;
    }
    #print_root {
        display: block !important;
        position: relative !important;
        margin: 0 !important;
        width: 100% !important;
        color: #0f172a !important;
        transform: scale(0.82);
        transform-origin: top left;
    }
    #print_root > div {
        break-before: avoid !important;
        page-break-before: avoid !important;
    }
    #print_root .print-page {
        page-break-after: always;
        break-after: page;
    }
    #print_root .print-page:last-child {
        page-break-after: auto;
        break-after: auto;
    }
}
</style>

<script>
let currentBalance = 0;
let totalSavings = 0;
const MEMBER_DATA = @json($memberSummaries);

const TYPE_CONFIG = {
    deposit: {
        label: 'Deposit',
        badge: 'Deposit Mode',
        icon: 'fa-arrow-down',
        headerGradient: ['from-emerald-600', 'via-green-600', 'to-teal-600'],
        titleGradient: ['from-emerald-600', 'via-green-600', 'to-teal-600'],
        infoGradient: ['from-emerald-600', 'to-green-600'],
        buttonGradient: ['from-emerald-600', 'via-green-600', 'to-teal-600'],
        showCharges: false,
    },
    withdrawal: {
        label: 'Withdrawal',
        badge: 'Withdrawal Mode',
        icon: 'fa-arrow-up',
        headerGradient: ['from-red-600', 'via-rose-600', 'to-pink-600'],
        titleGradient: ['from-red-600', 'via-rose-600', 'to-pink-600'],
        infoGradient: ['from-red-600', 'to-rose-600'],
        buttonGradient: ['from-red-600', 'via-rose-600', 'to-pink-600'],
        showCharges: true,
    },
    transfer: {
        label: 'Transfer',
        badge: 'Transfer Mode (Withdrawal)',
        icon: 'fa-exchange-alt',
        headerGradient: ['from-indigo-600', 'via-blue-600', 'to-cyan-600'],
        titleGradient: ['from-indigo-600', 'via-blue-600', 'to-cyan-600'],
        infoGradient: ['from-indigo-600', 'to-blue-600'],
        buttonGradient: ['from-indigo-600', 'via-blue-600', 'to-cyan-600'],
        showCharges: true,
    },
    default: {
        label: 'New',
        badge: 'Select Type',
        icon: 'fa-exchange-alt',
        headerGradient: ['from-cyan-600', 'via-blue-600', 'to-indigo-600'],
        titleGradient: ['from-cyan-600', 'via-blue-600', 'to-indigo-600'],
        infoGradient: ['from-cyan-600', 'to-blue-600'],
        buttonGradient: ['from-cyan-600', 'via-blue-600', 'to-indigo-600'],
        showCharges: true,
    },
};

const CATEGORY_CONFIG = {
    savings: {
        label: 'Savings',
        hintTitle: 'Savings-focused transaction',
        hintBody: 'Use this for member savings contributions or withdrawals.',
        showFields: ['reference'],
    },
    loan_repayment: {
        label: 'Loan Repayment',
        hintTitle: 'Loan repayment details',
        hintBody: 'Include the loan reference in the description or reference number.',
        showFields: ['reference', 'receipt_number', 'batch_id', 'location'],
    },
    shares: {
        label: 'Shares',
        hintTitle: 'Share purchase or redemption',
        hintBody: 'Keep the reference and receipt for share-related entries.',
        showFields: ['reference', 'receipt_number'],
    },
    dividend: {
        label: 'Dividend',
        hintTitle: 'Dividend distribution',
        hintBody: 'Use reference or receipt details for dividend confirmations.',
        showFields: ['reference', 'receipt_number'],
    },
    emergency: {
        label: 'Emergency',
        hintTitle: 'Emergency transaction',
        hintBody: 'Capture the location and notes for emergency disbursements.',
        showFields: ['location'],
    },
    other: {
        label: 'Other',
        hintTitle: 'General transaction',
        hintBody: 'Provide any optional details that apply to this transaction.',
        showFields: ['reference', 'receipt_number', 'batch_id', 'location', 'scheduled_at'],
    },
    default: {
        label: 'Select',
        hintTitle: 'Select a category',
        hintBody: 'The form will highlight fields that matter for the chosen category.',
        showFields: ['reference', 'receipt_number', 'batch_id', 'location', 'scheduled_at'],
    },
};

const WITHDRAWAL_ALLOWED_CATEGORIES = ['savings', 'dividend', 'emergency', 'other'];

const ALL_GRADIENTS = [
    'from-cyan-600', 'via-blue-600', 'to-indigo-600',
    'from-emerald-600', 'via-green-600', 'to-teal-600',
    'from-red-600', 'via-rose-600', 'to-pink-600',
    'from-indigo-600', 'to-blue-600', 'to-cyan-600', 'from-blue-600', 'via-blue-600',
    'from-emerald-600', 'to-green-600', 'from-red-600', 'to-rose-600', 'via-rose-600',
];

const CATEGORY_FIELDS = ['reference', 'receipt_number', 'batch_id', 'location', 'scheduled_at'];
const ALWAYS_VISIBLE_FIELDS = ['member_id', 'amount', 'payment_method', 'channel'];
const CHARGE_FIELDS = ['fee', 'tax_amount', 'commission', 'net_amount', 'total_charges'];
const TRANSFER_FIELDS = ['transfer_to_member'];
const BANK_OPTIONS = [
    'Stanbic',
    'Centenary',
    'Equity',
    'DFCU',
    'Absa',
    'Standard Chartered',
    'PostBank',
    'UDB',
    'Other Bank',
];
const MOBILE_MONEY_OPTIONS = ['MTN', 'Airtel', 'Other Network'];

let defaultChannelOptions = [];

function swapGradient(el, gradientClasses) {
    if (!el) return;
    ALL_GRADIENTS.forEach((cls) => el.classList.remove(cls));
    gradientClasses.forEach((cls) => el.classList.add(cls));
}

function setFieldVisibility(fieldKey, isVisible) {
    const field = document.querySelector(`[data-field="${fieldKey}"]`);
    if (!field) return;
    field.classList.toggle('hidden', !isVisible);
}

function applyTransactionMode() {
    const type = document.getElementById('type')?.value || 'default';
    applyWithdrawalCategoryFilter(type);
    const category = document.getElementById('category')?.value || 'default';
    const typeConfig = TYPE_CONFIG[type] || TYPE_CONFIG.default;
    const categoryConfig = CATEGORY_CONFIG[category] || CATEGORY_CONFIG.default;

    const header = document.getElementById('transaction_header');
    const title = document.getElementById('transaction_page_title');
    const infoBar = document.getElementById('transaction_info_bar');
    const submitButton = document.getElementById('transaction_submit_button');
    const icon = document.getElementById('transaction_icon');

    swapGradient(header, typeConfig.headerGradient);
    swapGradient(title, typeConfig.titleGradient);
    swapGradient(infoBar, typeConfig.infoGradient);
    swapGradient(submitButton, typeConfig.buttonGradient);

    if (icon) {
        icon.classList.remove('fa-arrow-down', 'fa-arrow-up', 'fa-exchange-alt');
        icon.classList.add(typeConfig.icon);
    }

    const pageTitle = document.getElementById('transaction_page_title');
    const pageSubtitle = document.getElementById('transaction_page_subtitle');
    const formTitle = document.getElementById('transaction_form_title');
    const formSubtitle = document.getElementById('transaction_form_subtitle');
    if (pageTitle) pageTitle.textContent = `${typeConfig.label} Transaction`;
    if (pageSubtitle) pageSubtitle.textContent = `${typeConfig.badge} - ${categoryConfig.label} Category`;
    if (formTitle) formTitle.textContent = `${typeConfig.label} Transaction`;
    if (formSubtitle) formSubtitle.textContent = categoryConfig.hintTitle;

    const typeBadge = document.getElementById('transaction_badge_type');
    if (typeBadge) typeBadge.textContent = `Type: ${typeConfig.badge}`;

    const categoryBadge = document.getElementById('transaction_badge_category');
    if (categoryBadge) categoryBadge.textContent = `Category: ${categoryConfig.label}`;

    const hintTitle = document.getElementById('category_hint_title');
    const hintBody = document.getElementById('category_hint_body');
    if (hintTitle) hintTitle.textContent = categoryConfig.hintTitle;
    if (hintBody) hintBody.textContent = categoryConfig.hintBody;

    CHARGE_FIELDS.forEach((field) => setFieldVisibility(field, typeConfig.showCharges));
    ALWAYS_VISIBLE_FIELDS.forEach((field) => setFieldVisibility(field, true));
    TRANSFER_FIELDS.forEach((field) => setFieldVisibility(field, type === 'transfer'));
    const toSelect = document.getElementById('transfer_to_member_id');
    if (toSelect) {
        if (type === 'transfer') {
            toSelect.setAttribute('required', 'required');
        } else {
            toSelect.removeAttribute('required');
        }
    }

    CATEGORY_FIELDS.forEach((field) => setFieldVisibility(field, false));
    (categoryConfig.showFields || []).forEach((field) => setFieldVisibility(field, true));

    const description = document.getElementById('description');
    if (description) {
        const placeholders = {
            savings: 'Savings details (member, purpose, notes)...',
            loan_repayment: 'Loan repayment details (loan reference, schedule)...',
            shares: 'Share transaction details (units, price, notes)...',
            dividend: 'Dividend details (period, reference)...',
            emergency: 'Emergency transaction details (reason, approvals)...',
            other: 'Transaction description...',
            default: 'Transaction description...',
        };
        description.placeholder = placeholders[category] || placeholders.default;
    }

    const memberLabel = document.getElementById('member_label_text');
    if (memberLabel) {
        memberLabel.textContent = type === 'transfer' ? 'From Member' : 'Member';
    }

    updateCategoryControls(type, category);
    updateWithdrawalControls();
    syncTransferMembers();
}

function updateMemberSnapshot(memberId, data) {
    const snapshot = document.getElementById('member_snapshot');
    if (!snapshot) return;

    if (!memberId || !data) {
        snapshot.classList.add('hidden');
        return;
    }

    snapshot.classList.remove('hidden');
    document.getElementById('snapshot_member_name').textContent = data.full_name || 'Member';
    document.getElementById('snapshot_member_id').textContent = `Member ID: ${memberId}`;
    document.getElementById('snapshot_balance').textContent = formatNumber(data.balance || 0);
    document.getElementById('snapshot_savings').textContent = formatNumber(data.savings || 0);

    const loanBalance = data.loan?.remaining_balance || 0;
    document.getElementById('snapshot_loan_balance').textContent = formatNumber(loanBalance);
    document.getElementById('snapshot_loan_status').textContent = data.loan
        ? `${data.loan.status} - Loan ${data.loan.loan_id}`
        : 'No active loan';

    document.getElementById('snapshot_share_value').textContent = formatNumber(data.shares?.total_value || 0);
    document.getElementById('snapshot_share_units').textContent = `${formatNumber(data.shares?.total_shares || 0)} shares`;

    document.getElementById('snapshot_dividend_amount').textContent = formatNumber(data.dividend?.amount || 0);
    document.getElementById('snapshot_dividend_meta').textContent = data.dividend
        ? `Rate ${data.dividend.dividend_rate || 0}% - ${data.dividend.payment_date || 'N/A'}`
        : 'No dividend history';

    document.getElementById('snapshot_savings_amount').textContent = formatNumber(data.last_savings?.amount || 0);
    document.getElementById('snapshot_savings_meta').textContent = data.last_savings
        ? `After: UGX ${formatNumber(data.last_savings.balance_after || 0)} - ${data.last_savings.transaction_date || 'N/A'}`
        : 'No savings history';
}

function updateCategoryControls(type, category) {
    const selectedMember = document.getElementById('member_id')?.value;
    const memberData = selectedMember ? (MEMBER_DATA[selectedMember] || null) : null;
    const submitButton = document.getElementById('transaction_submit_button');
    const snapshotStatus = document.getElementById('snapshot_status');

    if (snapshotStatus) {
        snapshotStatus.textContent = 'Review the member snapshot for this transaction.';
        snapshotStatus.classList.remove('text-red-600');
    }

    updateLoanPanel(memberData, category);
    updateLoanRepaymentAmount();

    if (category === 'loan_repayment') {
        if (type !== 'deposit') {
            if (snapshotStatus) {
                snapshotStatus.textContent = 'Loan repayment must use transaction type: deposit.';
                snapshotStatus.classList.add('text-red-600');
            }
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
            return;
        }

        const loan = memberData?.loan;
        if (!loan || (loan.remaining_balance || 0) <= 0) {
            if (snapshotStatus) {
                snapshotStatus.textContent = 'No active loan available for repayment.';
                snapshotStatus.classList.add('text-red-600');
            }
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        } else if (submitButton && type !== 'transfer') {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    } else if (submitButton && type !== 'transfer') {
        submitButton.disabled = false;
        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

function updateLoanPanel(memberData, category) {
    const panel = document.getElementById('loan_panel');
    const details = document.getElementById('loan_details');
    const message = document.getElementById('loan_status_message');
    const nameEl = document.getElementById('loan_member_name');
    const balanceBadge = document.getElementById('loan_balance_badge');
    const type = document.getElementById('type')?.value;

    if (!panel) return;
    if (category !== 'loan_repayment' || type !== 'deposit') {
        panel.classList.add('hidden');
        return;
    }

    panel.classList.remove('hidden');
    if (nameEl) nameEl.textContent = memberData?.full_name || 'Member';

    if (!memberData) {
        if (message) message.textContent = 'Select a member to load loan details.';
        if (balanceBadge) balanceBadge.textContent = 'Outstanding: UGX 0.00';
        if (details) details.classList.add('hidden');
        const allocation = document.getElementById('loan_allocation');
        if (allocation) allocation.classList.add('hidden');
        setLoanHiddenInputs(null);
        return;
    }

    const loan = memberData.loan || null;
    if (!loan) {
        if (message) message.textContent = 'No loan available, no payment needed.';
        if (balanceBadge) balanceBadge.textContent = 'Outstanding: UGX 0.00';
        if (details) details.classList.add('hidden');
        const allocation = document.getElementById('loan_allocation');
        if (allocation) allocation.classList.add('hidden');
        setLoanHiddenInputs(null);
        return;
    }

    if (message) message.textContent = 'Loan repayment selected. Capture the repayment amount below.';
    if (details) details.classList.remove('hidden');
    const allocation = document.getElementById('loan_allocation');
    if (allocation) allocation.classList.add('hidden');
    document.getElementById('loan_id_display').textContent = loan.loan_id || '--';
    document.getElementById('loan_remaining_display').textContent = formatNumber(loan.remaining_balance || 0);
    document.getElementById('loan_monthly_display').textContent = formatNumber(loan.monthly_payment || 0);
    document.getElementById('loan_amount_display').textContent = formatNumber(loan.amount || 0);
    if (balanceBadge) balanceBadge.textContent = `Outstanding: UGX ${formatNumber(loan.remaining_balance || 0)}`;
    setLoanHiddenInputs(loan);
}

function setLoanHiddenInputs(loan) {
    const loanId = document.getElementById('loan_id');
    const remaining = document.getElementById('loan_remaining_balance');
    const monthly = document.getElementById('loan_monthly_payment');
    const amount = document.getElementById('loan_amount');
    const applied = document.getElementById('loan_applied_amount');
    const excess = document.getElementById('loan_excess_to_savings');

    if (!loan) {
        if (loanId) loanId.value = '';
        if (remaining) remaining.value = '';
        if (monthly) monthly.value = '';
        if (amount) amount.value = '';
        if (applied) applied.value = '';
        if (excess) excess.value = '';
        return;
    }

    if (loanId) loanId.value = loan.loan_id || '';
    if (remaining) remaining.value = loan.remaining_balance ?? '';
    if (monthly) monthly.value = loan.monthly_payment ?? '';
    if (amount) amount.value = loan.amount ?? '';
    if (applied) applied.value = '';
    if (excess) excess.value = '';
}

function updateLoanRepaymentAmount() {
    const type = document.getElementById('type')?.value;
    const category = document.getElementById('category')?.value;
    const selectedMember = document.getElementById('member_id')?.value;
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const hint = document.getElementById('loan_amount_hint');
    const error = document.getElementById('loan_amount_error');
    const submitButton = document.getElementById('transaction_submit_button');
    const allocation = document.getElementById('loan_allocation');
    const appliedDisplay = document.getElementById('loan_applied_display');
    const excessDisplay = document.getElementById('loan_excess_display');
    const appliedInput = document.getElementById('loan_applied_amount');
    const excessInput = document.getElementById('loan_excess_to_savings');
    const amountInput = document.getElementById('amount_display');
    const amountHidden = document.getElementById('amount');

    if (type !== 'deposit' || category !== 'loan_repayment' || !selectedMember) {
        if (hint) hint.classList.add('hidden');
        if (error) error.classList.add('hidden');
        if (allocation) allocation.classList.add('hidden');
        return;
    }

    const loan = MEMBER_DATA[selectedMember]?.loan || null;
    if (!loan) {
        if (hint) hint.classList.add('hidden');
        if (error) {
            error.textContent = 'No loan available, no payment needed.';
            error.classList.remove('hidden');
        }
        if (allocation) allocation.classList.add('hidden');
        if (amountInput) amountInput.disabled = true;
        if (amountHidden) amountHidden.value = '';
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
        return;
    }

    const remaining = loan.remaining_balance || 0;
    if (amountInput) amountInput.disabled = false;
    if (hint) {
        hint.textContent = `Loan outstanding: UGX ${formatNumber(remaining)}`;
        hint.classList.remove('hidden');
    }

    const applied = Math.min(amount, remaining);
    const excess = Math.max(amount - remaining, 0);

    if (allocation) allocation.classList.toggle('hidden', amount <= 0);
    if (appliedDisplay) appliedDisplay.textContent = formatNumber(applied);
    if (excessDisplay) excessDisplay.textContent = formatNumber(excess);
    if (appliedInput) appliedInput.value = applied.toString();
    if (excessInput) excessInput.value = excess.toString();

    if (error) {
        if (amount > remaining && remaining > 0) {
            error.textContent = `Excess UGX ${formatNumber(excess)} will be moved to savings.`;
            error.classList.remove('hidden');
            error.classList.remove('text-red-600');
            error.classList.add('text-emerald-700');
        } else {
            error.classList.add('hidden');
            error.classList.remove('text-emerald-700');
            error.classList.add('text-red-600');
        }
    }

    if (submitButton) {
        submitButton.disabled = false;
        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

function applyWithdrawalCategoryFilter(type) {
    const categorySelect = document.getElementById('category');
    const note = document.getElementById('withdrawal_category_note');
    if (!categorySelect) return;

    const isWithdrawal = type === 'withdrawal';
    if (note) note.classList.toggle('hidden', !isWithdrawal);

    Array.from(categorySelect.options).forEach((option) => {
        if (!option.value) return;
        const allowed = !isWithdrawal || WITHDRAWAL_ALLOWED_CATEGORIES.includes(option.value);
        option.disabled = !allowed;
        option.hidden = !allowed;
    });

    if (isWithdrawal && !WITHDRAWAL_ALLOWED_CATEGORIES.includes(categorySelect.value)) {
        categorySelect.value = '';
    }
}

function updateWithdrawalControls() {
    const type = document.getElementById('type')?.value;
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const amountInput = document.getElementById('amount_display');
    const submitButton = document.getElementById('transaction_submit_button');
    const error = document.getElementById('withdrawal_amount_error');
    const snapshotStatus = document.getElementById('snapshot_status');
    const selectedMember = document.getElementById('member_id')?.value;

    if (type !== 'withdrawal') {
        if (error) error.classList.add('hidden');
        return;
    }

    if (!selectedMember) {
        if (error) error.classList.add('hidden');
        return;
    }

    const available = currentBalance || 0;
    const hasFunds = available > 0;
    const canWithdraw = amount > 0 && amount <= available;

    if (amountInput) {
        amountInput.setAttribute('data-max', available.toString());
        amountInput.setAttribute('title', `Available to withdraw: UGX ${formatNumber(available)}`);
    }

    if (!hasFunds) {
        if (error) {
            error.textContent = 'No funds available to withdraw.';
            error.classList.remove('hidden');
        }
        if (snapshotStatus) {
            snapshotStatus.textContent = 'No funds available to withdraw.';
            snapshotStatus.classList.add('text-red-600');
        }
    } else if (amount > 0 && !canWithdraw) {
        if (error) {
            error.textContent = `Insufficient funds. Available: UGX ${formatNumber(available)}.`;
            error.classList.remove('hidden');
        }
        if (snapshotStatus) {
            snapshotStatus.textContent = `Insufficient funds. Available: UGX ${formatNumber(available)}.`;
            snapshotStatus.classList.add('text-red-600');
        }
    } else if (error) {
        error.classList.add('hidden');
    }

    if (submitButton) {
        const disable = !hasFunds || (amount > 0 && !canWithdraw);
        submitButton.disabled = disable;
        submitButton.classList.toggle('opacity-50', disable);
        submitButton.classList.toggle('cursor-not-allowed', disable);
    }
}

function setChannelOptions(options, placeholder = 'Select Channel') {
    const channel = document.getElementById('channel');
    if (!channel) return;
    channel.innerHTML = '';
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = placeholder;
    channel.appendChild(defaultOption);
    options.forEach((option) => {
        const opt = document.createElement('option');
        opt.value = option.toLowerCase().replace(/\s+/g, '_');
        opt.textContent = option;
        channel.appendChild(opt);
    });
}

function applyPaymentMethod() {
    const method = document.getElementById('payment_method')?.value;
    if (!method) {
        setChannelOptions(defaultChannelOptions, 'Select Channel');
        return;
    }

    if (method === 'mobile_money') {
        setChannelOptions(MOBILE_MONEY_OPTIONS, 'Select Network');
    } else if (method === 'card') {
        setChannelOptions(BANK_OPTIONS, 'Select Bank');
    } else if (method === 'bank_transfer') {
        setChannelOptions(BANK_OPTIONS, 'Select Bank');
    } else if (method === 'cheque') {
        setChannelOptions(BANK_OPTIONS, 'Select Bank');
    } else if (method === 'cash') {
        setChannelOptions(['Branch', 'Head Office', 'Cashier Desk'], 'Select Location');
    } else {
        setChannelOptions(defaultChannelOptions, 'Select Channel');
    }
}

function syncTransferMembers() {
    const type = document.getElementById('type')?.value;
    const fromMember = document.getElementById('member_id')?.value;
    const toSelect = document.getElementById('transfer_to_member_id');
    const toName = document.getElementById('transfer_to_member_name');
    if (!toSelect) return;

    Array.from(toSelect.options).forEach((opt) => {
        if (!opt.value) return;
        opt.disabled = opt.value === fromMember;
    });

    if (type !== 'transfer') {
        toSelect.value = '';
        if (toName) toName.value = '';
        updateTransferPreview();
        return;
    }

    const selectedOption = toSelect.options[toSelect.selectedIndex];
    if (toName) {
        toName.value = selectedOption?.dataset?.name || '';
    }

    updateTransferPreview();
}

function updateTransferPreview() {
    const type = document.getElementById('type')?.value;
    const preview = document.getElementById('transfer_preview');
    if (!preview) return;

    const amountHint = document.getElementById('transfer_amount_hint');
    const amountError = document.getElementById('transfer_amount_error');

    if (type !== 'transfer') {
        preview.classList.add('hidden');
        if (amountHint) amountHint.classList.add('hidden');
        if (amountError) amountError.classList.add('hidden');
        return;
    }

    preview.classList.remove('hidden');
    if (amountHint) {
        amountHint.classList.remove('hidden');
        amountHint.textContent = `Available to transfer: UGX ${formatNumber(currentBalance)}`;
    }
    const fromSelect = document.getElementById('member_id');
    const toSelect = document.getElementById('transfer_to_member_id');
    const fromLabel = fromSelect?.options[fromSelect.selectedIndex]?.textContent || 'Sender';
    const toLabel = toSelect?.options[toSelect.selectedIndex]?.textContent || 'Recipient';
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const amountInput = document.getElementById('amount_display');

    const route = document.getElementById('transfer_preview_route');
    if (route) route.textContent = `${fromLabel} -> ${toLabel}`;

    const statement = document.getElementById('transfer_preview_statement');
    if (statement) {
        statement.textContent = `${fromLabel} transferred UGX ${formatNumber(amount)} to ${toLabel}.`;
    }

    const balance = document.getElementById('transfer_preview_balance');
    if (balance) balance.textContent = `Sender balance: UGX ${formatNumber(currentBalance)}`;

    const amountEl = document.getElementById('transfer_preview_amount');
    if (amountEl) amountEl.textContent = `UGX ${formatNumber(amount)}`;

    const availableEl = document.getElementById('transfer_available_balance');
    if (availableEl) availableEl.textContent = formatNumber(currentBalance);

    const amountValueEl = document.getElementById('transfer_amount_value');
    if (amountValueEl) amountValueEl.textContent = formatNumber(amount);

    const statusEl = document.getElementById('transfer_balance_status');
    const canSend = amount > 0 && amount <= currentBalance;
    if (statusEl) {
        statusEl.textContent = amount <= 0 ? 'Select amount' : (canSend ? 'Enough balance to send' : 'Insufficient balance');
        statusEl.classList.remove('text-emerald-700', 'text-red-600', 'text-slate-700');
        if (amount <= 0) statusEl.classList.add('text-slate-700');
        else if (canSend) statusEl.classList.add('text-emerald-700');
        else statusEl.classList.add('text-red-600');
    }

    if (amountError) {
        amountError.classList.toggle('hidden', canSend || amount <= 0);
    }

    if (amountInput) {
        amountInput.setAttribute('data-max', currentBalance.toString());
        amountInput.setAttribute('title', `Available balance: UGX ${formatNumber(currentBalance)}`);
    }
}

function formatNumber(num) {
    return parseFloat(num || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
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

function loadMemberBalance() {
    const select = document.getElementById('member_id');
    const option = select.options[select.selectedIndex];
    
    if (option.value) {
        const memberData = MEMBER_DATA[option.value] || null;
        const resolvedBalance = memberData?.balance ?? parseFloat(option.dataset.balance);
        const resolvedSavings = memberData?.savings ?? parseFloat(option.dataset.savings);
        currentBalance = Number.isFinite(resolvedBalance) ? resolvedBalance : 0;
        totalSavings = Number.isFinite(resolvedSavings) ? resolvedSavings : 0;

        updateMemberSnapshot(option.value, memberData);
        const selectedType = document.getElementById('type')?.value || 'default';
        const selectedCategory = document.getElementById('category')?.value || 'default';
        updateCategoryControls(selectedType, selectedCategory);
        
        document.getElementById('current_balance').textContent = formatNumber(currentBalance);
        document.getElementById('total_savings').textContent = formatNumber(totalSavings);
        document.getElementById('transaction_count').textContent = '0';
        document.getElementById('balance_info').classList.remove('hidden');
        document.getElementById('transaction_summary').classList.remove('hidden');
        calculateTransaction();
        updateTransferPreview();
    } else {
        document.getElementById('balance_info').classList.add('hidden');
        document.getElementById('transaction_summary').classList.add('hidden');
        updateMemberSnapshot(null, null);
    }
}

function calculateTransaction() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const fee = parseFloat(document.getElementById('fee').value) || 0;
    const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
    const commission = parseFloat(document.getElementById('commission').value) || 0;
    const type = document.getElementById('type').value;
    
    const totalCharges = fee + tax + commission;
    let netAmount = amount;
    let balanceAfter = currentBalance;
    
    if (type === 'withdrawal') {
        netAmount = amount - totalCharges;
        balanceAfter = currentBalance - netAmount;
    } else if (type === 'deposit') {
        balanceAfter = currentBalance + amount;
    } else if (type === 'transfer') {
        netAmount = amount - totalCharges;
        balanceAfter = currentBalance - netAmount;
    }
    
    document.getElementById('net_amount_display').value = formatNumber(netAmount);
    document.getElementById('total_charges_display').value = formatNumber(totalCharges);
    document.getElementById('balance_after').textContent = formatNumber(balanceAfter);
    
    // Update summary
    document.getElementById('summary_amount').textContent = formatNumber(amount);
    document.getElementById('summary_charges').textContent = formatNumber(totalCharges);
    document.getElementById('summary_net').textContent = formatNumber(netAmount);
    document.getElementById('summary_balance').textContent = formatNumber(balanceAfter);

    updateTransferPreview();
    updateWithdrawalControls();
    updateLoanRepaymentAmount();

    const submitButton = document.getElementById('transaction_submit_button');
    if (submitButton && type === 'transfer') {
        const canSend = amount > 0 && amount <= currentBalance;
        submitButton.disabled = !canSend;
        submitButton.classList.toggle('opacity-50', !canSend);
        submitButton.classList.toggle('cursor-not-allowed', !canSend);
    } else if (submitButton && type !== 'withdrawal') {
        submitButton.disabled = false;
        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const channel = document.getElementById('channel');
    if (channel) {
        defaultChannelOptions = Array.from(channel.options)
            .map((option) => option.textContent)
            .filter((text) => text && text !== 'Select Channel');
    }
    applyTransactionMode();
    applyPaymentMethod();
    syncTransferMembers();

    const memberSelect = document.getElementById('member_id');
    if (memberSelect && memberSelect.value) {
        loadMemberBalance();
    }

    const previewButton = document.getElementById('transaction_preview_button');
    const previewModal = document.getElementById('transaction_preview_modal');
    const previewOverlay = document.getElementById('preview_overlay');
    const closeTop = document.getElementById('preview_close_top');
    const closeBottom = document.getElementById('preview_close_bottom');
    const printButton = document.getElementById('preview_print_button');

    function closePreview() {
        if (previewModal) previewModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openPreview() {
        if (!previewModal) return;
        populatePreview();
        previewModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    if (previewButton) previewButton.addEventListener('click', openPreview);
    if (closeTop) closeTop.addEventListener('click', closePreview);
    if (closeBottom) closeBottom.addEventListener('click', closePreview);
    if (printButton) {
        printButton.addEventListener('click', () => {
            populatePreview();
            buildPrintDocument();
            const printRoot = document.getElementById('print_root');
            if (printRoot) printRoot.classList.remove('hidden');
            setTimeout(() => window.print(), 50);
        });
    }
    window.addEventListener('beforeprint', () => {
        populatePreview();
        buildPrintDocument();
        const printRoot = document.getElementById('print_root');
        if (printRoot) printRoot.classList.remove('hidden');
    });
    window.addEventListener('afterprint', () => {
        const printRoot = document.getElementById('print_root');
        if (printRoot) printRoot.classList.add('hidden');
    });
    if (previewOverlay) previewOverlay.addEventListener('click', closePreview);
});

function populatePreview() {
    const type = document.getElementById('type')?.value || 'select';
    const category = document.getElementById('category')?.value || 'select';
    const memberSelect = document.getElementById('member_id');
    const memberLabel = memberSelect?.options[memberSelect.selectedIndex]?.textContent || '--';
    const amount = parseFloat(document.getElementById('amount')?.value) || 0;
    const fee = parseFloat(document.getElementById('fee')?.value) || 0;
    const tax = parseFloat(document.getElementById('tax_amount')?.value) || 0;
    const commission = parseFloat(document.getElementById('commission')?.value) || 0;
    const totalCharges = fee + tax + commission;
    const net = parseFloat(document.getElementById('net_amount_display')?.value.replace(/,/g, '')) || 0;
    const balanceAfter = document.getElementById('balance_after')?.textContent || '0.00';
    const paymentMethod = document.getElementById('payment_method')?.value || '--';
    const channel = document.getElementById('channel')?.value || '--';
    const reference = document.querySelector('[name="reference"]')?.value || '--';
    const receipt = document.querySelector('[name="receipt_number"]')?.value || '--';
    const batch = document.querySelector('[name="batch_id"]')?.value || '--';
    const location = document.querySelector('[name="location"]')?.value || '--';
    const currency = document.querySelector('[name="currency"]')?.value || '--';
    const priority = document.querySelector('[name="priority"]')?.value || '--';
    const transactionDate = document.querySelector('[name="transaction_date"]')?.value || '--';
    const scheduledDate = document.querySelector('[name="scheduled_at"]')?.value || '--';
    const notification = document.getElementById('notification_sent')?.checked ? 'Yes' : 'No';
    const description = document.getElementById('description')?.value || '--';
    const notes = document.getElementById('notes')?.value || '--';
    const toMember = document.getElementById('transfer_to_member_id');
    const toLabel = toMember?.options[toMember.selectedIndex]?.textContent || '';
    const loanId = document.getElementById('loan_id')?.value || '';
    const loanApplied = document.getElementById('loan_applied_amount')?.value || '';
    const loanExcess = document.getElementById('loan_excess_to_savings')?.value || '';

    const previewTitle = document.getElementById('preview_title');
    if (previewTitle) previewTitle.textContent = `${type.toUpperCase()} - ${category.toUpperCase()}`;

    const setText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    };

    setText('preview_type', type || '--');
    setText('preview_category', category || '--');
    setText('preview_member', memberLabel || '--');
    const memberId = memberSelect?.value || '--';
    setText('preview_member_id', memberId ? `Member ID: ${memberId}` : 'Member ID: --');
    setText('preview_amount', `UGX ${formatNumber(amount)}`);
    setText('preview_charges', `UGX ${formatNumber(totalCharges)}`);
    setText('preview_net', `UGX ${formatNumber(net)}`);
    setText('preview_balance_after', `UGX ${balanceAfter}`);
    setText('preview_payment', `${paymentMethod}${channel !== '--' ? ` / ${channel}` : ''}`);
    setText('preview_channel', channel);
    setText('preview_channel_dup', channel);
    setText('preview_reference', reference);
    setText('preview_receipt', receipt);
    setText('preview_batch', batch);
    setText('preview_location', location);
    setText('preview_currency', currency);
    setText('preview_priority', priority);
    setText('preview_transaction_date', transactionDate);
    setText('preview_scheduled_date', scheduledDate);
    setText('preview_notification', notification);
    setText('preview_description', description);
    setText('preview_notes', notes);

    const special = document.getElementById('preview_special');
    if (special) {
        if (type === 'transfer') {
            special.textContent = `Transfer from ${memberLabel} to ${toLabel || 'recipient'} for UGX ${formatNumber(amount)}.`;
        } else if (category === 'loan_repayment') {
            const appliedValue = parseFloat(loanApplied) || 0;
            const excessValue = parseFloat(loanExcess) || 0;
            const applied = `UGX ${formatNumber(appliedValue)}`;
            const excess = `UGX ${formatNumber(excessValue)}`;
            special.textContent = `Loan ${loanId || '--'} repayment. Applied: ${applied}. Excess to savings: ${excess}.`;
        } else {
            special.textContent = 'No special details.';
        }
    }

    const previewPhoto = document.getElementById('preview_member_photo');
    if (previewPhoto) {
        const photo = memberId && MEMBER_DATA[memberId]?.profile_picture_url
            ? MEMBER_DATA[memberId].profile_picture_url
            : previewPhoto.getAttribute('data-default');
        if (photo) previewPhoto.src = photo;
    }
}

function buildPrintDocument() {
    const printRoot = document.getElementById('print_root');
    if (!printRoot) return;

    let data = null;
    try {
        data = collectTransactionData();
    } catch (error) {
        console.error('Print data build failed:', error);
    }
    const logoUrl = "{{ asset('assets/images/logo.png') }}";
    const appName = "{{ config('app.name') }}";
    const contactLine = document.getElementById('preview_contact_line')?.textContent?.trim() || '';
    const memberPhoto = data?.memberPhoto || "{{ asset('images/default-avatar.svg') }}";
    const previewBody = document.getElementById('print_body');
    const previewHtml = previewBody?.innerHTML?.trim() || '';

    const looksEmpty = data
        && data.memberName === '--'
        && data.memberId === '--'
        && data.amountLabel === 'UGX 0.00'
        && data.type === '--'
        && data.category === '--';

    if (previewHtml) {
        printRoot.innerHTML = `
            <div style="font-family: 'Segoe UI', Arial, sans-serif; color:#0f172a;">
                <div class="print-page">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:12px; margin-bottom:16px;">
                        <div style="display:flex; gap:12px; align-items:center;">
                            <img src="${logoUrl}" alt="Logo" style="width:56px;height:56px;border-radius:12px;border:1px solid #e2e8f0;object-fit:contain;">
                            <div>
                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; font-weight:600;">Transaction Summary</div>
                                <div style="font-size:20px; font-weight:700;">${appName}</div>
                                <div style="font-size:11px; color:#64748b;">${contactLine}</div>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:12px; color:#64748b;">Generated</div>
                            <div style="font-size:12px; font-weight:600;">${new Date().toLocaleString()}</div>
                        </div>
                    </div>
                    ${previewHtml}
                </div>
            </div>
        `;
        return;
    }

    if (!data || (looksEmpty && previewBody) || (!previewBody && looksEmpty)) {
        const emptyHtml = '<div style="padding:12px; color:#64748b;">No preview data available.</div>';
        printRoot.innerHTML = `
            <div style="font-family: 'Segoe UI', Arial, sans-serif; color:#0f172a;">
                <div class="print-page">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:12px; margin-bottom:16px;">
                        <div style="display:flex; gap:12px; align-items:center;">
                            <img src="${logoUrl}" alt="Logo" style="width:56px;height:56px;border-radius:12px;border:1px solid #e2e8f0;object-fit:contain;">
                            <div>
                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; font-weight:600;">Transaction Summary</div>
                                <div style="font-size:20px; font-weight:700;">${appName}</div>
                                <div style="font-size:11px; color:#64748b;">${contactLine}</div>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:12px; color:#64748b;">Generated</div>
                            <div style="font-size:12px; font-weight:600;">${new Date().toLocaleString()}</div>
                        </div>
                    </div>
                    ${emptyHtml}
                </div>
            </div>
        `;
        return;
    }

    printRoot.innerHTML = `
        <div style="font-family: 'Segoe UI', Arial, sans-serif; color:#0f172a;">
            <div class="print-page">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:12px; margin-bottom:16px;">
                <div style="display:flex; gap:12px; align-items:center;">
                    <img src="${logoUrl}" alt="Logo" style="width:56px;height:56px;border-radius:12px;border:1px solid #e2e8f0;object-fit:contain;">
                    <div>
                        <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; font-weight:600;">Transaction Summary</div>
                        <div style="font-size:20px; font-weight:700;">${appName}</div>
                        <div style="font-size:11px; color:#64748b;">${contactLine}</div>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:12px; color:#64748b;">Generated</div>
                    <div style="font-size:12px; font-weight:600;">${new Date().toLocaleString()}</div>
                </div>
            </div>
            <div style="display:flex; gap:16px; align-items:center; border:1px solid #e2e8f0; border-radius:12px; padding:12px; margin-bottom:16px;">
                <img src="${memberPhoto}" alt="Member" style="width:60px;height:60px;border-radius:12px;border:1px solid #e2e8f0;object-fit:cover;">
                <div>
                    <div style="font-size:12px; text-transform:uppercase; color:#64748b; font-weight:600;">Member</div>
                    <div style="font-size:16px; font-weight:700;">${data.memberName}</div>
                    <div style="font-size:12px; color:#64748b;">Member ID: ${data.memberId}</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap:12px; margin-bottom:16px;">
                ${printCard('Type', data.type)}
                ${printCard('Category', data.category)}
                ${printCard('Amount', data.amountLabel)}
                ${printCard('Net', data.netLabel)}
            </div>

            <div style="display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap:12px; margin-bottom:16px;">
                ${printCard('Charges', data.chargesLabel)}
                ${printCard('Balance After', data.balanceAfterLabel)}
                ${printCard('Currency', data.currency)}
                ${printCard('Priority', data.priority)}
            </div>

            <div style="display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:12px; margin-bottom:16px;">
                ${printCard('Transfer / Loan', data.special)}
                ${printCard('Payment', data.payment)}
            </div>
            </div>

            <div class="print-page">
            <div style="display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:12px; margin-bottom:16px;">
                ${printCard('Transaction Date', data.transactionDate)}
                ${printCard('Scheduled Date', data.scheduledDate)}
                ${printCard('Notification', data.notification)}
            </div>

            <div style="display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:12px; margin-bottom:16px;">
                ${printCard('Reference', data.reference)}
                ${printCard('Receipt', data.receipt)}
                ${printCard('Batch ID', data.batch)}
            </div>

            <div style="display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:12px; margin-bottom:16px;">
                ${printCard('Location', data.location)}
                ${printCard('Channel', data.channel)}
                ${printCard('Channel (Alt)', data.channel)}
            </div>

            ${printBlock('Description', data.description)}
            ${printBlock('Internal Notes', data.notes)}
            </div>
        </div>
    `;
}

function printCard(label, value) {
    return `
        <div style="border:1px solid #e2e8f0; border-radius:12px; padding:10px; background:#f8fafc;">
            <div style="font-size:10px; text-transform:uppercase; color:#64748b; font-weight:600;">${label}</div>
            <div style="font-size:13px; font-weight:700; color:#0f172a;">${value}</div>
        </div>
    `;
}

function printBlock(label, value) {
    return `
        <div style="border:1px solid #e2e8f0; border-radius:12px; padding:12px; margin-bottom:12px; background:#fff;">
            <div style="font-size:10px; text-transform:uppercase; color:#64748b; font-weight:600;">${label}</div>
            <div style="font-size:12px; color:#0f172a; margin-top:6px;">${value}</div>
        </div>
    `;
}

function collectTransactionData() {
    const previewText = (id) => document.getElementById(id)?.textContent?.trim() || '';
    const cleanValue = (value) => {
        const trimmed = (value ?? '').toString().trim();
        return trimmed && trimmed !== '--' ? trimmed : '';
    };
    const readMoney = (text) => {
        const numeric = (text || '').toString().replace(/[^0-9.-]/g, '');
        const parsed = parseFloat(numeric);
        return Number.isFinite(parsed) ? parsed : NaN;
    };

    const type = cleanValue(document.getElementById('type')?.value) || cleanValue(previewText('preview_type')) || '--';
    const category = cleanValue(document.getElementById('category')?.value) || cleanValue(previewText('preview_category')) || '--';
    const memberSelect = document.getElementById('member_id');
    const memberName = cleanValue(memberSelect?.options[memberSelect.selectedIndex]?.textContent) || cleanValue(previewText('preview_member')) || '--';
    const memberId = cleanValue(memberSelect?.value) || cleanValue(previewText('preview_member_id')?.replace('Member ID:', '').trim()) || '--';

    const amountRaw = parseFloat(document.getElementById('amount')?.value);
    const amountFallback = readMoney(previewText('preview_amount'));
    const amount = Number.isFinite(amountRaw) && amountRaw > 0 ? amountRaw : (Number.isFinite(amountFallback) ? amountFallback : 0);

    const fee = parseFloat(document.getElementById('fee')?.value) || 0;
    const tax = parseFloat(document.getElementById('tax_amount')?.value) || 0;
    const commission = parseFloat(document.getElementById('commission')?.value) || 0;
    const totalChargesRaw = fee + tax + commission;
    const chargesFallback = readMoney(previewText('preview_charges'));
    const totalCharges = totalChargesRaw > 0 ? totalChargesRaw : (Number.isFinite(chargesFallback) ? chargesFallback : 0);

    const netRaw = parseFloat(document.getElementById('net_amount_display')?.value.replace(/,/g, ''));
    const netFallback = readMoney(previewText('preview_net'));
    const net = Number.isFinite(netRaw) && netRaw > 0 ? netRaw : (Number.isFinite(netFallback) ? netFallback : 0);

    const balanceAfter = cleanValue(document.getElementById('balance_after')?.textContent) || cleanValue(previewText('preview_balance_after').replace('UGX', '').trim()) || '0.00';

    const paymentMethod = cleanValue(document.getElementById('payment_method')?.value) || '';
    const channel = cleanValue(document.getElementById('channel')?.value) || cleanValue(previewText('preview_channel')) || '--';
    const payment = paymentMethod
        ? `${paymentMethod}${channel !== '--' ? ` / ${channel}` : ''}`
        : (cleanValue(previewText('preview_payment')) || '--');

    const reference = cleanValue(document.querySelector('[name="reference"]')?.value) || cleanValue(previewText('preview_reference')) || '--';
    const receipt = cleanValue(document.querySelector('[name="receipt_number"]')?.value) || cleanValue(previewText('preview_receipt')) || '--';
    const batch = cleanValue(document.querySelector('[name="batch_id"]')?.value) || cleanValue(previewText('preview_batch')) || '--';
    const location = cleanValue(document.querySelector('[name="location"]')?.value) || cleanValue(previewText('preview_location')) || '--';
    const currency = cleanValue(document.querySelector('[name="currency"]')?.value) || cleanValue(previewText('preview_currency')) || '--';
    const priority = cleanValue(document.querySelector('[name="priority"]')?.value) || cleanValue(previewText('preview_priority')) || '--';
    const transactionDate = cleanValue(document.querySelector('[name="transaction_date"]')?.value) || cleanValue(previewText('preview_transaction_date')) || '--';
    const scheduledDate = cleanValue(document.querySelector('[name="scheduled_at"]')?.value) || cleanValue(previewText('preview_scheduled_date')) || '--';
    const notification = document.getElementById('notification_sent')?.checked ? 'Yes' : (cleanValue(previewText('preview_notification')) || 'No');
    const description = cleanValue(document.getElementById('description')?.value) || cleanValue(previewText('preview_description')) || '--';
    const notes = cleanValue(document.getElementById('notes')?.value) || cleanValue(previewText('preview_notes')) || '--';

    const loanId = document.getElementById('loan_id')?.value || '';
    const loanApplied = parseFloat(document.getElementById('loan_applied_amount')?.value) || 0;
    const loanExcess = parseFloat(document.getElementById('loan_excess_to_savings')?.value) || 0;
    const toMember = document.getElementById('transfer_to_member_id');
    const toLabel = toMember?.options[toMember.selectedIndex]?.textContent || '';

    let special = cleanValue(previewText('preview_special')) || 'No special details.';
    if (type === 'transfer') {
        special = `Transfer from ${memberName} to ${toLabel || 'recipient'} for UGX ${formatNumber(amount)}.`;
    } else if (category === 'loan_repayment') {
        special = `Loan ${loanId || '--'} repayment. Applied: UGX ${formatNumber(loanApplied)}. Excess to savings: UGX ${formatNumber(loanExcess)}.`;
    }

    const memberPhoto = memberId && MEMBER_DATA[memberId]?.profile_picture_url
        ? MEMBER_DATA[memberId].profile_picture_url
        : "{{ asset('images/default-avatar.svg') }}";

    return {
        type: type || '--',
        category: category || '--',
        memberName: memberName || '--',
        memberId: memberId || '--',
        memberPhoto,
        amountLabel: `UGX ${formatNumber(amount)}`,
        chargesLabel: `UGX ${formatNumber(totalCharges)}`,
        netLabel: `UGX ${formatNumber(net)}`,
        balanceAfterLabel: `UGX ${balanceAfter}`,
        currency,
        priority,
        payment,
        channel,
        reference,
        receipt,
        batch,
        location,
        transactionDate,
        scheduledDate,
        notification,
        description,
        notes,
        special,
    };
}
</script>