@section('title', 'Savings Hub')<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 p-4 md:p-6">
    <div class="mx-auto max-w-[1400px] space-y-6">
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl blur-xl opacity-40"></div>
                <div class="relative bg-gradient-to-br from-emerald-600 to-teal-600 p-3 rounded-xl shadow-xl">
                    <i class="fas fa-piggy-bank text-white text-2xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Savings Hub</h1>
                <p class="text-xs md:text-sm text-gray-600 font-medium">All savings accounts, movements, and insights in one place.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-semibold shadow-sm mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('interest_rate'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm font-semibold shadow-sm mb-6">
            {{ $errors->first('interest_rate') }}
        </div>
    @endif

    <div class="bg-white/70 backdrop-blur border border-emerald-100 rounded-2xl p-4 mb-6 shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <p class="text-[11px] uppercase tracking-wider text-emerald-600 font-semibold">Quick Controls</p>
                <p class="text-xs text-gray-600">Tune alerts and jump to key sections.</p>
            </div>
            <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.savings.index') }}" class="flex flex-wrap items-end gap-2">
                @foreach(request()->except(['low_balance_threshold', 'large_withdrawal_threshold', 'large_withdrawal_days', 'recon_tolerance', 'page', 'accounts_page', 'movements_page']) as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $subValue)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $subValue }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <div>
                    <label class="block text-[10px] text-gray-500 font-semibold mb-1">Low Balance Alert</label>
                    <input type="number" name="low_balance_threshold" value="{{ request('low_balance_threshold', $lowBalanceThreshold) }}" class="w-36 px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white" placeholder="UGX">
                </div>
                <div>
                    <label class="block text-[10px] text-gray-500 font-semibold mb-1">Large Withdrawal Alert</label>
                    <input type="number" name="large_withdrawal_threshold" value="{{ request('large_withdrawal_threshold', $largeWithdrawalThreshold) }}" class="w-40 px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white" placeholder="UGX">
                </div>
                <div>
                    <label class="block text-[10px] text-gray-500 font-semibold mb-1">Alert Window (Days)</label>
                    <input type="number" name="large_withdrawal_days" value="{{ request('large_withdrawal_days', $largeWithdrawalDays) }}" class="w-28 px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white" min="1" max="90">
                </div>
                <div>
                    <label class="block text-[10px] text-gray-500 font-semibold mb-1">Reconcile Tolerance</label>
                    <input type="number" name="recon_tolerance" value="{{ request('recon_tolerance', $reconTolerance) }}" class="w-36 px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white" placeholder="UGX">
                </div>
                <button type="submit" class="px-4 py-2 text-xs bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg font-semibold shadow">Apply</button>
            </form>
            <form method="POST" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.savings.interest-rate') }}" class="flex items-end gap-2 bg-emerald-50 border border-emerald-100 rounded-xl p-3">
                @csrf
                <div>
                    <label class="block text-[10px] text-gray-500 font-semibold mb-1">Savings Interest Rate (%)</label>
                    <input type="number" name="interest_rate" step="0.01" min="0" max="100" value="{{ number_format($settingsInterestRate ?? 0, 2, '.', '') }}" class="w-36 px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <button type="submit" class="px-4 py-2 text-xs bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg font-semibold shadow">Update Rate</button>
            </form>
        </div>
        <div class="flex flex-wrap gap-2 mt-3">
            <a href="#alerts" class="px-3 py-1.5 text-[11px] rounded-full bg-emerald-100 text-emerald-700 font-semibold">Alerts</a>
            <a href="#charts" class="px-3 py-1.5 text-[11px] rounded-full bg-emerald-100 text-emerald-700 font-semibold">Trends</a>
            <a href="#leaders" class="px-3 py-1.5 text-[11px] rounded-full bg-emerald-100 text-emerald-700 font-semibold">Leaderboard</a>
            <a href="#accounts" class="px-3 py-1.5 text-[11px] rounded-full bg-emerald-100 text-emerald-700 font-semibold">Accounts</a>
            <a href="#movements" class="px-3 py-1.5 text-[11px] rounded-full bg-emerald-100 text-emerald-700 font-semibold">Movements</a>
            <a href="#audit" class="px-3 py-1.5 text-[11px] rounded-full bg-emerald-100 text-emerald-700 font-semibold">Audit</a>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200/80 rounded-full overflow-visible mb-6">
        <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-xl md:text-2xl text-emerald-600 font-bold animate-slide-text whitespace-nowrap z-10">Savings intelligence ready</span>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-500 rounded-2xl p-3 text-white shadow-lg ring-1 ring-white/20">
            <p class="text-emerald-100 text-[10px] font-medium mb-1">Reconciled Savings</p>
            <h3 class="text-lg font-bold">UGX {{ number_format($totalSavingsBalance) }}</h3>
            <p class="text-[10px] text-emerald-100">{{ number_format($totalAccounts) }} accounts</p>
        </div>
        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl p-3 text-white shadow-lg ring-1 ring-white/20">
            <p class="text-blue-100 text-[10px] font-medium mb-1">Deposits</p>
            <h3 class="text-lg font-bold">UGX {{ number_format($savingsDeposits) }}</h3>
            <p class="text-[10px] text-blue-100">All time</p>
        </div>
        <div class="bg-gradient-to-br from-rose-500 to-red-600 rounded-2xl p-3 text-white shadow-lg ring-1 ring-white/20">
            <p class="text-rose-100 text-[10px] font-medium mb-1">Withdrawals</p>
            <h3 class="text-lg font-bold">UGX {{ number_format($savingsWithdrawals) }}</h3>
            <p class="text-[10px] text-rose-100">All time</p>
        </div>
        <div class="bg-gradient-to-br from-teal-600 to-cyan-600 rounded-2xl p-3 text-white shadow-lg ring-1 ring-white/20">
            <p class="text-teal-100 text-[10px] font-medium mb-1">Net Savings</p>
            <h3 class="text-lg font-bold">UGX {{ number_format($savingsNet) }}</h3>
            <p class="text-[10px] text-teal-100">Deposits minus withdrawals</p>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-3 text-white shadow-lg ring-1 ring-white/20">
            <p class="text-amber-100 text-[10px] font-medium mb-1">Interest Profit</p>
            <h3 class="text-lg font-bold">UGX {{ number_format($totalInterestProfit ?? 0) }}</h3>
            <p class="text-[10px] text-amber-100">
                Savings (accrued/paid): UGX {{ number_format($totalSavingsInterestProfit ?? 0) }}
                @if(($useComputedSavingsInterest ?? false) && ($settingsInterestRate ?? 0) > 0)
                    <span class="text-amber-200">({{ number_format($settingsInterestRate, 2) }}% settings)</span>
                @endif
                · Loans: UGX {{ number_format($totalLoanInterestProfit ?? 0) }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-6">
        <div class="bg-white rounded-2xl shadow-xl p-4 border border-emerald-100">
            <p class="text-[10px] uppercase tracking-wider text-emerald-600 font-semibold">Source Totals</p>
            <div class="mt-2 space-y-1 text-xs text-gray-600">
                <div class="flex justify-between">
                    <span>Accounts Ledger</span>
                    <span class="font-semibold">UGX {{ number_format($totalSavingsBalanceAccounts ?? 0) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Transactions Derived</span>
                    <span class="font-semibold">UGX {{ number_format($totalSavingsBalanceTransactions ?? 0) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Profit (Interest)</span>
                    <span class="font-semibold">UGX {{ number_format($totalInterestProfit ?? 0) }}</span>
                </div>
            </div>
            <div class="mt-3 text-[10px] text-gray-500">
                Source usage: {{ number_format($balanceSourceCounts->account ?? 0) }} account, {{ number_format($balanceSourceCounts->transaction ?? 0) }} transaction.
            </div>
            <div class="mt-3">
                <div class="flex items-center justify-between text-[10px] text-gray-500">
                    <span>System Progress</span>
                    <span class="font-semibold text-emerald-700">{{ number_format($profitMargin ?? 0, 2) }}%</span>
                </div>
                <div class="mt-1 h-2 rounded-full bg-emerald-100 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500" style="width: {{ min(100, max(0, $profitMargin ?? 0)) }}%"></div>
                </div>
                <p class="mt-1 text-[10px] text-gray-400">Based on profit margin (interest profit ÷ reconciled savings).</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-4 border border-emerald-100">
            <p class="text-[10px] uppercase tracking-wider text-emerald-600 font-semibold">Data Alignment</p>
            <div class="mt-2 grid grid-cols-2 gap-2 text-[11px] text-gray-600">
                <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-2">
                    <p class="text-[10px] text-gray-500">Accounts Present</p>
                    <p class="text-sm font-bold text-emerald-700">{{ number_format($reconciliationStats->accounts_present ?? 0) }}</p>
                    <p class="text-[10px] text-gray-400">Ledger sources</p>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-2">
                    <p class="text-[10px] text-gray-500">Account vs Transaction</p>
                    <p class="text-sm font-bold text-blue-700">{{ number_format($reconciliationStats->acc_txn_mismatches ?? 0) }} mismatches</p>
                    <p class="text-[10px] text-gray-400">Avg gap: UGX {{ number_format($reconciliationStats->acc_txn_avg_gap ?? 0) }}</p>
                </div>
                <div class="bg-amber-50 border border-amber-100 rounded-lg p-2">
                    <p class="text-[10px] text-gray-500">Transactions Present</p>
                    <p class="text-sm font-bold text-amber-700">{{ number_format($reconciliationStats->txn_present ?? 0) }}</p>
                    <p class="text-[10px] text-gray-400">Derived sources</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-2">
                    <p class="text-[10px] text-gray-500">Coverage</p>
                    <p class="text-sm font-bold text-slate-700">{{ number_format($balanceSourceCounts->savers ?? 0) }} active savers</p>
                    <p class="text-[10px] text-gray-400">Accounts: {{ number_format($totalAccounts ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-4 border border-emerald-100">
            <p class="text-[10px] uppercase tracking-wider text-emerald-600 font-semibold">Economic Signals</p>
            <div class="mt-2 space-y-2 text-xs text-gray-600">
                <div class="flex justify-between">
                    <span>Liquidity Ratio</span>
                    <span class="font-semibold">{{ number_format($liquidityRatio ?? 0, 1) }}%</span>
                </div>
                <div class="flex justify-between">
                    <span>Overdraft Utilization</span>
                    <span class="font-semibold">{{ number_format($overdraftUtilization ?? 0, 1) }}%</span>
                </div>
                <div class="flex justify-between">
                    <span>Top 10 Concentration</span>
                    <span class="font-semibold">{{ number_format($topTenShare ?? 0, 1) }}%</span>
                </div>
                <div class="flex justify-between">
                    <span>30-Day Velocity</span>
                    <span class="font-semibold">{{ number_format($savingsVelocity ?? 0, 2) }}%</span>
                </div>
            </div>
        </div>
    </div>

    @php
        $reconMismatchTotal = (int) ($mismatchCount ?? 0);
    @endphp
    @if($reconMismatchTotal > 0)
        <div id="recon-warning" class="bg-rose-50 border border-rose-200 rounded-2xl p-4 mb-6 shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <p class="text-[11px] uppercase tracking-wider text-rose-600 font-semibold">Reconciliation Warning</p>
                    <p class="text-sm font-semibold text-rose-700">{{ number_format($reconMismatchTotal) }} savings mismatches exceed the tolerance (UGX {{ number_format($reconTolerance) }})</p>
                    <p class="text-[11px] text-rose-600">Review the reconciliation table to resolve account/transaction gaps.</p>
                </div>
                <a href="#reconciliation" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-lg bg-rose-600 text-white shadow">Review Reconciliation</a>
            </div>
        </div>
    @endif

    @php
        $avgPerMember = $totalMembersWithSavings > 0 ? $totalSavingsBalance / $totalMembersWithSavings : 0;
        $withdrawalRate = $savingsDeposits > 0 ? ($savingsWithdrawals / $savingsDeposits) * 100 : 0;
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-2xl shadow-xl p-3 border border-gray-100">
            <p class="text-[10px] text-gray-500 font-semibold">Average Balance</p>
            <p class="text-lg font-bold text-emerald-600">UGX {{ number_format($avgBalance) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-3 border border-gray-100">
            <p class="text-[10px] text-gray-500 font-semibold">Active Accounts</p>
            <p class="text-lg font-bold text-blue-600">{{ number_format($activeAccounts) }}</p>
            <p class="text-[10px] text-gray-400">Status: active</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-3 border border-gray-100">
            <p class="text-[10px] text-gray-500 font-semibold">Last 30 Days Net</p>
            <p class="text-lg font-bold {{ $last30dSavingsNet >= 0 ? 'text-green-600' : 'text-red-600' }}">UGX {{ number_format($last30dSavingsNet) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-3 border border-gray-100">
            <p class="text-[10px] text-gray-500 font-semibold">Account Status Mix</p>
            <div class="flex flex-wrap gap-1 mt-1">
                @forelse($statusBreakdown as $status)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700">
                        {{ ucfirst($status->status ?? 'unknown') }}: {{ $status->count }}
                    </span>
                @empty
                    <span class="text-[10px] text-gray-400">No status data</span>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
        <div class="bg-white/80 backdrop-blur rounded-2xl shadow-xl p-4 border border-emerald-100">
            <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Savings Health</p>
            <p class="text-lg font-bold text-emerald-700">UGX {{ number_format($avgPerMember) }}</p>
            <p class="text-[11px] text-gray-500">Average per member with savings</p>
        </div>
        <div class="bg-white/80 backdrop-blur rounded-2xl shadow-xl p-4 border border-emerald-100">
            <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Withdrawal Intensity</p>
            <p class="text-lg font-bold text-rose-600">{{ number_format($withdrawalRate, 1) }}%</p>
            <p class="text-[11px] text-gray-500">Withdrawals vs deposits</p>
        </div>
        <div class="bg-white/80 backdrop-blur rounded-2xl shadow-xl p-4 border border-emerald-100">
            <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Members With Savings</p>
            <p class="text-lg font-bold text-blue-600">{{ number_format($totalMembersWithSavings) }}</p>
            <p class="text-[11px] text-gray-500">Active savers in the system</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-2xl shadow-xl p-4 border border-emerald-100">
            <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Available Balance</p>
            <p class="text-lg font-bold text-emerald-700">UGX {{ number_format($totalAvailableBalance) }}</p>
            <p class="text-[11px] text-gray-500">Immediately withdrawable</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-4 border border-emerald-100">
            <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Overdraft Used</p>
            <p class="text-lg font-bold text-amber-600">UGX {{ number_format($totalOverdraftUsed) }}</p>
            <p class="text-[11px] text-gray-500">Across all accounts</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-4 border border-emerald-100">
            <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Overdraft Limit</p>
            <p class="text-lg font-bold text-indigo-600">UGX {{ number_format($totalOverdraftLimit) }}</p>
            <p class="text-[11px] text-gray-500">Total approved limit</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-4 border border-emerald-100">
            <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Account States</p>
            <div class="flex flex-wrap gap-1 mt-1">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700">Active: {{ $activeAccounts }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">Frozen: {{ $frozenAccounts }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">Closed: {{ $closedAccounts }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">Joint: {{ $jointAccounts }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700">Matured: {{ $maturedAccounts }}</span>
            </div>
        </div>
    </div>

    <!-- Charts -->
    @php
        $hasMonthlyData = collect($monthlyDeposits ?? [])->sum() + collect($monthlyWithdrawals ?? [])->sum() > 0;
        $hasNetData = collect($monthlyNet ?? [])->sum() !== 0;
    @endphp
    <div id="charts" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6 scroll-mt-24">
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Monthly Savings Flow</h3>
                    <p class="text-[11px] text-gray-500">Deposits vs withdrawals (last 12 months)</p>
                </div>
                <div class="flex items-center gap-2 text-[10px] text-gray-500">
                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Deposits</span>
                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Withdrawals</span>
                </div>
            </div>
            <div class="relative h-[240px]">
                <canvas id="savingsMonthlyChart" style="{{ $hasMonthlyData ? '' : 'display:none;' }}"></canvas>
                <div id="savingsMonthlyEmpty" class="absolute inset-0 flex items-center justify-center" style="{{ $hasMonthlyData ? 'display:none;' : '' }}">
                    <div class="text-center">
                        <div class="mx-auto mb-2 w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-600">No savings data yet</p>
                        <p class="text-[11px] text-gray-400">Start recording savings movements</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Net Savings Trend</h3>
                    <p class="text-[11px] text-gray-500">Monthly net change</p>
                </div>
            </div>
            <div class="relative h-[240px]">
                <canvas id="savingsNetChart" style="{{ $hasNetData ? '' : 'display:none;' }}"></canvas>
                <div id="savingsNetEmpty" class="absolute inset-0 flex items-center justify-center" style="{{ $hasNetData ? 'display:none;' : '' }}">
                    <div class="text-center">
                        <div class="mx-auto mb-2 w-12 h-12 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg">
                            <i class="fas fa-coins text-white"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-600">No net trend yet</p>
                        <p class="text-[11px] text-gray-400">Net savings will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Savings Movement Mix</h3>
                    <p class="text-[11px] text-gray-500">Deposit vs withdrawal vs transfers</p>
                </div>
            </div>
            <div class="relative h-[220px]">
                <canvas id="movementMixChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3">Movement Summary</h3>
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-2">
                    <p class="text-[10px] text-gray-600">Total Movements</p>
                    <p class="text-sm font-bold text-emerald-700">{{ number_format($movementsSummary->total_count ?? 0) }}</p>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-2">
                    <p class="text-[10px] text-gray-600">Total Amount</p>
                    <p class="text-sm font-bold text-blue-700">UGX {{ number_format($movementsSummary->total_amount ?? 0) }}</p>
                </div>
                <div class="bg-amber-50 border border-amber-100 rounded-lg p-2">
                    <p class="text-[10px] text-gray-600">Average Amount</p>
                    <p class="text-sm font-bold text-amber-700">UGX {{ number_format($movementsSummary->avg_amount ?? 0) }}</p>
                </div>
                <div class="bg-rose-50 border border-rose-100 rounded-lg p-2">
                    <p class="text-[10px] text-gray-600">Largest Movement</p>
                    <p class="text-sm font-bold text-rose-700">UGX {{ number_format($movementsSummary->max_amount ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3">Movement Counts</h3>
            @php
                $savingsDepositCount = (int) ($movementCategoryStats->get('savings_deposit')->count ?? 0);
                $loanDisbursementCount = (int) ($movementCategoryStats->get('loan_disbursement')->count ?? 0);
                $savingsWithdrawalCount = (int) ($movementCategoryStats->get('savings_withdrawal')->count ?? 0);
                $transferInCount = (int) ($movementCategoryStats->get('transfer_in')->count ?? 0);
                $transferOutCount = (int) ($movementCategoryStats->get('transfer_out')->count ?? 0);
                $fundraisingTransferCount = (int) ($movementCategoryStats->get('fundraising_transfer')->count ?? 0);
            @endphp
            <div class="space-y-2 text-xs text-gray-600">
                <div class="flex justify-between">
                    <span>Savings Deposits</span>
                    <span class="font-semibold">{{ number_format($savingsDepositCount) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Loan Disbursements</span>
                    <span class="font-semibold">{{ number_format($loanDisbursementCount) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Savings Withdrawals</span>
                    <span class="font-semibold">{{ number_format($savingsWithdrawalCount) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Transfers In</span>
                    <span class="font-semibold">{{ number_format($transferInCount) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Transfers Out</span>
                    <span class="font-semibold">{{ number_format($transferOutCount + $fundraisingTransferCount) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Fundraising Transfers</span>
                    <span class="font-semibold">{{ number_format($fundraisingTransferCount) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white/80 backdrop-blur border border-emerald-100 rounded-2xl p-4 mb-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <p class="text-[11px] uppercase tracking-wider text-emerald-600 font-semibold">Tables Navigation</p>
                <p class="text-xs text-gray-600">Jump to any table section as data scales.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" data-table-toggle="top-savers" class="table-toggle px-3 py-1.5 text-[11px] rounded-full bg-emerald-100 text-emerald-700 font-semibold">Top Savers</button>
                <button type="button" data-table-toggle="goals" class="table-toggle px-3 py-1.5 text-[11px] rounded-full bg-teal-100 text-teal-700 font-semibold">Savings Goals</button>
                <button type="button" data-table-toggle="reconciliation" class="table-toggle px-3 py-1.5 text-[11px] rounded-full bg-emerald-100 text-emerald-700 font-semibold">Reconciliation</button>
                <button type="button" data-table-toggle="accounts" class="table-toggle px-3 py-1.5 text-[11px] rounded-full bg-emerald-100 text-emerald-700 font-semibold">Savings Accounts</button>
                <button type="button" data-table-toggle="movements" class="table-toggle px-3 py-1.5 text-[11px] rounded-full bg-blue-100 text-blue-700 font-semibold">Savings Movements</button>
                <button type="button" data-table-toggle="audit" class="table-toggle px-3 py-1.5 text-[11px] rounded-full bg-slate-100 text-slate-700 font-semibold">Savings Audit Trail</button>
                <button type="button" data-table-toggle="alerts" class="table-toggle px-3 py-1.5 text-[11px] rounded-full bg-rose-100 text-rose-700 font-semibold">Low Balance Alerts</button>
                <button type="button" data-table-toggle="withdrawals" class="table-toggle px-3 py-1.5 text-[11px] rounded-full bg-amber-100 text-amber-700 font-semibold">Large Withdrawal Watch</button>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="grid grid-cols-1 gap-4 mb-6 scroll-mt-24">
        <div id="reconciliation" data-table-section="reconciliation" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-emerald-100 hidden">
            <div class="px-4 py-3 border-b bg-emerald-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Savings Reconciliation</h3>
                    <p class="text-[11px] text-gray-500">Showing mismatches above UGX {{ number_format($reconTolerance) }}</p>
                </div>
                <span class="text-[10px] font-semibold text-emerald-600">{{ number_format($mismatchCount) }} flagged</span>
            </div>
            <div class="px-4 py-3 border-b bg-white">
                <div class="text-[11px] text-gray-500">
                    Priority order: Accounts ledger → Transaction-derived. Use this table to target reconciliations before reporting or payouts.
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-emerald-600 to-teal-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Account</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Transactions</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Reported</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Max Gap</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Source</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-emerald-50">
                        @forelse($mismatches as $row)
                            <tr class="hover:bg-emerald-50/40 transition">
                                <td class="px-4 py-3 text-xs text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $row->profile_picture_url ?? asset('images/default-avatar.svg') }}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';" class="w-8 h-8 rounded-full object-cover ring-2 ring-emerald-500 ring-offset-2" alt="">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $row->full_name ?? 'N/A' }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $row->member_number ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-700">UGX {{ number_format($row->account_balance ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">UGX {{ number_format($row->txn_balance ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-emerald-700">UGX {{ number_format($row->reported_balance ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-rose-600">UGX {{ number_format($row->max_gap ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ ucfirst($row->balance_source ?? 'none') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No mismatches above the current tolerance.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div id="alerts" data-table-section="alerts" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-rose-100 hidden">
            <div class="px-4 py-3 border-b bg-rose-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Low Balance Alerts</h3>
                    <p class="text-[11px] text-gray-500">Accounts below UGX {{ number_format($lowBalanceThreshold) }}</p>
                </div>
                <span class="text-[10px] font-semibold text-rose-600">Top {{ $lowBalanceAccounts->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-rose-500 to-red-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Account</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Savings Accounts</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Net Savings</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-rose-50">
                        @forelse($lowBalanceAccounts as $account)
                            <tr class="hover:bg-rose-50/40 transition">
                                <td class="px-4 py-3 text-xs text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $account->member->profile_picture_url ?? asset('images/default-avatar.svg') }}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';" class="w-8 h-8 rounded-full object-cover ring-2 ring-rose-500 ring-offset-2" alt="">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $account->member->full_name ?? 'N/A' }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $account->member->member_number ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $account->account_number ?? 'Account' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $account->member->member_number ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-rose-600">UGX {{ number_format($account->current_balance ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">No low-balance accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div id="withdrawals" data-table-section="withdrawals" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-amber-100 hidden">
            <div class="px-4 py-3 border-b bg-amber-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Large Withdrawal Watch</h3>
                    <p class="text-[11px] text-gray-500">Last {{ $largeWithdrawalDays }} days above UGX {{ number_format($largeWithdrawalThreshold) }}</p>
                </div>
                <span class="text-[10px] font-semibold text-amber-600">Top {{ $largeWithdrawals->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-amber-500 to-orange-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Transaction</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-amber-50">
                        @forelse($largeWithdrawals as $tx)
                            <tr class="hover:bg-amber-50/40 transition">
                                <td class="px-4 py-3 text-xs text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $tx->member->profile_picture_url ?? asset('images/default-avatar.svg') }}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';" class="w-8 h-8 rounded-full object-cover ring-2 ring-amber-500 ring-offset-2" alt="">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $tx->member->full_name ?? 'N/A' }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $tx->member->member_number ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $tx->transaction_number ?? $tx->reference_number ?? 'Txn' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ optional($tx->transaction_date ?? $tx->created_at)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-amber-600">UGX {{ number_format($tx->amount ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">No large withdrawals in the last {{ $largeWithdrawalDays }} days.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Leaderboard & Goals -->
    <div class="grid grid-cols-1 gap-4 mb-6 scroll-mt-24">
        <div id="top-savers" data-table-section="top-savers" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 hidden">
            <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Top Savers</h3>
                    <p class="text-[11px] text-gray-500">Ranked by current savings balance</p>
                </div>
                <span class="text-[10px] text-emerald-600 font-semibold">Showing {{ $topMembers->firstItem() ?? 0 }}-{{ $topMembers->lastItem() ?? 0 }} of {{ $topMembers->total() ?? 0 }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-emerald-600 to-teal-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Rank</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Savings Accounts</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-white uppercase">Net Savings</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-white uppercase">Current Balance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-emerald-50">
                        @forelse($topMembers as $index => $member)
                            <tr class="hover:bg-emerald-50/40 transition">
                                <td class="px-4 py-3 text-xs text-gray-700 font-semibold">
                                    {{ ($topMembers->firstItem() ?? 0) + $index }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $member->profile_picture_url ?? asset('images/default-avatar.svg') }}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';" class="w-9 h-9 rounded-full object-cover ring-2 ring-emerald-500 ring-offset-2" alt="">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $member->full_name ?? 'N/A' }}</p>
                                            <p class="text-[10px] text-gray-500">Member No: {{ $member->member_number ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">
                                            {{ number_format($member->accounts_count ?? 0) }} account{{ ($member->accounts_count ?? 0) == 1 ? '' : 's' }}
                                        </span>
                                        <div class="text-[11px] text-gray-600">
                                            {{ $member->account_numbers !== '' ? $member->account_numbers : 'N/A' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right text-xs font-semibold text-emerald-700">
                                    UGX {{ number_format($member->net_savings ?? 0) }}
                                    <div class="text-[10px] text-gray-400">Deposits − Withdrawals</div>
                                </td>
                                <td class="px-4 py-3 text-right text-xs font-semibold text-gray-800">
                                    UGX {{ number_format($member->balance ?? 0) }}
                                    <div class="text-[10px] text-gray-400">Total savings balance</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No savings leaderboard data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t">
                {{ $topMembers->links() }}
            </div>
        </div>

        <div id="goals" data-table-section="goals" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 hidden">
            <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Savings Goals (Milestones)</h3>
                    <p class="text-[11px] text-gray-500">Progress based on current balances</p>
                </div>
                <span class="text-[10px] text-gray-400">Members with savings: {{ number_format($totalMembersWithSavings) }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-emerald-600 to-teal-600">
                        <tr class="border-b-2 border-white/20">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Target (UGX)</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Members</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-emerald-50">
                        @forelse($goalProgress as $goal)
                            <tr class="hover:bg-emerald-50/40 transition">
                                <td class="px-4 py-3 text-xs text-gray-700 font-semibold">UGX {{ number_format($goal['target']) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ number_format($goal['members']) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <div class="w-full h-2 rounded-full bg-gray-100 overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500" style="width: {{ $goal['percent'] }}%"></div>
                                        </div>
                                        <span class="text-[10px] text-gray-500 whitespace-nowrap">{{ $goal['percent'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500">Goal tracking will appear once balances are available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Savings Accounts -->
    <div id="accounts" data-table-section="accounts" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-6 scroll-mt-24 hidden">
        <div class="px-4 py-3 border-b bg-gray-50">
            <h3 class="text-sm font-bold text-gray-800">Savings Accounts</h3>
        </div>
        <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.savings.index') }}" class="p-3 border-b border-emerald-100 bg-emerald-50/40">
            <input type="hidden" name="low_balance_threshold" value="{{ request('low_balance_threshold', $lowBalanceThreshold) }}">
            <input type="hidden" name="recon_tolerance" value="{{ request('recon_tolerance', $reconTolerance) }}">
            <input type="hidden" name="large_withdrawal_threshold" value="{{ request('large_withdrawal_threshold', $largeWithdrawalThreshold) }}">
            <input type="hidden" name="large_withdrawal_days" value="{{ request('large_withdrawal_days', $largeWithdrawalDays) }}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <div class="md:col-span-4">
                    <input type="text" name="accounts_search" value="{{ request('accounts_search') }}" placeholder="Search member or account..." class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <select name="accounts_status" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="">Status</option>
                        @foreach($accountStatuses as $statusOption)
                            <option value="{{ $statusOption }}" @selected(request('accounts_status') === $statusOption)>{{ ucfirst($statusOption) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="accounts_min" value="{{ request('accounts_min') }}" placeholder="Min balance" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="accounts_max" value="{{ request('accounts_max') }}" placeholder="Max balance" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <select name="accounts_joint" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="">Joint?</option>
                        <option value="yes" @selected(request('accounts_joint') === 'yes')>Yes</option>
                        <option value="no" @selected(request('accounts_joint') === 'no')>No</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="accounts_opened_from" value="{{ request('accounts_opened_from') }}" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white" placeholder="Opened from">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="accounts_opened_to" value="{{ request('accounts_opened_to') }}" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white" placeholder="Opened to">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="accounts_maturity_from" value="{{ request('accounts_maturity_from') }}" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white" placeholder="Maturity from">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="accounts_maturity_to" value="{{ request('accounts_maturity_to') }}" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white" placeholder="Maturity to">
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg font-semibold">Filter</button>
                    <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.savings.index') }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                </div>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-emerald-600 to-teal-600">
                    <tr class="border-b-2 border-white/20">
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Account</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Balance</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Available</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Overdraft</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Interest</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Open/Maturity</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Updated</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-emerald-50">
                    @forelse($accounts as $account)
                        <tr class="hover:bg-emerald-50/40 transition">
                            <td class="px-4 py-3 text-xs text-gray-700">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $account->member->profile_picture_url ?? asset('images/default-avatar.svg') }}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';" class="w-8 h-8 rounded-full object-cover ring-2 ring-emerald-500 ring-offset-2" alt="">
                                    <div>
                                        <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.members.show', $account->member_id) }}" class="font-semibold text-emerald-700 hover:underline">{{ $account->member->full_name ?? 'N/A' }}</a>
                                        <p class="text-[10px] text-gray-400">{{ $account->member->member_number ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-700">
                                {{ $account->account_number ?? 'N/A' }}<br>
                                <span class="text-[10px] text-gray-400">{{ $account->account_name ?? 'Savings' }}{{ $account->is_joint ? ' - Joint' : '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold text-emerald-700">UGX {{ number_format($account->current_balance ?? 0) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700">UGX {{ number_format($account->available_balance ?? $account->current_balance ?? 0) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700">
                                <p class="font-semibold text-gray-800">UGX {{ number_format($account->overdraft_used ?? 0) }}</p>
                                <p class="text-[10px] text-gray-400">Limit: {{ number_format($account->overdraft_limit ?? 0) }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-700">
                                <p class="font-semibold text-gray-800">UGX {{ number_format($account->accrued_interest ?? 0) }}</p>
                                <p class="text-[10px] text-gray-400">Last calc: {{ optional($account->last_interest_calculation)->format('M d, Y') ?? 'N/A' }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @php
                                    $status = strtolower((string) ($account->status ?? 'unknown'));
                                    $statusClass = match ($status) {
                                        'active' => 'bg-emerald-100 text-emerald-700',
                                        'frozen' => 'bg-amber-100 text-amber-700',
                                        'closed' => 'bg-slate-100 text-slate-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $statusClass }}">{{ ucfirst($status) }}</span>
                            </td>
                            @php
                                $lastSavingsDate = $lastSavingsDates[$account->member_id] ?? null;
                                $lastSavingsDateFormatted = $lastSavingsDate ? \Illuminate\Support\Carbon::parse($lastSavingsDate)->format('M d, Y') : null;
                            @endphp
                            <td class="px-4 py-3 text-xs text-gray-600">
                                <p>{{ $lastSavingsDateFormatted ?? optional($account->opening_date ?? data_get($account, 'created_at'))->format('M d, Y') ?? 'N/A' }}</p>
                                <p class="text-[10px] text-gray-400">Maturity: {{ optional($account->maturity_date)->format('M d, Y') ?? 'N/A' }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $lastSavingsDateFormatted ?? optional($account->updated_at ?? data_get($account, 'created_at'))->format('M d, Y') ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-sm text-gray-500">No savings accounts available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $accounts->links() }}
        </div>
    </div>

    <!-- Savings Movements -->
    <div id="movements" data-table-section="movements" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-6 scroll-mt-24 hidden">
        <div class="px-4 py-3 border-b bg-gray-50">
            <h3 class="text-sm font-bold text-gray-800">Savings Movements (Deposits, Withdrawals, Transfers)</h3>
        </div>
        <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.savings.index') }}" class="p-3 border-b border-emerald-100 bg-emerald-50/40">
            <input type="hidden" name="low_balance_threshold" value="{{ request('low_balance_threshold', $lowBalanceThreshold) }}">
            <input type="hidden" name="recon_tolerance" value="{{ request('recon_tolerance', $reconTolerance) }}">
            <input type="hidden" name="large_withdrawal_threshold" value="{{ request('large_withdrawal_threshold', $largeWithdrawalThreshold) }}">
            <input type="hidden" name="large_withdrawal_days" value="{{ request('large_withdrawal_days', $largeWithdrawalDays) }}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <div class="md:col-span-4">
                    <input type="text" name="movement_search" value="{{ request('movement_search') }}" placeholder="Search transactions..." class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <select name="movement_type" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="">Type</option>
                        @foreach($movementTypes as $type)
                            <option value="{{ $type }}" @selected(request('movement_type') === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <select name="movement_category" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="">Category</option>
                        @foreach($movementCategories as $category)
                            <option value="{{ $category->name }}" @selected(request('movement_category') === $category->name)>{{ $category->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="movement_date_from" value="{{ request('movement_date_from') }}" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="movement_date_to" value="{{ request('movement_date_to') }}" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="movement_amount_min" value="{{ request('movement_amount_min') }}" placeholder="Min amount" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="number" name="movement_amount_max" value="{{ request('movement_amount_max') }}" placeholder="Max amount" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <select name="movement_status" class="w-full px-3 py-2 text-xs border border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="">Status</option>
                        @foreach($movementStatuses as $status)
                            <option value="{{ $status }}" @selected(request('movement_status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg font-semibold">Filter</button>
                    <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.savings.index') }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                </div>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-emerald-600 to-teal-600">
                    <tr class="border-b-2 border-white/20">
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($movements as $movement)
                        <tr class="border-b hover:bg-emerald-50/40">
                            <td class="px-4 py-3 text-xs text-gray-700">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $movement->member->profile_picture_url ?? asset('images/default-avatar.svg') }}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';" class="w-8 h-8 rounded-full object-cover ring-2 ring-emerald-500 ring-offset-2" alt="">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $movement->member->full_name ?? 'N/A' }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $movement->member->member_number ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-700">{{ $movement->transactionType->display_name ?? ucfirst($movement->transactionType->name ?? 'N/A') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700">{{ $movement->transactionCategory->display_name ?? ucfirst($movement->transactionCategory->name ?? 'N/A') }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-emerald-700">UGX {{ number_format($movement->amount ?? 0) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700">{{ $movement->statusRelation->display_name ?? $movement->statusRelation->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ optional($movement->transaction_date ?? $movement->created_at)->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-xs">
                                <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.transactions.show', $movement->id) }}" class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-700 font-semibold">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">No savings movements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $movements->links() }}
        </div>
    </div>

    <!-- Audit Trail -->
    <div id="audit" data-table-section="audit" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 scroll-mt-24 hidden">
        <div class="px-4 py-3 border-b bg-gray-50">
            <h3 class="text-sm font-bold text-gray-800">Savings Audit Trail</h3>
        </div>
        <form method="GET" action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.savings.index') }}" class="p-3 border-b border-slate-200 bg-slate-50/60">
            <input type="hidden" name="low_balance_threshold" value="{{ request('low_balance_threshold', $lowBalanceThreshold) }}">
            <input type="hidden" name="recon_tolerance" value="{{ request('recon_tolerance', $reconTolerance) }}">
            <input type="hidden" name="large_withdrawal_threshold" value="{{ request('large_withdrawal_threshold', $largeWithdrawalThreshold) }}">
            <input type="hidden" name="large_withdrawal_days" value="{{ request('large_withdrawal_days', $largeWithdrawalDays) }}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <div class="md:col-span-4">
                    <input type="text" name="audit_search" value="{{ request('audit_search') }}" placeholder="Search audit logs..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-slate-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <select name="audit_action" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-slate-500 bg-white">
                        <option value="">Action</option>
                        <option value="create" @selected(request('audit_action') === 'create')>Create</option>
                        <option value="update" @selected(request('audit_action') === 'update')>Update</option>
                        <option value="delete" @selected(request('audit_action') === 'delete')>Delete</option>
                        <option value="download" @selected(request('audit_action') === 'download')>Download</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <select name="audit_category" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-slate-500 bg-white">
                        <option value="">Category</option>
                        @foreach($movementCategories as $category)
                            <option value="{{ $category->name }}" @selected(request('audit_category') === $category->name)>{{ $category->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="audit_date_from" value="{{ request('audit_date_from') }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-slate-500 bg-white">
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="audit_date_to" value="{{ request('audit_date_to') }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-slate-500 bg-white">
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-slate-700 to-slate-900 text-white rounded-lg font-semibold">Filter</button>
                    <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.savings.index') }}" class="px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg font-semibold text-center">Reset</a>
                </div>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-slate-600 to-slate-700">
                    <tr class="border-b-2 border-white/20">
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">User</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-xs text-gray-600">{{ optional($log->created_at)->format('M d, Y H:i') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700">{{ ucfirst($log->action ?? 'activity') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $log->member_picture_url ?? asset('images/default-avatar.svg') }}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';" class="w-8 h-8 rounded-full object-cover ring-2 ring-slate-500 ring-offset-2" alt="">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $log->member_name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-700">{{ $log->category_name ?? 'Savings' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $log->description ?? $log->entity_identifier ?? 'Update' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700">{{ $log->user_name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No audit logs found for savings activity.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-4 text-[11px] text-gray-500">
            Showing the latest 20 audit entries linked to savings-related transactions.
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
function initAdminSavingsCharts(force = false) {
    if (!window.Chart) {
        const monthlyEmpty = document.getElementById('savingsMonthlyEmpty');
        const netEmpty = document.getElementById('savingsNetEmpty');
        if (monthlyEmpty) monthlyEmpty.style.display = '';
        if (netEmpty) netEmpty.style.display = '';
        return;
    }
    window.adminSavingsCharts = window.adminSavingsCharts || {};
    const labels = @json($monthlyLabels ?? []);
    const deposits = @json($monthlyDeposits ?? []);
    const withdrawals = @json($monthlyWithdrawals ?? []);
    const net = @json($monthlyNet ?? []);
    const movementLabels = @json($movementChartLabels ?? []);
    const movementTotals = @json($movementChartTotals ?? []);
    const fmtCurrency = (v) => 'UGX ' + (Number(v) || 0).toLocaleString();

    const hasMonthlyData = deposits.some(v => v > 0) || withdrawals.some(v => v > 0);
    if (hasMonthlyData) {
        const monthlyEmpty = document.getElementById('savingsMonthlyEmpty');
        if (monthlyEmpty) monthlyEmpty.style.display = 'none';
        const monthlyCanvas = document.getElementById('savingsMonthlyChart');
        if (monthlyCanvas) {
            if (force && window.adminSavingsCharts.monthly) {
                window.adminSavingsCharts.monthly.destroy();
            }
            try {
                window.adminSavingsCharts.monthly = new Chart(monthlyCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Deposits',
                            data: deposits,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 4
                        }, {
                            label: 'Withdrawals',
                            data: withdrawals,
                            borderColor: '#f43f5e',
                            backgroundColor: 'rgba(244, 63, 94, 0.12)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10 } } },
                            tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${fmtCurrency(ctx.parsed.y)}` } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { callback: (v) => fmtCurrency(v), font: { size: 10 } } },
                            x: { ticks: { font: { size: 9 } } }
                        }
                    }
                });
                monthlyCanvas.style.display = '';
            } catch (err) {
                if (monthlyEmpty) monthlyEmpty.style.display = '';
                monthlyCanvas.style.display = 'none';
            }
        }
    }

    const hasNetData = net.some(v => v !== 0);
    if (hasNetData) {
        const netEmpty = document.getElementById('savingsNetEmpty');
        if (netEmpty) netEmpty.style.display = 'none';
        const netCanvas = document.getElementById('savingsNetChart');
        if (netCanvas) {
            if (force && window.adminSavingsCharts.net) {
                window.adminSavingsCharts.net.destroy();
            }
            try {
                window.adminSavingsCharts.net = new Chart(netCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Net',
                            data: net,
                            backgroundColor: net.map(v => (v >= 0 ? '#10b981' : '#f43f5e'))
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: (ctx) => fmtCurrency(ctx.parsed.y) } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { callback: (v) => fmtCurrency(v), font: { size: 10 } } },
                            x: { ticks: { font: { size: 9 } } }
                        }
                    }
                });
                netCanvas.style.display = '';
            } catch (err) {
                if (netEmpty) netEmpty.style.display = '';
                netCanvas.style.display = 'none';
            }
        }
    }

    const mixCanvas = document.getElementById('movementMixChart');
    if (mixCanvas && movementTotals.some(v => v > 0)) {
        if (force && window.adminSavingsCharts.mix) {
            window.adminSavingsCharts.mix.destroy();
        }
        try {
            window.adminSavingsCharts.mix = new Chart(mixCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: movementLabels,
                    datasets: [{
                        data: movementTotals,
                        backgroundColor: ['#10b981', '#f43f5e', '#3b82f6', '#f59e0b'],
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } },
                        tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${fmtCurrency(ctx.parsed)}` } }
                    },
                    cutout: '62%'
                }
            });
        } catch (err) {
            mixCanvas.style.display = 'none';
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() { initAdminSavingsCharts(true); });
} else {
    initAdminSavingsCharts(true);
}

document.addEventListener('DOMContentLoaded', function() {
    const toggles = Array.from(document.querySelectorAll('[data-table-toggle]'));
    const sections = Array.from(document.querySelectorAll('[data-table-section]'));
    if (toggles.length === 0 || sections.length === 0) return;

    const setActive = (name) => {
        let activeSection = null;
        sections.forEach((section) => {
            const isActive = section.dataset.tableSection === name;
            section.classList.toggle('hidden', !isActive);
            if (isActive) activeSection = section;
        });
        toggles.forEach((btn) => {
            const isActive = btn.dataset.tableToggle === name;
            btn.classList.toggle('bg-emerald-600', isActive);
            btn.classList.toggle('text-white', isActive);
            btn.classList.toggle('shadow', isActive);
        });
        if (activeSection) {
            activeSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    toggles.forEach((btn) => {
        btn.addEventListener('click', () => setActive(btn.dataset.tableToggle));
    });

    setActive('accounts');
});

</script>
@endpush

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
@keyframes slide-text { 0% { left: 0%; opacity: 1; } 95% { opacity: 1; } 100% { left: 100%; opacity: 0; } }
.animate-slide-text { animation: slide-text 5s ease-out forwards; }
</style>