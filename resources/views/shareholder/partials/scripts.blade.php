<script>
    let dashboardState = @json($dashboardData);
    const charts = {};

    const currencyFormatter = new Intl.NumberFormat('en-UG', {
        maximumFractionDigits: 0,
    });
    const compactCurrencyFormatter = new Intl.NumberFormat('en-UG', {
        notation: 'compact',
        maximumFractionDigits: 1,
    });

    function fmtCurrency(value) {
        return 'UGX ' + currencyFormatter.format(Number(value || 0));
    }

    function fmtCompactCurrency(value) {
        return 'UGX ' + compactCurrencyFormatter.format(Number(value || 0));
    }

    function fmtNumber(value, digits = 0) {
        return Number(value || 0).toLocaleString('en-UG', {
            minimumFractionDigits: digits,
            maximumFractionDigits: digits,
        });
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function updateStats(stats) {
        document.getElementById('statPortfolioValue').textContent = fmtCurrency(stats.portfolioValue);
        document.getElementById('statPortfolioChangePct').textContent = fmtNumber(stats.portfolioChangePct, 1) + '%';
        document.getElementById('statTotalShares').textContent = fmtNumber(stats.totalShares);
        document.getElementById('statAverageSharePrice').textContent = fmtCurrency(stats.averageSharePrice);
        document.getElementById('statDividendsPaid').textContent = fmtCurrency(stats.dividendsPaid);
        document.getElementById('statDividendsPending').textContent = fmtCurrency(stats.dividendsPending);
        document.getElementById('statNetWorth').textContent = fmtCurrency(stats.netWorth);
        document.getElementById('statDividendYieldPct').textContent = fmtNumber(stats.dividendYieldPct, 2) + '%';
        document.getElementById('statNetCashFlow30').textContent = fmtCurrency(stats.netCashFlow30);
        document.getElementById('statDeposits30').textContent = fmtNumber(stats.deposits30);
        document.getElementById('statWithdrawals30').textContent = fmtNumber(stats.withdrawals30);
        document.getElementById('statSavingsBalance').textContent = fmtCurrency(stats.savingsBalance);
        document.getElementById('statLoanOutstanding').textContent = fmtCurrency(stats.loanOutstanding);
        document.getElementById('statActiveLoans').textContent = fmtNumber(stats.activeLoans);
        document.getElementById('statProjectSignals').textContent = fmtNumber(stats.activeProjects) + '/' + fmtNumber(stats.totalProjects);
        document.getElementById('statAvgProjectRoi').textContent = fmtNumber(stats.avgProjectRoi, 1) + '%';
        document.getElementById('statActiveOpportunities').textContent = fmtNumber(stats.activeOpportunities);
        document.getElementById('statOpportunityTarget').textContent = fmtCurrency(stats.opportunityTarget);
    }

    function renderInsightCards(insights) {
        document.getElementById('insightBestCashFlow').textContent = insights.bestCashFlowPeriod || '-';
        document.getElementById('insightBestDividend').textContent = insights.bestDividendPeriod || '-';
        document.getElementById('insightAvgNetCash').textContent = fmtCurrency(insights.avgNetCashPerPeriod || 0);
    }

    function renderRecentLists(data) {
        const dividendsWrap = document.getElementById('recentDividendsWrap');
        const sharesWrap = document.getElementById('recentSharesWrap');
        const transactionsWrap = document.getElementById('recentTransactionsWrap');
        const opportunitiesWrap = document.getElementById('opportunitiesWrap');

        dividendsWrap.innerHTML = (data.recent.dividends || []).length
            ? data.recent.dividends.map((item) => `
                <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 p-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-800">${escapeHtml(item.status ? item.status.toUpperCase() : 'DIVIDEND')}</p>
                        <p class="text-[11px] text-slate-500">${escapeHtml((item.payment_date || item.created_at || '').toString().slice(0, 10))}</p>
                    </div>
                    <p class="text-xs font-bold text-emerald-700">${fmtCurrency(item.amount)}</p>
                </div>
            `).join('')
            : '<p class="rounded-lg bg-slate-50 p-3 text-xs text-slate-500">No dividend records for this period.</p>';

        sharesWrap.innerHTML = (data.recent.shares || []).length
            ? data.recent.shares.map((item) => {
                const shareValue = Number(item.shares_owned || 0) * Number(item.share_value || 0);
                return `
                    <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 p-2">
                        <div>
                        <p class="text-xs font-semibold text-slate-800">${fmtNumber(item.shares_owned)} units</p>
                        <p class="text-[11px] text-slate-500">Price ${fmtCurrency(item.share_value)} • ${escapeHtml((item.purchase_date || item.created_at || '').toString().slice(0, 10))}</p>
                    </div>
                    <p class="text-xs font-bold text-cyan-700">${fmtCurrency(shareValue)}</p>
                </div>
                `;
            }).join('')
            : '<p class="rounded-lg bg-slate-50 p-3 text-xs text-slate-500">No share purchase records for this period.</p>';

        transactionsWrap.innerHTML = (data.recent.transactions || []).length
            ? data.recent.transactions.map((item) => `
                <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 p-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-800">${escapeHtml((item.type || 'transaction').replace('_', ' ').toUpperCase())}</p>
                        <p class="text-[11px] text-slate-500">${escapeHtml((item.created_at || '').toString().slice(0, 10))} • ${escapeHtml((item.status || 'completed').toUpperCase())}</p>
                    </div>
                    <p class="text-xs font-bold text-indigo-700">${fmtCurrency(item.amount)}</p>
                </div>
            `).join('')
            : '<p class="rounded-lg bg-slate-50 p-3 text-xs text-slate-500">No transactions for this period.</p>';

        const opportunityRows = [
            ...(data.recent.opportunities || []).map((item) => ({
                title: item.title,
                subtitle: `Opportunity • ${String(item.risk_level || 'n/a').toUpperCase()} risk`,
                metric: `${fmtNumber(item.expected_roi, 1)}% ROI`,
            })),
            ...(data.recent.projects || []).slice(0, 3).map((item) => ({
                title: item.name,
                subtitle: `Project • ${String(item.status || 'n/a').toUpperCase()} • ${fmtNumber(item.progress, 0)}%`,
                metric: `${fmtNumber(item.roi, 1)}% ROI`,
            })),
        ];

        opportunitiesWrap.innerHTML = opportunityRows.length
            ? opportunityRows.map((item) => `
                <div class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 p-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-800">${escapeHtml(item.title)}</p>
                        <p class="text-[11px] text-slate-500">${escapeHtml(item.subtitle)}</p>
                    </div>
                    <p class="text-xs font-bold text-amber-700">${escapeHtml(item.metric)}</p>
                </div>
            `).join('')
            : '<p class="rounded-lg bg-slate-50 p-3 text-xs text-slate-500">No active opportunities or projects.</p>';
    }

    function destroyChart(name) {
        if (charts[name]) {
            charts[name].destroy();
        }
    }

    function buildCharts(data) {
        const labels = data.labels || [];
        const chartData = data.charts || {};

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        font: { size: 11, weight: '600' },
                    },
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    titleFont: { size: 12, weight: '700' },
                    bodyFont: { size: 11 },
                    padding: 10,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(100, 116, 139, 0.15)' },
                    ticks: {
                        callback: (value) => compactCurrencyFormatter.format(Number(value || 0)),
                    },
                },
                x: {
                    grid: { display: false },
                },
            },
        };

        destroyChart('portfolioTrend');
        charts.portfolioTrend = new Chart(document.getElementById('portfolioTrendChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Share Value',
                        data: chartData.portfolioTrend?.shareValue || [],
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(14, 165, 233, 0.12)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                    },
                    {
                        label: 'Portfolio Value',
                        data: chartData.portfolioTrend?.portfolioValue || [],
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.10)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                    },
                    {
                        label: 'Paid Dividends',
                        data: chartData.portfolioTrend?.dividendsPaid || [],
                        borderColor: '#a855f7',
                        backgroundColor: 'rgba(168, 85, 247, 0.10)',
                        fill: false,
                        tension: 0.35,
                        borderWidth: 2.5,
                    },
                ],
            },
            options: commonOptions,
        });

        destroyChart('cashFlow');
        charts.cashFlow = new Chart(document.getElementById('cashFlowChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Deposits',
                        data: chartData.cashFlow?.deposits || [],
                        backgroundColor: 'rgba(16, 185, 129, 0.75)',
                    },
                    {
                        label: 'Withdrawals',
                        data: chartData.cashFlow?.withdrawals || [],
                        backgroundColor: 'rgba(239, 68, 68, 0.75)',
                    },
                    {
                        label: 'Loan Payments',
                        data: chartData.cashFlow?.loanPayments || [],
                        backgroundColor: 'rgba(245, 158, 11, 0.75)',
                    },
                    {
                        type: 'line',
                        label: 'Net Cash',
                        data: chartData.cashFlow?.net || [],
                        borderColor: '#1d4ed8',
                        backgroundColor: 'rgba(29, 78, 216, 0.12)',
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: false,
                    },
                ],
            },
            options: commonOptions,
        });

        destroyChart('dividendSplit');
        charts.dividendSplit = new Chart(document.getElementById('dividendSplitChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Paid',
                        data: chartData.dividendSplit?.paid || [],
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.15)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                    },
                    {
                        label: 'Pending',
                        data: chartData.dividendSplit?.pending || [],
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.12)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                    },
                ],
            },
            options: commonOptions,
        });

        destroyChart('shareAccumulation');
        charts.shareAccumulation = new Chart(document.getElementById('shareAccumulationChart'), {
            data: {
                labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Purchased Units',
                        data: chartData.shares?.purchases || [],
                        backgroundColor: 'rgba(6, 182, 212, 0.75)',
                    },
                    {
                        type: 'line',
                        label: 'Cumulative Units',
                        data: chartData.shares?.cumulative || [],
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15, 118, 110, 0.15)',
                        tension: 0.35,
                        fill: true,
                        borderWidth: 2.5,
                    },
                ],
            },
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(100, 116, 139, 0.15)' },
                        ticks: {
                            callback: (value) => fmtNumber(value, 0),
                        },
                    },
                },
            },
        });

        destroyChart('assetAllocation');
        charts.assetAllocation = new Chart(document.getElementById('assetAllocationChart'), {
            type: 'doughnut',
            data: {
                labels: chartData.assetAllocation?.labels || [],
                datasets: [{
                    data: chartData.assetAllocation?.values || [],
                    backgroundColor: ['#0ea5e9', '#22c55e', '#a855f7', '#f97316'],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 10,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: commonOptions.plugins,
            },
        });

        destroyChart('projectSignal');
        charts.projectSignal = new Chart(document.getElementById('projectSignalChart'), {
            type: 'bar',
            data: {
                labels: chartData.projectSummary?.labels || [],
                datasets: [{
                    label: 'Count',
                    data: chartData.projectSummary?.values || [],
                    backgroundColor: ['#10b981', '#3b82f6', '#64748b', '#f59e0b'],
                    borderRadius: 8,
                }],
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: { display: false },
                },
                scales: {
                    ...commonOptions.scales,
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => fmtNumber(value, 0),
                        },
                    },
                },
            },
        });

        destroyChart('riskSummary');
        charts.riskSummary = new Chart(document.getElementById('riskSummaryChart'), {
            type: 'pie',
            data: {
                labels: chartData.riskSummary?.labels || [],
                datasets: [{
                    data: chartData.riskSummary?.values || [],
                    backgroundColor: ['#16a34a', '#eab308', '#ef4444'],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: commonOptions.plugins,
            },
        });
    }

    function refreshDashboard(data) {
        dashboardState = data;
        updateStats(data.stats || {});
        renderInsightCards(data.insights || {});
        renderRecentLists(data);
        buildCharts(data);
    }

    document.getElementById('yearFilter').addEventListener('change', async function (event) {
        const year = event.target.value;
        try {
            const response = await fetch(`{{ route('shareholder.dashboard.data') }}?year=${encodeURIComponent(year)}`, {
                headers: { 'Accept': 'application/json' },
            });
            const payload = await response.json();
            if (!response.ok) {
                throw new Error(payload.error || 'Failed to load dashboard data.');
            }
            refreshDashboard(payload);
        } catch (error) {
            console.error(error);
        }
    });

    refreshDashboard(dashboardState);
</script>
