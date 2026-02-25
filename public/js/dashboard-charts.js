// Gradient helper
function createGradient(ctx, color1, color2) {
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, color1);
    gradient.addColorStop(1, color2);
    return gradient;
}

// Initialize charts with advanced features
function initDashboardCharts(stats, monthlyData) {
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 13 },
                cornerRadius: 8
            }
        },
        animation: { duration: 1000, easing: 'easeInOutQuart' }
    };

    // Growth Chart with gradients
    const growthCtx = document.getElementById('growthChart')?.getContext('2d');
    if (growthCtx) {
        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: monthlyData.months,
                datasets: [{
                    label: 'Members',
                    data: monthlyData.members,
                    borderColor: '#3b82f6',
                    backgroundColor: createGradient(growthCtx, 'rgba(59, 130, 246, 0.6)', 'rgba(59, 130, 246, 0.08)'),
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3
                }, {
                    label: 'Loans',
                    data: monthlyData.loans,
                    borderColor: '#a855f7',
                    backgroundColor: createGradient(growthCtx, 'rgba(168, 85, 247, 0.6)', 'rgba(168, 85, 247, 0.08)'),
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    pointBackgroundColor: '#a855f7',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3
                }, {
                    label: 'Transactions',
                    data: monthlyData.transactions,
                    borderColor: '#22c55e',
                    backgroundColor: createGradient(growthCtx, 'rgba(34, 197, 94, 0.6)', 'rgba(34, 197, 94, 0.08)'),
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    pointBackgroundColor: '#22c55e',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: { position: 'bottom', labels: { padding: 15, font: { size: 12, weight: 'bold' }, usePointStyle: true } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' }, ticks: { font: { size: 11 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    // Distribution Chart with click interaction
    const distEl = document.getElementById('distributionChart');
    if (distEl) {
        const distChart = new Chart(distEl, {
            type: 'doughnut',
            data: {
                labels: ['Members', 'Loans', 'Projects', 'Fundraising'],
                datasets: [{
                    data: [stats.totalMembers, stats.totalLoans, stats.totalProjects, stats.activeFundraisings],
                    backgroundColor: ['#3b82f6', '#a855f7', '#6366f1', '#ec4899'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 15
                }],
                originalData: [stats.totalMembers, stats.totalLoans, stats.totalProjects, stats.activeFundraisings],
                originalColors: ['#3b82f6', '#a855f7', '#6366f1', '#ec4899']
            },
            options: {
                ...commonOptions,
                cutout: '65%',
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        distChart.data.datasets[0].data = distChart.data.originalData.map((v, i) => i === idx ? v * 1.1 : v);
                        distChart.data.datasets[0].backgroundColor = distChart.data.originalColors.map((c, i) => i === idx ? c : c + '80');
                        distChart.update();
                    } else {
                        distChart.data.datasets[0].data = [...distChart.data.originalData];
                        distChart.data.datasets[0].backgroundColor = [...distChart.data.originalColors];
                        distChart.update();
                    }
                },
                plugins: {
                    ...commonOptions.plugins,
                    legend: { position: 'bottom', labels: { padding: 15, font: { size: 12, weight: 'bold' }, usePointStyle: true } }
                }
            }
        });
    }

    // Loan Status Chart with center text
    const loanEl = document.getElementById('loanStatusChart');
    if (loanEl) {
        const loanChart = new Chart(loanEl, {
            type: 'pie',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    data: [stats.approvedLoans, stats.pendingLoans, stats.totalLoans - stats.approvedLoans - stats.pendingLoans],
                    backgroundColor: ['#22c55e', '#eab308', '#ef4444'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 15
                }],
                originalData: [stats.approvedLoans, stats.pendingLoans, stats.totalLoans - stats.approvedLoans - stats.pendingLoans],
                expandedIndex: null
            },
            options: {
                ...commonOptions,
                onClick: (e, elements, chart) => {
                    chart.data.expandedIndex = elements.length > 0 ? elements[0].index : null;
                    chart.update();
                },
                plugins: {
                    ...commonOptions.plugins,
                    legend: { position: 'bottom', labels: { padding: 15, font: { size: 12, weight: 'bold' }, usePointStyle: true } }
                }
            },
            plugins: [{
                id: 'centerText',
                afterDatasetsDraw: (chart) => {
                    if (chart.data.expandedIndex !== null) {
                        const ctx = chart.ctx;
                        const centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                        const centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;
                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillStyle = '#1f2937';
                        ctx.font = 'bold 24px Arial';
                        ctx.fillText(chart.data.originalData[chart.data.expandedIndex], centerX, centerY);
                        ctx.fillStyle = '#6b7280';
                        ctx.font = '12px Arial';
                        ctx.fillText(chart.data.labels[chart.data.expandedIndex], centerX, centerY - 20);
                        ctx.restore();
                    }
                }
            }]
        });
    }

    // Loan Amount Chart
    const loanAmountEl = document.getElementById('loanAmountChart');
    if (loanAmountEl) {
        new Chart(loanAmountEl, {
            type: 'bar',
            data: {
                labels: monthlyData.months,
                datasets: [{
                    label: 'Loan Count',
                    data: monthlyData.loans,
                    backgroundColor: 'rgba(168, 85, 247, 0.8)',
                    borderColor: '#a855f7',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: '#a855f7'
                }]
            },
            options: {
                ...commonOptions,
                plugins: { ...commonOptions.plugins, legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' }, ticks: { font: { size: 11 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    // Member Growth Chart
    const memberGrowthEl = document.getElementById('memberGrowthChart');
    if (memberGrowthEl) {
        const memberCtx = memberGrowthEl.getContext('2d');
        new Chart(memberCtx, {
            type: 'line',
            data: {
                labels: monthlyData.months,
                datasets: [{
                    label: 'New Members',
                    data: monthlyData.members,
                    borderColor: '#3b82f6',
                    backgroundColor: createGradient(memberCtx, 'rgba(59, 130, 246, 0.6)', 'rgba(59, 130, 246, 0.08)'),
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3
                }]
            },
            options: {
                ...commonOptions,
                plugins: { ...commonOptions.plugins, legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' }, ticks: { font: { size: 11 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    // Member Activity Chart
    const activityEl = document.getElementById('memberActivityChart');
    if (activityEl) {
        new Chart(activityEl, {
            type: 'radar',
            data: {
                labels: ['Loans', 'Savings', 'Transactions', 'Projects', 'Fundraising'],
                datasets: [{
                    label: 'Activity Level',
                    data: [stats.totalLoans, stats.totalSavings/1000000, stats.totalTransactions, stats.totalProjects, stats.activeFundraisings],
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: '#3b82f6',
                    borderWidth: 3,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                ...commonOptions,
                plugins: { ...commonOptions.plugins, legend: { display: false } },
                scales: {
                    r: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.1)' },
                        angleLines: { color: 'rgba(0, 0, 0, 0.1)' },
                        ticks: { backdropColor: 'transparent', font: { size: 10 } }
                    }
                }
            }
        });
    }

    // Revenue Chart
    const revenueEl = document.getElementById('revenueChart');
    if (revenueEl) {
        new Chart(revenueEl, {
            type: 'bar',
            data: {
                labels: monthlyData.months,
                datasets: [{
                    label: 'Revenue',
                    data: monthlyData.transactions,
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: '#22c55e',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: '#22c55e'
                }, {
                    label: 'Expenses',
                    data: monthlyData.transactions.map(v => v * 0.7),
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: '#ef4444',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: '#ef4444'
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: { position: 'bottom', labels: { padding: 15, font: { size: 12, weight: 'bold' }, usePointStyle: true } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' }, ticks: { font: { size: 11 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    // Asset Chart
    const assetEl = document.getElementById('assetChart');
    if (assetEl) {
        new Chart(assetEl, {
            type: 'polarArea',
            data: {
                labels: ['Savings', 'Loans', 'Investments', 'Cash'],
                datasets: [{
                    data: [stats.totalSavings/1000000, stats.totalLoanAmount/1000000, stats.totalAssets/2000000, stats.totalAssets/3000000],
                    backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(245, 158, 11, 0.8)'],
                    borderColor: ['#3b82f6', '#a855f7', '#22c55e', '#f59e0b'],
                    borderWidth: 2
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: { position: 'bottom', labels: { padding: 15, font: { size: 12, weight: 'bold' }, usePointStyle: true } }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.1)' },
                        ticks: { backdropColor: 'transparent', font: { size: 10 } }
                    }
                }
            }
        });
    }
}
