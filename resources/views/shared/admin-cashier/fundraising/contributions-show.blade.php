<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-3 md:p-6">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.contributions', $fundraising->id) }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left text-blue-600"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Contribution Details</h2>
                <p class="text-gray-600 text-sm">{{ $fundraising->title }} ({{ $fundraising->campaign_id }})</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.contributions.edit', [$fundraising->id, $contribution->id]) }}" class="px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                <i class="fas fa-edit mr-2"></i>Edit Contribution
            </a>
            <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.contributions.print', [$fundraising->id, $contribution->id]) }}" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                <i class="fas fa-print mr-2"></i>Print
            </a>
            <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.show', $fundraising->id) }}" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                <i class="fas fa-eye mr-2"></i>View Campaign
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 max-w-4xl">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600">
            <h3 class="text-white text-lg font-bold flex items-center gap-2">
                <i class="fas fa-receipt"></i>
                Contribution #{{ $contribution->contribution_number ?? 'N/A' }}
            </h3>
        </div>
        @if(session('success'))
        <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        </div>
        @endif
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Linked Member</p>
                <p class="font-bold text-gray-900">{{ $contribution->member?->full_name ?? 'Guest' }}</p>
                @if($contribution->member?->member_number)
                    <p class="text-xs text-gray-500">{{ $contribution->member?->member_number }}</p>
                @endif
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Contributor Name</p>
                <p class="font-bold text-gray-900">{{ $contribution->is_anonymous ? 'Anonymous' : ($contribution->contributor_name ?? 'Anonymous') }}</p>
                @if($contribution->is_anonymous)
                    <p class="text-xs text-gray-500">Marked as anonymous</p>
                @endif
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Contributor Email</p>
                <p class="font-bold text-gray-900">{{ $contribution->contributor_email ?? 'N/A' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Contributor Phone</p>
                <p class="font-bold text-gray-900">{{ $contribution->contributor_phone ?? 'N/A' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Contributor Address</p>
                <p class="font-bold text-gray-900">{{ $contribution->contributor_address ?? 'N/A' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Amount</p>
                <p class="font-bold text-green-700">UGX {{ number_format($contribution->amount, 0) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Transaction #</p>
                <p class="font-bold text-gray-900">{{ $contribution->transaction?->transaction_number ?? 'N/A' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Payment Method</p>
                <p class="font-bold text-gray-900">{{ $contribution->paymentMethod?->display_name ?? $contribution->paymentMethod?->name ?? 'N/A' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Funding Source</p>
                <p class="font-bold text-gray-900">{{ in_array(optional($contribution->transaction?->transactionType)->name, ['transfer', 'withdrawal'], true) ? 'Savings Transfer' : 'Deposit' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Contribution Date</p>
                <p class="font-bold text-gray-900">{{ optional($contribution->contribution_date)->format('M d, Y') }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Receipt</p>
                <p class="font-bold text-gray-900">{{ $contribution->receipt_issued ? 'Issued' : 'Not Issued' }}</p>
                @if($contribution->receipt_number)
                    <p class="text-xs text-gray-500"># {{ $contribution->receipt_number }}</p>
                @endif
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Receipt Issued By</p>
                <p class="font-bold text-gray-900">{{ $contribution->receiptIssuer?->username ?? 'N/A' }}</p>
                @if($contribution->receipt_issued_at)
                    <p class="text-xs text-gray-500">{{ optional($contribution->receipt_issued_at)->format('M d, Y H:i') }}</p>
                @endif
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">Thank You Sent</p>
                <p class="font-bold text-gray-900">{{ $contribution->thank_you_sent ? 'Yes' : 'No' }}</p>
                @if($contribution->thank_you_sent_at)
                    <p class="text-xs text-gray-500">{{ optional($contribution->thank_you_sent_at)->format('M d, Y H:i') }}</p>
                @endif
            </div>
            <div class="p-4 bg-gray-50 rounded-xl md:col-span-2">
                <p class="text-xs text-gray-600 mb-1">Public Message</p>
                <p class="text-gray-800">{{ $contribution->message ?? 'N/A' }}</p>
                <p class="text-xs text-gray-500 mt-1">Visible: {{ $contribution->is_public_message ? 'Yes' : 'No' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl md:col-span-2">
                <p class="text-xs text-gray-600 mb-1">Notes</p>
                <p class="text-gray-800">{{ $contribution->notes ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>