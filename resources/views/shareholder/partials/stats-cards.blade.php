<div x-data="{ showAllMobileStats: false }" class="space-y-3">
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-xl border border-cyan-200 bg-cyan-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Portfolio Value</p>
            <p id="statPortfolioValue" class="mt-2 text-2xl font-black text-slate-900">UGX {{ number_format($stats['portfolioValue'] ?? 0, 0) }}</p>
            <p class="mt-1 text-xs text-slate-600">Change: <span id="statPortfolioChangePct">{{ number_format($stats['portfolioChangePct'] ?? 0, 1) }}%</span></p>
        </div>

        <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Shares</p>
            <p id="statTotalShares" class="mt-2 text-2xl font-black text-slate-900">{{ number_format($stats['totalShares'] ?? 0) }}</p>
            <p class="mt-1 text-xs text-slate-600">Avg Price: <span id="statAverageSharePrice">UGX {{ number_format($stats['averageSharePrice'] ?? 0, 0) }}</span></p>
        </div>

        <div class="rounded-xl border border-purple-200 bg-purple-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-purple-700">Dividends</p>
            <p id="statDividendsPaid" class="mt-2 text-2xl font-black text-slate-900">UGX {{ number_format($stats['dividendsPaid'] ?? 0, 0) }}</p>
            <p class="mt-1 text-xs text-slate-600">Pending: <span id="statDividendsPending">UGX {{ number_format($stats['dividendsPending'] ?? 0, 0) }}</span></p>
        </div>

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Net Worth</p>
            <p id="statNetWorth" class="mt-2 text-2xl font-black text-slate-900">UGX {{ number_format($stats['netWorth'] ?? 0, 0) }}</p>
            <p class="mt-1 text-xs text-slate-600">Dividend Yield: <span id="statDividendYieldPct">{{ number_format($stats['dividendYieldPct'] ?? 0, 2) }}%</span></p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">30-Day Cash Flow</p>
            <p id="statNetCashFlow30" class="mt-2 text-2xl font-black text-slate-900">UGX {{ number_format($stats['netCashFlow30'] ?? 0, 0) }}</p>
            <p class="mt-1 text-xs text-slate-600">
                Deposits <span id="statDeposits30">{{ number_format($stats['deposits30'] ?? 0, 0) }}</span>
                | Withdrawals <span id="statWithdrawals30">{{ number_format($stats['withdrawals30'] ?? 0, 0) }}</span>
            </p>
        </div>

        <div class="hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:block" :class="showAllMobileStats ? '!block' : 'hidden sm:block'">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Savings</p>
            <p id="statSavingsBalance" class="mt-2 text-2xl font-black text-slate-900">UGX {{ number_format($stats['savingsBalance'] ?? 0, 0) }}</p>
        </div>

        <div class="hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:block" :class="showAllMobileStats ? '!block' : 'hidden sm:block'">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Outstanding Loan</p>
            <p id="statLoanOutstanding" class="mt-2 text-2xl font-black text-slate-900">UGX {{ number_format($stats['loanOutstanding'] ?? 0, 0) }}</p>
        </div>

        <div class="hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:block" :class="showAllMobileStats ? '!block' : 'hidden sm:block'">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Active Loans</p>
            <p id="statActiveLoans" class="mt-2 text-2xl font-black text-slate-900">{{ number_format($stats['activeLoans'] ?? 0) }}</p>
        </div>

        <div class="hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:block" :class="showAllMobileStats ? '!block' : 'hidden sm:block'">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Project Signals</p>
            <p id="statProjectSignals" class="mt-2 text-2xl font-black text-slate-900">{{ number_format($stats['activeProjects'] ?? 0) }}/{{ number_format($stats['totalProjects'] ?? 0) }}</p>
            <p class="mt-1 text-xs text-slate-600">Avg ROI: <span id="statAvgProjectRoi">{{ number_format($stats['avgProjectRoi'] ?? 0, 1) }}%</span></p>
        </div>

        <div class="hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:block" :class="showAllMobileStats ? '!block' : 'hidden sm:block'">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Opportunities</p>
            <p id="statActiveOpportunities" class="mt-2 text-2xl font-black text-slate-900">{{ number_format($stats['activeOpportunities'] ?? 0) }}</p>
            <p class="mt-1 text-xs text-slate-600">Target: <span id="statOpportunityTarget">UGX {{ number_format($stats['opportunityTarget'] ?? 0, 0) }}</span></p>
        </div>
    </div>

    <div class="sm:hidden">
        <button
            type="button"
            @click="showAllMobileStats = !showAllMobileStats"
            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm"
            x-text="showAllMobileStats ? 'Show fewer KPI cards' : 'Show more KPI cards'">
        </button>
    </div>
</div>
