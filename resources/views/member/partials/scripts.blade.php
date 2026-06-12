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

// Growth Chart
allCharts.growth = new Chart(document.getElementById('growthChart'), {
    type: 'line',
    data: {
        labels: [...monthlyData.months, 'Predicted'],
        datasets: [{
            label: 'Dividends (M)',
            data: [...monthlyData.dividends, monthlyData.predictions.dividends],
            borderColor: '#a855f7',
            backgroundColor: 'rgba(168, 85, 247, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            segment: { borderDash: ctx => ctx.p1DataIndex === monthlyData.dividends.length ? [5, 5] : undefined }
        }, {
            label: 'Share Value (M)',
            data: [...monthlyData.shareValue, monthlyData.predictions.shareValue],
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            segment: { borderDash: ctx => ctx.p1DataIndex === monthlyData.shareValue.length ? [5, 5] : undefined }
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
        labels: ['Shares', 'Dividends', 'Portfolio', 'Projects'],
        datasets: [{
            data: [stats.totalShares, stats.totalDividends/1000000, stats.portfolioValue/1000000, stats.totalProjects],
            backgroundColor: ['#3b82f6', '#a855f7', '#22c55e', '#6366f1'],
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

// Dividend Chart
allCharts.dividend = new Chart(document.getElementById('dividendChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.months,
        datasets: [{
            label: 'Dividends (UGX M)',
            data: monthlyData.dividends,
            backgroundColor: 'rgba(168, 85, 247, 0.8)',
            borderColor: '#a855f7',
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        ...commonOptions,
        plugins: { ...commonOptions.plugins, legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});

// Share Value Chart
allCharts.shareValue = new Chart(document.getElementById('shareValueChart'), {
    type: 'line',
    data: {
        labels: monthlyData.months,
        datasets: [{
            label: 'Share Value (UGX M)',
            data: monthlyData.shareValue,
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34, 197, 94, 0.2)',
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

// Performance Chart
allCharts.performance = new Chart(document.getElementById('performanceChart'), {
    type: 'line',
    data: {
        labels: monthlyData.months,
        datasets: [{
            label: 'Performance (%)',
            data: monthlyData.portfolioPerformance,
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

// Investment Chart
allCharts.investment = new Chart(document.getElementById('investmentChart'), {
    type: 'polarArea',
    data: {
        labels: ['Shares', 'Dividends', 'Portfolio', 'Investments'],
        datasets: [{
            data: [stats.shareValue/1000000, stats.totalDividends/1000000, stats.portfolioValue/1000000, stats.totalInvestments/1000000],
            backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(245, 158, 11, 0.8)'],
            borderColor: ['#3b82f6', '#a855f7', '#22c55e', '#f59e0b'],
            borderWidth: 2
        }]
    },
    options: commonOptions
});

// ROI Chart
allCharts.roi = new Chart(document.getElementById('roiChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.months,
        datasets: [{
            label: 'Project ROI (%)',
            data: monthlyData.projectROI,
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: '#22c55e',
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        ...commonOptions,
        plugins: { ...commonOptions.plugins, legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});

// Allocation Chart
allCharts.allocation = new Chart(document.getElementById('allocationChart'), {
    type: 'pie',
    data: {
        labels: ['Active Projects', 'Completed', 'Pending'],
        datasets: [{
            data: [stats.activeProjects, stats.totalProjects - stats.activeProjects, 0],
            backgroundColor: ['#22c55e', '#3b82f6', '#eab308'],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 20
        }]
    },
    options: commonOptions
});

// Year filter
document.getElementById('yearFilter').addEventListener('change', function(e) {
    const year = e.target.value;
    document.getElementById('yearText').textContent = year === 'all' ? '2023 to ' + new Date().getFullYear() : year;

    fetch(`{{ route('shareholder.dashboard.data') }}?year=${year}`)
        .then(response => response.json())
        .then(data => {
            window.monthlyData = data;
            if (data.stats) window.stats = data.stats;
            updateAllCharts();
        })
        .catch(error => console.error('Error:', error));
});

function updateAllCharts() {
    const isEmpty = window.monthlyData.dividends.every(v => v === 0);
    
    ['growth', 'distribution', 'dividend', 'shareValue', 'performance', 'investment', 'roi', 'allocation'].forEach(chart => {
        const emptyDiv = document.getElementById(chart + 'Empty');
        if (isEmpty && emptyDiv) {
            emptyDiv.classList.remove('hidden');
            window.allCharts[chart].canvas.style.display = 'none';
        } else if (emptyDiv) {
            emptyDiv.classList.add('hidden');
            window.allCharts[chart].canvas.style.display = 'block';
        }
    });

    allCharts.growth.data.labels = [...window.monthlyData.months, 'Predicted'];
    allCharts.growth.data.datasets[0].data = [...window.monthlyData.dividends, window.monthlyData.predictions.dividends];
    allCharts.growth.data.datasets[1].data = [...window.monthlyData.shareValue, window.monthlyData.predictions.shareValue];
    allCharts.growth.update();

    allCharts.distribution.data.datasets[0].data = [window.stats.totalShares, window.stats.totalDividends/1000000, window.stats.portfolioValue/1000000, window.stats.totalProjects];
    allCharts.distribution.update();

    allCharts.dividend.data.labels = window.monthlyData.months;
    allCharts.dividend.data.datasets[0].data = window.monthlyData.dividends;
    allCharts.dividend.update();

    allCharts.shareValue.data.labels = window.monthlyData.months;
    allCharts.shareValue.data.datasets[0].data = window.monthlyData.shareValue;
    allCharts.shareValue.update();

    allCharts.performance.data.labels = window.monthlyData.months;
    allCharts.performance.data.datasets[0].data = window.monthlyData.portfolioPerformance;
    allCharts.performance.update();

    allCharts.investment.data.datasets[0].data = [window.stats.shareValue/1000000, window.stats.totalDividends/1000000, window.stats.portfolioValue/1000000, window.stats.totalInvestments/1000000];
    allCharts.investment.update();

    allCharts.roi.data.labels = window.monthlyData.months;
    allCharts.roi.data.datasets[0].data = window.monthlyData.projectROI;
    allCharts.roi.update();

    allCharts.allocation.data.datasets[0].data = [window.stats.activeProjects, window.stats.totalProjects - window.stats.activeProjects, 0];
    allCharts.allocation.update();
}

window.updateAllCharts = updateAllCharts;
</script>

<style>
@keyframes slide-right {
    0% { width: 0%; }
    100% { width: 100%; }
}
.animate-slide-right {
    animation: slide-right 5s ease-out forwards;
}
@keyframes slide-text {
    0% { left: 0%; opacity: 1; }
    95% { opacity: 1; }
    100% { left: 100%; opacity: 0; }
}
.animate-slide-text {
    animation: slide-text 5s ease-out forwards;
}
</style>

