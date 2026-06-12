@php
    $systemName = setting('system_name', setting('company_name', config('bss.system_name', 'BSS Investment Group')));
    $systemEmail = setting('system_email', setting('notification_email', ''));
    $systemPhone = setting('system_phone', '');
    $systemAddress = setting('address', '');
    $logoUrl = asset('assets/images/logo.png');
@endphp

<div class="min-h-screen bg-slate-100 p-4 md:p-6">
    <div class="no-print mb-4 flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Contribution Receipt</p>
            <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900">One-page print format</h2>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.contributions.show', [$fundraising->id, $contribution->id]) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">
                Back
            </a>
        </div>
    </div>

    <div class="mx-auto max-w-3xl print-card">
        <div class="overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-xl">
            <div class="bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-900 px-6 py-5 text-white">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl bg-white/10 ring-1 ring-white/15">
                        <img src="{{ $logoUrl }}" alt="{{ $systemName }} Logo" class="h-full w-full object-contain p-2" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <i class="fas fa-landmark hidden text-2xl text-cyan-300"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="truncate text-xl font-black tracking-tight md:text-2xl">{{ $systemName }}</h1>
                        <p class="text-sm text-slate-300">Contribution Receipt</p>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ $systemEmail ?: 'No email configured' }}@if($systemPhone) · {{ $systemPhone }}@endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-5">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-500">Receipt Number</p>
                        <p class="mt-2 text-sm font-black text-slate-900">{{ $contribution->receipt_number ?? 'N/A' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-500">Contribution ID</p>
                        <p class="mt-2 text-sm font-black text-slate-900">{{ $contribution->contribution_number ?? 'N/A' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-500">Amount</p>
                        <p class="mt-2 text-lg font-black text-emerald-600">UGX {{ number_format($contribution->amount, 0) }}</p>
                    </div>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-500">Campaign</p>
                        <p class="mt-2 text-sm font-bold text-slate-900">{{ $fundraising->title }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $fundraising->campaign_id }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-500">Member</p>
                        <p class="mt-2 text-sm font-bold text-slate-900">{{ $contribution->member?->full_name ?? 'Guest' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $contribution->contributor_email ?? 'N/A' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-500">Contributor</p>
                        <p class="mt-2 text-sm font-bold text-slate-900">{{ $contribution->is_anonymous ? 'Anonymous' : ($contribution->contributor_name ?? 'Anonymous') }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $contribution->contributor_phone ?? 'N/A' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-500">Transaction</p>
                        <p class="mt-2 text-sm font-bold text-slate-900">{{ $contribution->transaction?->transaction_number ?? 'N/A' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $contribution->paymentMethod?->display_name ?? $contribution->paymentMethod?->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-500">Date</p>
                        <p class="mt-2 text-sm font-bold text-slate-900">{{ optional($contribution->contribution_date)->format('M d, Y') }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-500">Source</p>
                        <p class="mt-2 text-sm font-bold text-slate-900">{{ in_array(optional($contribution->transaction?->transactionType)->name, ['transfer', 'withdrawal'], true) ? 'Savings Transfer' : 'Deposit' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-500">Issued By</p>
                        <p class="mt-2 text-sm font-bold text-slate-900">{{ $contribution->receiptIssuer?->username ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-500">Notes</p>
                    <p class="mt-2 text-sm leading-6 text-slate-700">{{ $contribution->notes ?? 'N/A' }}</p>
                </div>

                <div class="mt-5 flex items-center justify-between gap-3 border-t border-slate-200 pt-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">Receipt Status</p>
                        <p class="mt-1 text-sm font-bold {{ $contribution->receipt_issued ? 'text-emerald-600' : 'text-amber-600' }}">{{ $contribution->receipt_issued ? 'Issued' : 'Pending' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Generated {{ now()->format('M d, Y H:i') }}</p>
                        <p class="text-xs text-slate-500">Authorized contribution record</p>
                        @if($systemAddress)
                            <p class="text-xs text-slate-500">{{ $systemAddress }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4;
        margin: 10mm;
    }

    html, body {
        background: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .no-print {
        display: none !important;
    }

    .print-card {
        max-width: 100% !important;
    }

    .shadow-xl {
        box-shadow: none !important;
    }

    .overflow-hidden {
        break-inside: avoid;
    }

    .rounded-\[24px\] {
        border-radius: 14px !important;
    }
}
</style>