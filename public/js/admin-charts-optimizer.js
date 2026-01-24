// Admin Charts Optimizer - Modern, Dynamic & Beautiful Charts
let chartInstances = {};

// Modern gradient colors
const gradients = {
    blue: ['rgba(59, 130, 246, 0.8)', 'rgba(147, 197, 253, 0.3)'],
    green: ['rgba(16, 185, 129, 0.8)', 'rgba(110, 231, 183, 0.3)'],
    purple: ['rgba(139, 92, 246, 0.8)', 'rgba(196, 181, 253, 0.3)'],
    orange: ['rgba(249, 115, 22, 0.8)', 'rgba(253, 186, 116, 0.3)'],
    pink: ['rgba(236, 72, 153, 0.8)', 'rgba(251, 207, 232, 0.3)']
};

// Create gradient
function createGradient(ctx, color1, color2) {
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, color1);
    gradient.addColorStop(1, color2);
    return gradient;
}

// Initialize all charts with modern styling
function initChartsOptimized(data) {
    if (!data) {
        console.error('No data provided to charts');
        return;
    }
    
    console.log('Chart data received:', data);
    
    const chartConfigs = {
        membersChart: {
            type: 'line',
            getData: (data, ctx) => ({
                labels: data.membersGrowth?.map(d => d.month) || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Members',
                    data: data.membersGrowth?.map(d => d.count) || [5, 10, 15, 20, 25, data.totalMembers || 30],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: ctx ? createGradient(ctx, 'rgba(59, 130, 246, 0.5)', 'rgba(59, 130, 246, 0.05)') : 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(59, 130, 246)',
                    pointHoverBorderWidth: 3
                }]
            }),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: (context) => context.parsed.y + ' Members'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                        ticks: { font: { size: 11 }, color: '#6b7280' }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { size: 11 }, color: '#6b7280' }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        },
        financialChart: {
            type: 'bar',
            getData: (data, ctx) => ({
                labels: ['Savings', 'Loans', 'Deposits', 'Withdrawals', 'Net Balance'],
                datasets: [{
                    label: 'Amount (UGX)',
                    data: [
                        data.totalSavings || 0,
                        data.totalLoanAmount || 0,
                        data.totalDeposits || 0,
                        data.totalWithdrawals || 0,
                        data.netBalance || 0
                    ],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ],
                    borderColor: [
                        'rgb(16, 185, 129)',
                        'rgb(249, 115, 22)',
                        'rgb(59, 130, 246)',
                        'rgb(239, 68, 68)',
                        'rgb(139, 92, 246)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            }),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: (context) => 'UGX ' + context.parsed.y.toLocaleString(),
                            afterLabel: (context) => {
                                if (context.label === 'Net Balance') {
                                    return '\nFormula: Savings + Deposits - Withdrawals - Loans';
                                }
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                        ticks: {
                            font: { size: 11 },
                            color: '#6b7280',
                            callback: (value) => 'UGX ' + (value / 1000000).toFixed(1) + 'M'
                        }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { size: 11 }, color: '#6b7280' }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutBounce'
                }
            }
        },
        loanStatusChart: {
            type: 'doughnut',
            getData: (data) => ({
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    data: [
                        data.approvedLoans || 0,
                        data.pendingLoans || 0,
                        data.rejectedLoans || 0
                    ],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.9)',
                        'rgba(249, 115, 22, 0.9)',
                        'rgba(239, 68, 68, 0.9)'
                    ],
                    borderColor: '#fff',
                    borderWidth: 4,
                    hoverOffset: 15,
                    hoverBorderWidth: 5
                }]
            }),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '600' },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: (context) => context.label + ': ' + context.parsed + ' loans'
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        },
        transactionTypesChart: {
            type: 'polarArea',
            getData: (data) => ({
                labels: ['Deposits', 'Withdrawals', 'Transfers', 'Fees'],
                datasets: [{
                    data: [
                        data.transactionStats?.deposits || 10,
                        data.transactionStats?.withdrawals || 5,
                        data.transactionStats?.transfers || 3,
                        data.transactionStats?.fees || 2
                    ],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(139, 92, 246, 0.7)',
                        'rgba(249, 115, 22, 0.7)'
                    ],
                    borderColor: '#fff',
                    borderWidth: 3
                }]
            }),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            font: { size: 11, weight: '600' },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    r: {
                        ticks: { display: false },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        },
        revenueChart: {
            type: 'line',
            getData: (data, ctx) => ({
                labels: data.savingsGrowth?.map(d => d.month) || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Savings',
                    data: data.savingsGrowth?.map(d => parseFloat(d.total) || 0) || [1000000, 1500000, 2000000, 1800000, 2200000, 2500000],
                    borderColor: 'rgb(249, 115, 22)',
                    backgroundColor: ctx ? createGradient(ctx, 'rgba(249, 115, 22, 0.5)', 'rgba(249, 115, 22, 0.05)') : 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBackgroundColor: 'rgb(249, 115, 22)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(249, 115, 22)',
                    pointHoverBorderWidth: 3
                }]
            }),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: (context) => 'UGX ' + context.parsed.y.toLocaleString()
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                        ticks: {
                            font: { size: 11 },
                            color: '#6b7280',
                            callback: (value) => 'UGX ' + (value / 1000000).toFixed(1) + 'M'
                        }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { size: 11 }, color: '#6b7280' }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        },
        projectChart: {
            type: 'bar',
            getData: (data) => ({
                labels: data.projects?.map(p => p.name) || ['Project A', 'Project B', 'Project C'],
                datasets: [{
                    label: 'Progress (%)',
                    data: data.projects?.map(p => p.progress) || [75, 50, 90],
                    backgroundColor: data.projects?.map((p, i) => {
                        const colors = [
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(249, 115, 22, 0.8)'
                        ];
                        return colors[i % colors.length];
                    }) || ['rgba(139, 92, 246, 0.8)', 'rgba(236, 72, 153, 0.8)', 'rgba(59, 130, 246, 0.8)'],
                    borderColor: data.projects?.map((p, i) => {
                        const colors = [
                            'rgb(139, 92, 246)',
                            'rgb(236, 72, 153)',
                            'rgb(59, 130, 246)',
                            'rgb(16, 185, 129)',
                            'rgb(249, 115, 22)'
                        ];
                        return colors[i % colors.length];
                    }) || ['rgb(139, 92, 246)', 'rgb(236, 72, 153)', 'rgb(59, 130, 246)'],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            }),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: (context) => 'Progress: ' + context.parsed.x + '%'
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                        ticks: {
                            font: { size: 11 },
                            color: '#6b7280',
                            callback: (value) => value + '%'
                        }
                    },
                    y: {
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { size: 11 }, color: '#6b7280' }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutBounce'
                }
            }
        }
    };

    // Create or update charts
    Object.keys(chartConfigs).forEach(chartId => {
        const canvas = document.getElementById(chartId);
        if (!canvas) {
            console.warn('Canvas not found:', chartId);
            return;
        }

        const config = chartConfigs[chartId];
        const ctx = canvas.getContext('2d');
        const chartData = config.getData(data, ctx);

        if (chartInstances[chartId]) {
            // Update existing chart
            chartInstances[chartId].data = chartData;
            chartInstances[chartId].update('none');
        } else {
            // Create new chart
            chartInstances[chartId] = new Chart(ctx, {
                type: config.type,
                data: chartData,
                options: config.options
            });
        }
    });
}

// Update charts with new data
function updateCharts(data) {
    if (data) {
        initChartsOptimized(data);
    }
}

// Destroy all charts
function destroyCharts() {
    Object.values(chartInstances).forEach(chart => chart?.destroy());
    chartInstances = {};
}

// Export functions
window.adminChartsOptimizer = {
    init: initChartsOptimized,
    update: updateCharts,
    destroy: destroyCharts
};
