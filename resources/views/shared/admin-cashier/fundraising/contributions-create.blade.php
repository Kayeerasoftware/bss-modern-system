<div class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 p-3 md:p-6">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.contributions', $fundraising->id) }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left text-green-600"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">Add Contribution</h2>
                <p class="text-gray-600 text-sm">{{ $fundraising->title }} ({{ $fundraising->campaign_id }})</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 max-w-3xl">
        <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600">
            <h3 class="text-white text-lg font-bold flex items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                Contribution Details
            </h3>
        </div>
        <form action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.contributions.store', $fundraising->id) }}" method="POST" class="p-6 space-y-5">
            @csrf

            @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4">
                <p class="font-bold mb-2">Please fix the following:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div id="member_snapshot" class="hidden rounded-2xl border border-slate-200 bg-white p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase text-slate-500 font-semibold">Member Snapshot</p>
                        <p class="text-lg font-bold text-gray-900" id="snapshot_member_name">Select a member</p>
                        <p class="text-xs text-gray-500" id="snapshot_member_id">Member ID: --</p>
                    </div>
                    <div class="text-sm text-slate-600" id="snapshot_status">Select a member to see finances.</div>
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
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Linked Member (Optional)</label>
                    <select name="member_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                        <option value="">External / Guest Contributor</option>
                        @foreach($members as $member)
                            @php
                                $fullName = trim((string) $member->full_name);
                                $label = $fullName !== '' ? $fullName : $member->member_number;
                                if ($member->member_number) {
                                    $label .= ' (' . $member->member_number . ')';
                                }
                            @endphp
                            <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-gray-500 mt-1">Select a member to link this contribution, or leave empty for a guest.</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Funding Source</label>
                    <select name="funding_source" id="funding_source" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" required>
                        <option value="deposit" @selected(old('funding_source', 'deposit') === 'deposit')>Deposit (Cash/Bank/Mobile)</option>
                        <option value="savings_transfer" @selected(old('funding_source') === 'savings_transfer')>Transfer From Savings</option>
                    </select>
                    <p class="text-[11px] text-gray-500 mt-1" id="funding_source_hint">Choose how the contribution is funded.</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Contributor Name</label>
                    <input type="text" name="contributor_name" value="{{ old('contributor_name') }}" list="contributors-list" autocomplete="off" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" required>
                    <p class="text-[11px] text-gray-500 mt-1">Start typing to see members/users. You can also enter a new name.</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Contributor Email</label>
                    <input type="email" name="contributor_email" value="{{ old('contributor_email') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Contributor Phone</label>
                    <input type="text" name="contributor_phone" value="{{ old('contributor_phone') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Contributor Address</label>
                    <input type="text" name="contributor_address" value="{{ old('contributor_address') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_anonymous" value="0">
                    <input type="checkbox" id="is_anonymous" name="is_anonymous" value="1" class="rounded text-green-600" @checked(old('is_anonymous'))>
                    <label for="is_anonymous" class="text-sm font-semibold text-gray-700">Mark as Anonymous</label>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Amount (UGX)</label>
                    <input type="number" name="amount" min="100" step="1" value="{{ old('amount') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" required>
                    <p id="amount_warning" class="text-[11px] text-red-600 mt-1 hidden">Amount exceeds available savings.</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Contribution Date</label>
                    <input type="date" name="contribution_date" value="{{ old('contribution_date', now()->format('Y-m-d')) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method_id" id="payment_method_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" required>
                        <option value="">Select method</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>{{ $method->display_name ?? $method->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Receipt Number</label>
                    <input type="text" name="receipt_number" value="{{ old('receipt_number') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="receipt_issued" value="0">
                    <input type="checkbox" id="receipt_issued" name="receipt_issued" value="1" class="rounded text-green-600" @checked(old('receipt_issued'))>
                    <label for="receipt_issued" class="text-sm font-semibold text-gray-700">Receipt Issued</label>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Receipt Issued At</label>
                    <input type="datetime-local" name="receipt_issued_at" value="{{ old('receipt_issued_at') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Receipt Issued By</label>
                    <select name="receipt_issued_by" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                        <option value="">Auto (Current User)</option>
                        @foreach($receiptIssuers as $issuer)
                            <option value="{{ $issuer->id }}" @selected(old('receipt_issued_by') == $issuer->id)>{{ $issuer->username ?? $issuer->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="thank_you_sent" value="0">
                    <input type="checkbox" id="thank_you_sent" name="thank_you_sent" value="1" class="rounded text-green-600" @checked(old('thank_you_sent'))>
                    <label for="thank_you_sent" class="text-sm font-semibold text-gray-700">Thank You Sent</label>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Thank You Sent At</label>
                    <input type="datetime-local" name="thank_you_sent_at" value="{{ old('thank_you_sent_at') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Public Message</label>
                <textarea name="message" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">{{ old('message') }}</textarea>
                <div class="flex items-center gap-2 mt-2">
                    <input type="hidden" name="is_public_message" value="0">
                    <input type="checkbox" id="is_public_message" name="is_public_message" value="1" class="rounded text-green-600" @checked(old('is_public_message', true))>
                    <label for="is_public_message" class="text-sm font-semibold text-gray-700">Show message publicly</label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="4" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-2">
                <button id="contribution_submit" type="submit" class="px-5 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-bold hover:shadow-lg transition-all">
                    <i class="fas fa-save mr-2"></i>Save Contribution
                </button>
                <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.contributions', $fundraising->id) }}" class="px-5 py-2 bg-white text-gray-700 rounded-lg font-bold border border-gray-200 hover:shadow-lg transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<datalist id="contributors-list">
    @foreach($contributors as $contributor)
        <option value="{{ $contributor['name'] }}">{{ $contributor['label'] }}</option>
    @endforeach
</datalist>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var contributors = @json($contributors);
    var members = @json($membersLite);
    var memberSummaries = @json($memberSummaries);
    var nameInput = document.querySelector('input[name="contributor_name"]');
    var emailInput = document.querySelector('input[name="contributor_email"]');
    var phoneInput = document.querySelector('input[name="contributor_phone"]');
    var memberSelect = document.querySelector('select[name="member_id"]');
    var fundingSelect = document.getElementById('funding_source');
    var amountInput = document.querySelector('input[name="amount"]');
    var snapshot = document.getElementById('member_snapshot');
    var snapshotName = document.getElementById('snapshot_member_name');
    var snapshotId = document.getElementById('snapshot_member_id');
    var snapshotBalance = document.getElementById('snapshot_balance');
    var snapshotSavings = document.getElementById('snapshot_savings');
    var snapshotLoan = document.getElementById('snapshot_loan_balance');
    var snapshotLoanStatus = document.getElementById('snapshot_loan_status');
    var snapshotShares = document.getElementById('snapshot_share_value');
    var snapshotShareUnits = document.getElementById('snapshot_share_units');
    var snapshotStatus = document.getElementById('snapshot_status');
    var fundingHint = document.getElementById('funding_source_hint');
    var amountWarning = document.getElementById('amount_warning');
    var paymentMethodSelect = document.getElementById('payment_method_id');
    var internalPaymentMethodId = @json($internalPaymentMethodId);
    var submitButton = document.getElementById('contribution_submit');

    if (!nameInput) return;

    function applyMatch() {
        var name = (nameInput.value || '').trim().toLowerCase();
        if (!name) return;
        var match = contributors.find(function (c) {
            return (c.name || '').trim().toLowerCase() === name;
        });
        if (!match) return;
        if (emailInput && !emailInput.value && match.email) {
            emailInput.value = match.email;
        }
        if (phoneInput && !phoneInput.value && match.phone) {
            phoneInput.value = match.phone;
        }
        if (memberSelect && !memberSelect.value && match.member_id) {
            memberSelect.value = String(match.member_id);
        }
    }

    if (memberSelect) {
        memberSelect.addEventListener('change', function () {
            var id = memberSelect.value;
            if (!id) {
                if (snapshot) snapshot.classList.add('hidden');
                return;
            }
            var member = members.find(function (m) {
                return String(m.id) === String(id);
            });
            if (!member) return;
            if (nameInput && !nameInput.value && member.name) {
                nameInput.value = member.name;
            }
            if (emailInput && !emailInput.value && member.email) {
                emailInput.value = member.email;
            }
            if (phoneInput && !phoneInput.value && member.phone) {
                phoneInput.value = member.phone;
            }
            updateSnapshot(id);
        });
    }

    function updateSnapshot(memberId) {
        if (!snapshot) return;
        var summary = memberSummaries[String(memberId)];
        if (!summary) {
            snapshot.classList.add('hidden');
            return;
        }
        snapshot.classList.remove('hidden');
        if (snapshotName) snapshotName.textContent = summary.full_name || 'Member';
        if (snapshotId) snapshotId.textContent = 'Member ID: ' + (summary.member_id || '--');
        if (snapshotBalance) snapshotBalance.textContent = (summary.balance || 0).toLocaleString();
        if (snapshotSavings) snapshotSavings.textContent = (summary.savings_balance || 0).toLocaleString();
        if (snapshotLoan) snapshotLoan.textContent = (summary.loan?.remaining_balance || 0).toLocaleString();
        if (snapshotLoanStatus) snapshotLoanStatus.textContent = summary.loan ? (summary.loan.status || 'Active loan') : 'No active loan';
        if (snapshotShares) snapshotShares.textContent = (summary.shares?.total_value || 0).toLocaleString();
        if (snapshotShareUnits) snapshotShareUnits.textContent = (summary.shares?.total_shares || 0) + ' shares';
        updateFundingHint();
    }

    function updateFundingHint() {
        if (!fundingHint) return;
        var source = fundingSelect ? fundingSelect.value : 'deposit';
        if (source === 'savings_transfer') {
            fundingHint.textContent = 'Transfers will reduce the member savings balance and require enough funds.';
            if (paymentMethodSelect && internalPaymentMethodId) {
                paymentMethodSelect.value = String(internalPaymentMethodId);
            }
            if (memberSelect) {
                memberSelect.setAttribute('required', 'required');
            }
        } else {
            fundingHint.textContent = 'Deposits can be cash, bank, or mobile money.';
            if (memberSelect) {
                memberSelect.removeAttribute('required');
            }
        }
        if (snapshotStatus && memberSelect && memberSelect.value) {
            var summary = memberSummaries[String(memberSelect.value)];
            if (summary && source === 'savings_transfer') {
                var amount = parseFloat(amountInput?.value || 0);
                var available = parseFloat(summary.savings_balance || 0);
                if (available <= 0 && summary.savings) {
                    available = parseFloat(summary.savings || 0);
                }
                if (amountInput) {
                    amountInput.max = available > 0 ? String(available) : '';
                }
                snapshotStatus.textContent = amount > 0
                    ? (amount <= available ? 'Enough savings for transfer.' : 'Insufficient savings for transfer.')
                    : 'Enter an amount to check savings.';
                if (amountWarning) {
                    amountWarning.classList.toggle('hidden', !(amount > available));
                }
                if (submitButton) {
                    submitButton.disabled = amount > available || !memberSelect.value;
                    submitButton.classList.toggle('opacity-50', submitButton.disabled);
                    submitButton.classList.toggle('cursor-not-allowed', submitButton.disabled);
                }
            } else {
                snapshotStatus.textContent = 'Member financial overview.';
                if (amountWarning) {
                    amountWarning.classList.add('hidden');
                }
                if (amountInput) {
                    amountInput.removeAttribute('max');
                }
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }
    }

    if (fundingSelect) {
        fundingSelect.addEventListener('change', updateFundingHint);
    }
    if (amountInput) {
        amountInput.addEventListener('input', updateFundingHint);
    }

    updateFundingHint();

    nameInput.addEventListener('change', applyMatch);
    nameInput.addEventListener('blur', applyMatch);

    if (memberSelect && memberSelect.value) {
        updateSnapshot(memberSelect.value);
    }
});
</script>