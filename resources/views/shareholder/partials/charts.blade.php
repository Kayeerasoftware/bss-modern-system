<div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 md:text-base">Portfolio Trend</h3>
        <p class="mb-3 text-xs text-slate-500">Share value vs portfolio snapshots and paid dividends.</p>
        <div class="h-72"><canvas id="portfolioTrendChart"></canvas></div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 md:text-base">Cash Flow</h3>
        <p class="mb-3 text-xs text-slate-500">Deposits, withdrawals, loan payments, and net movement per period.</p>
        <div class="h-72"><canvas id="cashFlowChart"></canvas></div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 md:text-base">Dividend Split</h3>
        <p class="mb-3 text-xs text-slate-500">Paid vs pending dividends over the selected period.</p>
        <div class="h-72"><canvas id="dividendSplitChart"></canvas></div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 md:text-base">Share Accumulation</h3>
        <p class="mb-3 text-xs text-slate-500">Purchased units and cumulative shareholder growth.</p>
        <div class="h-72"><canvas id="shareAccumulationChart"></canvas></div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 md:text-base">Asset Allocation</h3>
        <p class="mb-3 text-xs text-slate-500">Current composition across assets and liabilities.</p>
        <div class="h-72"><canvas id="assetAllocationChart"></canvas></div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 md:text-base">Project & Risk Signals</h3>
        <p class="mb-3 text-xs text-slate-500">Project status coverage with active opportunity risk profile.</p>
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="h-72"><canvas id="projectSignalChart"></canvas></div>
            <div class="h-72"><canvas id="riskSummaryChart"></canvas></div>
        </div>
    </div>
</div>

<div class="mt-4 grid grid-cols-1 gap-3 lg:grid-cols-3">
    <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Best Cash Flow Period</p>
        <p id="insightBestCashFlow" class="mt-1 text-sm font-bold text-slate-900">-</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Best Dividend Period</p>
        <p id="insightBestDividend" class="mt-1 text-sm font-bold text-slate-900">-</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Average Net Cash / Period</p>
        <p id="insightAvgNetCash" class="mt-1 text-sm font-bold text-slate-900">UGX 0</p>
    </div>
</div>
