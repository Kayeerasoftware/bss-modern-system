<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let monthlyData = @json($monthlyData);
let stats = @json($stats);
let allCharts = {};

window.monthlyData = monthlyData;
window.stats = stats;
window.allCharts = allCharts;

const commonOptions = {
    responsive: true,
    maintainAspectRatio: true,
    aspectRatio: 2,
    interaction: { mode: 'index', intersect: true },
    plugins: {
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.9)',
            padding: 16,
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
            cornerRadius: 8,
            displayColors: true
        },
        legend: {
            position: 'bottom',
            labels: { padding: 15, font: { size: 12, weight: 'bold' }, usePointStyle: true }
        }
    },
    animation: { duration: 1000, easing: 'easeInOutQuart' }
};

// Trends Chart
allCharts.trends = new Chart(document.getElementById('trendsChart'), {
    type: 'line',
    data: {
        labels: [...monthlyData.months, 'Predicted'],
        datasets: [{
            label: 'Deposits (M)',
            data: [...monthlyData.deposits, monthlyData.predictions.deposits],
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            segment: { borderDash: ctx => ctx.p1DataIndex === monthlyData.deposits.length ? [5, 5] : undefined }
        }, {
            label: 'Withdrawals (M)',
            data: [...monthlyData.withdrawals, monthlyData.predictions.withdrawals],
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            segment: { borderDash: ctx => ctx.p1DataIndex === monthlyData.withdrawals.length ? [5, 5] : undefined }
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});

// Distribution Chart
allCharts.distribution = new Chart(document.getElementById('distributionChart'), {
    type: 'doughnut',
    data: {
        labels: ['Deposits', 'Withdrawals', 'Net Cash'],
        datasets: [{
            data: [stats.todayDeposits/1000000, stats.todayWithdrawals/1000000, Math.abs(stats.todayNet/1000000)],
            backgroundColor: ['#22c55e', '#ef4444', '#3b82f6'],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 20
        }]
    },
    options: {
        ...commonOptions,
        cutout: '70%'
    }
});

// Deposits Chart
allCharts.deposits = new Chart(document.getElementById('depositsChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.months,
        datasets: [{
            label: 'Deposits (UGX M)',
            data: monthlyData.deposits,
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: '#22c55e',
            borderWidth: 2,
            borderRadius: 8
        }, {
            label: 'Withdrawals (UGX M)',
            data: monthlyData.withdrawals,
            backgroundColor: 'rgba(239, 68, 68, 0.8)',
            borderColor: '#ef4444',
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});

// Volume Chart
allCharts.volume = new Chart(document.getElementById('volumeChart'), {
    type: 'line',
    data: {
        labels: monthlyData.months,
        datasets: [{
            label: 'Transaction Count',
            data: monthlyData.transactions,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderWidth: 3,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});

// Net Cash Chart
allCharts.netCash = new Chart(document.getElementById('netCashChart'), {
    type: 'line',
    data: {
        labels: monthlyData.months,
        datasets: [{
            label: 'Net Cash Flow (UGX M)',
            data: monthlyData.netCash,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            segment: {
                borderColor: ctx => ctx.p0.parsed.y < 0 ? '#ef4444' : '#22c55e',
                backgroundColor: ctx => ctx.p0.parsed.y < 0 ? 'rgba(239, 68, 68, 0.2)' : 'rgba(34, 197, 94, 0.2)'
            }
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});

// Analysis Chart
allCharts.analysis = new Chart(document.getElementById('analysisChart'), {
    type: 'polarArea',
    data: {
        labels: ['Avg Deposits', 'Avg Withdrawals', 'Total Net', 'Avg Transactions'],
        datasets: [{
            data: [
                monthlyData.analytics.avgDeposits, 
                monthlyData.analytics.avgWithdrawals, 
                Math.abs(monthlyData.analytics.totalNet), 
                monthlyData.analytics.avgTransactions
            ],
            backgroundColor: ['rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)'],
            borderColor: ['#22c55e', '#ef4444', '#3b82f6', '#a855f7'],
            borderWidth: 2
        }]
    },
    options: commonOptions
});

// Year filter
document.getElementById('yearFilter').addEventListener('change', function(e) {
    const year = e.target.value;
    document.getElementById('yearText').textContent = year === 'all' ? '2023 to ' + new Date().getFullYear() : year;

    fetch(`{{ route('cashier.dashboard.data') }}?year=${year}`)
        .then(response => response.json())
        .then(data => {
            window.monthlyData = data;
            if (data.stats) window.stats = data.stats;
            updateAllCharts();
        })
        .catch(error => console.error('Error:', error));
});

function updateAllCharts() {
    const isEmpty = window.monthlyData.deposits.every(v => v === 0);
    
    ['trends', 'distribution', 'deposits', 'volume', 'netCash', 'analysis'].forEach(chart => {
        const emptyDiv = document.getElementById(chart + 'Empty');
        if (isEmpty && emptyDiv) {
            emptyDiv.classList.remove('hidden');
            window.allCharts[chart].canvas.style.display = 'none';
        } else if (emptyDiv) {
            emptyDiv.classList.add('hidden');
            window.allCharts[chart].canvas.style.display = 'block';
        }
    });

    allCharts.trends.data.labels = [...window.monthlyData.months, 'Predicted'];
    allCharts.trends.data.datasets[0].data = [...window.monthlyData.deposits, window.monthlyData.predictions.deposits];
    allCharts.trends.data.datasets[1].data = [...window.monthlyData.withdrawals, window.monthlyData.predictions.withdrawals];
    allCharts.trends.update();

    allCharts.distribution.data.datasets[0].data = [window.stats.todayDeposits/1000000, window.stats.todayWithdrawals/1000000, Math.abs((window.stats.todayDeposits - window.stats.todayWithdrawals)/1000000)];
    allCharts.distribution.update();

    allCharts.deposits.data.labels = window.monthlyData.months;
    allCharts.deposits.data.datasets[0].data = window.monthlyData.deposits;
    allCharts.deposits.data.datasets[1].data = window.monthlyData.withdrawals;
    allCharts.deposits.update();

    allCharts.volume.data.labels = window.monthlyData.months;
    allCharts.volume.data.datasets[0].data = window.monthlyData.transactions;
    allCharts.volume.update();

    allCharts.netCash.data.labels = window.monthlyData.months;
    allCharts.netCash.data.datasets[0].data = window.monthlyData.netCash;
    allCharts.netCash.update();

    allCharts.analysis.data.datasets[0].data = [
        window.monthlyData.analytics.avgDeposits,
        window.monthlyData.analytics.avgWithdrawals,
        Math.abs(window.monthlyData.analytics.totalNet),
        window.monthlyData.analytics.avgTransactions
    ];
    allCharts.analysis.update();
}

window.updateAllCharts = updateAllCharts;
</script>
