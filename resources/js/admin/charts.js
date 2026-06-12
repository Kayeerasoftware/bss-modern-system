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
    console.log('Role Distribution:', data.roleDistribution);

    const chartConfigs = {
        membersChart: {
            type: 'line',
            getData: (data, ctx) => {
                // Ensure we have all 12 months with proper data
                const allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const monthlyData = data.membersGrowth || [];

                // Create a map of month data for quick lookup
                const monthMap = new Map();
                monthlyData.forEach(item => {
                    monthMap.set(item.month, item.count);
                });

                // Fill in all 12 months, using 0 for missing months
                const labels = allMonths;
                const dataValues = allMonths.map(month => monthMap.get(month) || 0);

                // Calculate maximum value for proper scaling
                const maxValue = Math.max(...dataValues, data.totalMembers || 0);

                return {
                    labels: labels,
                    datasets: [{
                        label: `Member Growth ${data.selectedYear || new Date().getFullYear()}`,
                        data: dataValues,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: ctx ? createGradient(ctx, 'rgba(59, 130, 246, 0.6)', 'rgba(59, 130, 246, 0.08)') : 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 4,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 6,
                        pointHoverRadius: 10,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 3,
                        pointHoverBackgroundColor: '#ffffff',
                        pointHoverBorderColor: 'rgb(59, 130, 246)',
                        pointHoverBorderWidth: 4,
                        pointStyle: 'circle',
                        segment: {
                            borderColor: (ctx) => {
                                const chart = ctx.chart;
                                const { ctx: context } = chart;
                                const gradient = context.createLinearGradient(0, 0, 0, 400);
                                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
                                gradient.addColorStop(1, 'rgba(59, 130, 246, 0.1)');
                                return gradient;
                            }
                        }
                    }]
                };
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                    axis: 'x'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        padding: 14,
                        cornerRadius: 10,
                        borderWidth: 1,
                        borderColor: 'rgba(59, 130, 246, 0.3)',
                        displayColors: false,
                        callbacks: {
                            title: (context) => {
                                return context[0].label + ' 2024';
                            },
                            label: (context) => {
                                const value = context.parsed.y;
                                const total = data.totalMembers || 0;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0';
                                return [
                                    `Members: ${value.toLocaleString()}`,
                                    `Growth: +${value - (data.membersGrowth?.[context.dataIndex - 1]?.count || 0)}`,
                                    `Market Share: ${percentage}%`
                                ];
                            },
                            afterLabel: (context) => {
                                if (context.dataIndex === (data.membersGrowth?.length || 6) - 1) {
                                    return [
                                        '',
                                        '📈 Trend: Upward trajectory',
                                        '🎯 Target: 500 members by Q4'
                                    ];
                                }
                                return '';
                            }
                        }
                    },
                    annotation: {
                        annotations: {
                            targetLine: {
                                type: 'line',
                                scaleID: 'y',
                                value: 500,
                                borderColor: 'rgba(34, 197, 94, 0.6)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    content: 'Target: 500',
                                    enabled: true,
                                    position: 'center',
                                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                    color: '#ffffff',
                                    font: { size: 10, weight: 'bold' },
                                    padding: 6,
                                    borderRadius: 4
                                }
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false,
                            drawOnChartArea: true,
                            drawTicks: false
                        },
                        ticks: {
                            font: { size: 11, weight: '600' },
                            color: '#6b7280',
                            callback: function(value) {
                                if (value >= 1000) {
                                    return (value / 1000).toFixed(1) + 'K';
                                }
                                return value;
                            }
                        },
                        suggestedMax: data.totalMembers ? data.totalMembers * 1.2 : 100
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                            drawTicks: false
                        },
                        ticks: {
                            font: { size: 11, weight: '600' },
                            color: '#6b7280',
                            callback: function(value, index) {
                                return this.getLabelForValue(value);
                            }
                        }
                    }
                },
                animation: {
                    duration: 2500,
                    easing: 'easeInOutQuart',
                    animations: {
                        y: {
                            easing: 'easeOutQuart',
                            from: (ctx) => {
                                if (ctx.type === 'data' && ctx.mode === 'reset') {
                                    return ctx.context.parsed.y;
                                }
                                return ctx.chart.scales.y.getPixelForValue(0);
                            }
                        },
                        x: {
                            easing: 'easeOutQuart'
                        }
                    }
                },
                elements: {
                    point: {
                        hoverBorderWidth: 4,
                        hoverRadius: 10,
                        radius: 6
                    },
                    line: {
                        tension: 0.4,
                        borderWidth: 4
                    }
                },
                hover: {
                    mode: 'index',
                    intersect: false
                }
            },
            plugins: []
        },
        financialChart: {
            type: 'bar',
            getData: (data, ctx) => ({
                labels: ['Deposit', 'Withdrawal', 'Transfer', 'Loan Payment', 'Loan Request', 'Fundraising', 'Condolence'],
                datasets: [{
                    label: 'Total Amount (UGX)',
                    data: [
                        data.transactionTypeAmounts?.deposit || 0,
                        data.transactionTypeAmounts?.withdrawal || 0,
                        data.transactionTypeAmounts?.transfer || 0,
                        data.transactionTypeAmounts?.loan_payment || 0,
                        data.transactionTypeAmounts?.loan_request || 0,
                        data.transactionTypeAmounts?.fundraising || 0,
                        data.transactionTypeAmounts?.condolence || 0
                    ],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(107, 114, 128, 0.8)'
                    ],
                    borderColor: [
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                        'rgb(59, 130, 246)',
                        'rgb(139, 92, 246)',
                        'rgb(99, 102, 241)',
                        'rgb(236, 72, 153)',
                        'rgb(107, 114, 128)'
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
                            label: (context) => context.label + ': UGX ' + context.parsed.y.toLocaleString()
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
            getData: (data) => {
                const originalData = [
                    data.approvedLoans || 0,
                    data.pendingLoans || 0,
                    data.rejectedLoans || 0
                ];
                return {
                    labels: ['Approved', 'Pending', 'Rejected'],
                    datasets: [{
                        data: originalData,
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.9)',
                            'rgba(249, 115, 22, 0.9)',
                            'rgba(239, 68, 68, 0.9)'
                        ],
                        borderColor: '#fff',
                        borderWidth: 4,
                        hoverOffset: 15,
                        hoverBorderWidth: 5
                    }],
                    originalData: originalData,
                    originalColors: [
                        'rgba(16, 185, 129, 0.9)',
                        'rgba(249, 115, 22, 0.9)',
                        'rgba(239, 68, 68, 0.9)'
                    ],
                    highlightColors: [
                        'rgba(16, 185, 129, 1)',
                        'rgba(249, 115, 22, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    expandedIndex: null,
                    expandedLabel: '',
                    expandedValue: 0,
                    expandedPercentage: '0'
                };
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                onClick: (event, elements, chart) => {
                    const originalData = chart.data.originalData;

                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const total = originalData.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                        const percentage = total > 0 ? ((originalData[index] / total) * 100).toFixed(1) : '0';
                        const label = chart.data.labels[index];
                        const value = originalData[index];

                        chart.data.datasets[0].data = originalData.map((val, i) => {
                            return i === index ? val * 1.1 : val;
                        });

                        chart.data.datasets[0].backgroundColor = chart.data.originalColors.map((color, i) => {
                            return i === index ? chart.data.highlightColors[i] : color.replace('0.9', '0.5');
                        });

                        chart.data.expandedIndex = index;
                        chart.data.expandedLabel = label;
                        chart.data.expandedValue = value;
                        chart.data.expandedPercentage = percentage;

                        chart.update();
                    } else {
                        chart.data.datasets[0].data = [...originalData];
                        chart.data.datasets[0].backgroundColor = [...chart.data.originalColors];
                        chart.data.expandedIndex = null;
                        chart.update();
                    }
                },
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
                            label: (context) => {
                                const chart = chartInstances.loanStatusChart;
                                const originalData = chart.data.originalData;
                                const total = originalData.reduce((a, b) => a + b, 0);
                                const percentage = ((originalData[context.dataIndex] / total) * 100).toFixed(1);
                                return context.label + ': ' + originalData[context.dataIndex] + ' loans (' + percentage + '%)';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            },
            plugins: [{
                id: 'loanStatusText',
                afterDatasetsDraw: (chart) => {
                    if (chart.data.expandedIndex !== null) {
                        const ctx = chart.ctx;
                        const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                        const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;

                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';

                        ctx.fillStyle = '#6b7280';
                        ctx.font = '12px Arial';
                        ctx.fillText('Status', centerX, centerY - 30);

                        ctx.fillStyle = '#1f2937';
                        ctx.font = 'bold 16px Arial';
                        ctx.fillText(chart.data.expandedLabel, centerX, centerY - 5);

                        ctx.fillStyle = '#1f2937';
                        ctx.font = 'bold 24px Arial';
                        ctx.fillText(chart.data.expandedValue.toLocaleString('en-US'), centerX, centerY + 25);

                        ctx.fillStyle = '#6b7280';
                        ctx.font = '14px Arial';
                        ctx.fillText(chart.data.expandedPercentage + '%', centerX, centerY + 50);

                        ctx.restore();
                    }
                }
            }]
        },
        transactionTypesChart: {
            type: 'polarArea',
            getData: (data) => {
                const originalData = [
                    data.transactionTypeAmounts?.deposit || 0,
                    data.transactionTypeAmounts?.withdrawal || 0,
                    data.transactionTypeAmounts?.transfer || 0,
                    data.transactionTypeAmounts?.loan_payment || 0,
                    data.transactionTypeAmounts?.loan_request || 0,
                    data.transactionTypeAmounts?.fundraising || 0,
                    data.transactionTypeAmounts?.condolence || 0
                ];
                return {
                    labels: ['Deposit', 'Withdrawal', 'Transfer', 'Loan Payment', 'Loan Request', 'Fundraising', 'Condolence'],
                    datasets: [{
                        data: originalData,
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(239, 68, 68, 0.7)',
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(139, 92, 246, 0.7)',
                            'rgba(99, 102, 241, 0.7)',
                            'rgba(236, 72, 153, 0.7)',
                            'rgba(107, 114, 128, 0.7)'
                        ],
                        borderColor: '#fff',
                        borderWidth: 3
                    }],
                    originalData: originalData,
                    originalColors: [
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(139, 92, 246, 0.7)',
                        'rgba(99, 102, 241, 0.7)',
                        'rgba(236, 72, 153, 0.7)',
                        'rgba(107, 114, 128, 0.7)'
                    ],
                    highlightColors: [
                        'rgba(16, 185, 129, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(99, 102, 241, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(107, 114, 128, 1)'
                    ],
                    expandedIndex: null,
                    expandedLabel: '',
                    expandedValue: 0,
                    expandedPercentage: '0'
                };
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: (event, elements, chart) => {
                    const originalData = chart.data.originalData;

                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const total = originalData.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                        const percentage = total > 0 ? ((originalData[index] / total) * 100).toFixed(1) : '0';
                        const label = chart.data.labels[index];
                        const value = originalData[index];

                        // Keep all data, just make clicked one slightly bigger
                        chart.data.datasets[0].data = originalData.map((val, i) => {
                            return i === index ? val * 1.15 : val;
                        });

                        // Change colors - highlight clicked, dim others
                        chart.data.datasets[0].backgroundColor = chart.data.originalColors.map((color, i) => {
                            return i === index ? chart.data.highlightColors[i] : color.replace('0.7', '0.4');
                        });

                        chart.data.expandedIndex = index;
                        chart.data.expandedLabel = label;
                        chart.data.expandedValue = value;
                        chart.data.expandedPercentage = percentage;

                        chart.update();
                    } else {
                        // Click on empty area to reset
                        chart.data.datasets[0].data = [...originalData];
                        chart.data.datasets[0].backgroundColor = [...chart.data.originalColors];
                        chart.data.expandedIndex = null;
                        chart.update();
                    }
                },
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
                        cornerRadius: 8,
                        callbacks: {
                            label: (context) => {
                                const originalData = context.chart.data.originalData;
                                const total = originalData.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                                const percentage = total > 0 ? ((originalData[context.dataIndex] / total) * 100).toFixed(1) : '0';
                                const amount = originalData[context.dataIndex];
                                return context.label + ': UGX ' + amount.toLocaleString('en-US') + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        right: 150
                    }
                },
                scales: {
                    r: {
                        ticks: { display: false },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    }
                },
                animation: {
                    duration: 600,
                    easing: 'easeInOutQuart'
                }
            },
            plugins: [{
                id: 'expandedSegmentText',
                afterDatasetsDraw: (chart) => {
                    if (chart.data.expandedIndex !== null) {
                        const ctx = chart.ctx;
                        const rightX = chart.chartArea.right + 20;
                        const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;

                        ctx.save();
                        ctx.textAlign = 'left';
                        ctx.textBaseline = 'middle';

                        ctx.fillStyle = '#6b7280';
                        ctx.font = '12px Arial';
                        ctx.fillText('Transaction Type:', rightX, centerY - 40);

                        ctx.fillStyle = '#1f2937';
                        ctx.font = 'bold 16px Arial';
                        ctx.fillText(chart.data.expandedLabel, rightX, centerY - 15);

                        ctx.fillStyle = '#6b7280';
                        ctx.font = '12px Arial';
                        ctx.fillText('Amount:', rightX, centerY + 10);

                        ctx.fillStyle = '#1f2937';
                        ctx.font = 'bold 14px Arial';
                        const formattedAmount = chart.data.expandedValue.toLocaleString('en-US');
                        ctx.fillText('UGX ' + formattedAmount, rightX, centerY + 30);

                        ctx.fillStyle = '#6b7280';
                        ctx.font = '12px Arial';
                        ctx.fillText('Percentage:', rightX, centerY + 55);

                        ctx.fillStyle = '#1f2937';
                        ctx.font = 'bold 18px Arial';
                        ctx.fillText(chart.data.expandedPercentage + '%', rightX, centerY + 80);

                        ctx.restore();
                    }
                }
            }]
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
        memberRoleChart: {
            type: 'doughnut',
            getData: (data) => {
                console.log('Member Role Chart - roleDistribution:', data.roleDistribution);
                const client = data.roleDistribution?.client || 0;
                const shareholder = data.roleDistribution?.shareholder || 0;
                const cashier = data.roleDistribution?.cashier || 0;
                const td = data.roleDistribution?.td || 0;
                const ceo = data.roleDistribution?.ceo || 0;
                const originalData = [client, shareholder, cashier, td, ceo];
                console.log('Member Role Chart - originalData:', originalData);
                return {
                    labels: ['Client', 'Shareholder', 'Cashier', 'TD', 'CEO'],
                    datasets: [{
                        data: originalData,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.9)',
                            'rgba(16, 185, 129, 0.9)',
                            'rgba(249, 115, 22, 0.9)',
                            'rgba(139, 92, 246, 0.9)',
                            'rgba(239, 68, 68, 0.9)'
                        ],
                        borderColor: '#fff',
                        borderWidth: 4,
                        hoverOffset: 15,
                        hoverBorderWidth: 5
                    }],
                    originalData: originalData,
                    originalColors: [
                        'rgba(59, 130, 246, 0.9)',
                        'rgba(16, 185, 129, 0.9)',
                        'rgba(249, 115, 22, 0.9)',
                        'rgba(139, 92, 246, 0.9)',
                        'rgba(239, 68, 68, 0.9)'
                    ],
                    highlightColors: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(249, 115, 22, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    expandedIndex: null,
                    expandedLabel: '',
                    expandedValue: 0,
                    expandedPercentage: '0'
                };
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                onClick: (event, elements, chart) => {
                    const originalData = chart.data.originalData;
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const total = originalData.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                        const percentage = total > 0 ? ((originalData[index] / total) * 100).toFixed(1) : '0';
                        const label = chart.data.labels[index];
                        const value = originalData[index];
                        chart.data.datasets[0].data = originalData.map((val, i) => i === index ? val * 1.1 : val);
                        chart.data.datasets[0].backgroundColor = chart.data.originalColors.map((color, i) => i === index ? chart.data.highlightColors[i] : color.replace('0.9', '0.5'));
                        chart.data.expandedIndex = index;
                        chart.data.expandedLabel = label;
                        chart.data.expandedValue = value;
                        chart.data.expandedPercentage = percentage;
                        chart.update();
                    } else {
                        chart.data.datasets[0].data = [...originalData];
                        chart.data.datasets[0].backgroundColor = [...chart.data.originalColors];
                        chart.data.expandedIndex = null;
                        chart.update();
                    }
                },
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
                            label: (context) => {
                                const chart = chartInstances.memberRoleChart;
                                const originalData = chart.data.originalData;
                                const total = originalData.reduce((a, b) => a + b, 0);
                                const percentage = ((originalData[context.dataIndex] / total) * 100).toFixed(1);
                                return context.label + ': ' + originalData[context.dataIndex] + ' members (' + percentage + '%)';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            },
            plugins: [{
                id: 'memberRoleText',
                afterDatasetsDraw: (chart) => {
                    if (chart.data.expandedIndex !== null) {
                        const ctx = chart.ctx;
                        const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                        const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillStyle = '#6b7280';
                        ctx.font = '12px Arial';
                        ctx.fillText('Role', centerX, centerY - 30);
                        ctx.fillStyle = '#1f2937';
                        ctx.font = 'bold 16px Arial';
                        ctx.fillText(chart.data.expandedLabel, centerX, centerY - 5);
                        ctx.fillStyle = '#1f2937';
                        ctx.font = 'bold 24px Arial';
                        ctx.fillText(chart.data.expandedValue.toLocaleString('en-US'), centerX, centerY + 25);
                        ctx.fillStyle = '#6b7280';
                        ctx.font = '14px Arial';
                        ctx.fillText(chart.data.expandedPercentage + '%', centerX, centerY + 50);
                        ctx.restore();
                    }
                }
            }]
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
            const chartConfig = {
                type: config.type,
                data: chartData,
                options: config.options
            };

            // Add plugins if they exist
            if (config.plugins) {
                chartConfig.plugins = config.plugins;
            }

            chartInstances[chartId] = new Chart(ctx, chartConfig);
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

// Toggle chart type for Member Role Distribution
function toggleMemberRoleChartType(newType) {
    const chart = chartInstances.memberRoleChart;
    if (!chart) return;

    const currentData = chart.data.originalData;
    const expandedIndex = chart.data.expandedIndex;
    const expandedLabel = chart.data.expandedLabel;
    const expandedValue = chart.data.expandedValue;
    const expandedPercentage = chart.data.expandedPercentage;

    chart.destroy();

    const canvas = document.getElementById('memberRoleChart');
    const ctx = canvas.getContext('2d');

    const chartData = {
        labels: ['Client', 'Shareholder', 'Cashier', 'TD', 'CEO'],
        datasets: [{
            data: currentData,
            backgroundColor: [
                'rgba(59, 130, 246, 0.9)',
                'rgba(16, 185, 129, 0.9)',
                'rgba(249, 115, 22, 0.9)',
                'rgba(139, 92, 246, 0.9)',
                'rgba(239, 68, 68, 0.9)'
            ],
            borderColor: '#fff',
            borderWidth: newType === 'bar' ? 2 : 4
        }],
        originalData: currentData,
        originalColors: [
            'rgba(59, 130, 246, 0.9)',
            'rgba(16, 185, 129, 0.9)',
            'rgba(249, 115, 22, 0.9)',
            'rgba(139, 92, 246, 0.9)',
            'rgba(239, 68, 68, 0.9)'
        ],
        highlightColors: [
            'rgba(59, 130, 246, 1)',
            'rgba(16, 185, 129, 1)',
            'rgba(249, 115, 22, 1)',
            'rgba(139, 92, 246, 1)',
            'rgba(239, 68, 68, 1)'
        ],
        expandedIndex: expandedIndex,
        expandedLabel: expandedLabel,
        expandedValue: expandedValue,
        expandedPercentage: expandedPercentage
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        onClick: (event, elements, chart) => {
            const originalData = chart.data.originalData;
            if (elements.length > 0) {
                const index = elements[0].index;
                const total = originalData.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                const percentage = total > 0 ? ((originalData[index] / total) * 100).toFixed(1) : '0';
                const label = chart.data.labels[index];
                const value = originalData[index];
                chart.data.expandedIndex = index;
                chart.data.expandedLabel = label;
                chart.data.expandedValue = value;
                chart.data.expandedPercentage = percentage;
                chart.update();
            } else {
                chart.data.expandedIndex = null;
                chart.update();
            }
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 15, font: { size: 12, weight: '600' }, usePointStyle: true }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: (context) => {
                        const data = context.chart.data.originalData;
                        const total = data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                        const pct = total > 0 ? ((data[context.dataIndex] / total) * 100).toFixed(1) : '0';
                        return context.label + ': ' + data[context.dataIndex] + ' members (' + pct + '%)';
                    }
                }
            }
        },
        animation: { duration: 800, easing: 'easeInOutQuart' }
    };

    if (newType === 'doughnut' || newType === 'pie') {
        options.cutout = newType === 'doughnut' ? '65%' : '0%';
    } else if (newType === 'bar') {
        options.scales = {
            y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' }, ticks: { font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        };
    } else if (newType === 'polarArea') {
        options.scales = { r: { ticks: { display: false }, grid: { color: 'rgba(0, 0, 0, 0.05)' } } };
        options.layout = { padding: { right: 150 } };
    }

    const plugins = newType === 'polarArea' ? [{
        id: 'memberRoleText',
        afterDatasetsDraw: (chart) => {
            if (chart.data.expandedIndex !== null) {
                const ctx = chart.ctx;
                const rightX = chart.chartArea.right + 20;
                const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                ctx.save();
                ctx.textAlign = 'left';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#6b7280';
                ctx.font = '12px Arial';
                ctx.fillText('Role:', rightX, centerY - 40);
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold 16px Arial';
                ctx.fillText(chart.data.expandedLabel, rightX, centerY - 15);
                ctx.fillStyle = '#6b7280';
                ctx.font = '12px Arial';
                ctx.fillText('Count:', rightX, centerY + 10);
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold 14px Arial';
                ctx.fillText(chart.data.expandedValue + ' members', rightX, centerY + 30);
                ctx.fillStyle = '#6b7280';
                ctx.font = '12px Arial';
                ctx.fillText('Percentage:', rightX, centerY + 55);
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold 18px Arial';
                ctx.fillText(chart.data.expandedPercentage + '%', rightX, centerY + 80);
                ctx.restore();
            }
        }
    }] : [{
        id: 'memberRoleText',
        afterDatasetsDraw: (chart) => {
            if (chart.data.expandedIndex !== null) {
                const ctx = chart.ctx;
                const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#6b7280';
                ctx.font = '12px Arial';
                ctx.fillText('Role', centerX, centerY - 30);
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold 16px Arial';
                ctx.fillText(chart.data.expandedLabel, centerX, centerY - 5);
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold 24px Arial';
                ctx.fillText(chart.data.expandedValue, centerX, centerY + 25);
                ctx.fillStyle = '#6b7280';
                ctx.font = '14px Arial';
                ctx.fillText(chart.data.expandedPercentage + '%', centerX, centerY + 50);
                ctx.restore();
            }
        }
    }];

    chartInstances.memberRoleChart = new Chart(ctx, { type: newType, data: chartData, options, plugins });
}

// Toggle chart type for Loan Status Distribution
function toggleLoanStatusChartType(newType) {
    const chart = chartInstances.loanStatusChart;
    if (!chart) return;

    const currentData = chart.data.originalData;
    const expandedIndex = chart.data.expandedIndex;
    const expandedLabel = chart.data.expandedLabel;
    const expandedValue = chart.data.expandedValue;
    const expandedPercentage = chart.data.expandedPercentage;

    chart.destroy();

    const canvas = document.getElementById('loanStatusChart');
    const ctx = canvas.getContext('2d');

    const chartData = {
        labels: ['Approved', 'Pending', 'Rejected'],
        datasets: [{
            data: currentData,
            backgroundColor: [
                'rgba(16, 185, 129, 0.9)',
                'rgba(249, 115, 22, 0.9)',
                'rgba(239, 68, 68, 0.9)'
            ],
            borderColor: '#fff',
            borderWidth: newType === 'bar' ? 2 : 4
        }],
        originalData: currentData,
        originalColors: [
            'rgba(16, 185, 129, 0.9)',
            'rgba(249, 115, 22, 0.9)',
            'rgba(239, 68, 68, 0.9)'
        ],
        highlightColors: [
            'rgba(16, 185, 129, 1)',
            'rgba(249, 115, 22, 1)',
            'rgba(239, 68, 68, 1)'
        ],
        expandedIndex: expandedIndex,
        expandedLabel: expandedLabel,
        expandedValue: expandedValue,
        expandedPercentage: expandedPercentage
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        onClick: (event, elements, chart) => {
            const originalData = chart.data.originalData;
            if (elements.length > 0) {
                const index = elements[0].index;
                const total = originalData.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                const percentage = total > 0 ? ((originalData[index] / total) * 100).toFixed(1) : '0';
                const label = chart.data.labels[index];
                const value = originalData[index];
                chart.data.expandedIndex = index;
                chart.data.expandedLabel = label;
                chart.data.expandedValue = value;
                chart.data.expandedPercentage = percentage;
                chart.update();
            } else {
                chart.data.expandedIndex = null;
                chart.update();
            }
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 15, font: { size: 12, weight: '600' }, usePointStyle: true }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: (context) => {
                        const data = context.chart.data.originalData;
                        const total = data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                        const pct = total > 0 ? ((data[context.dataIndex] / total) * 100).toFixed(1) : '0';
                        return context.label + ': ' + data[context.dataIndex] + ' loans (' + pct + '%)';
                    }
                }
            }
        },
        animation: { duration: 800, easing: 'easeInOutQuart' }
    };

    if (newType === 'doughnut' || newType === 'pie') {
        options.cutout = newType === 'doughnut' ? '65%' : '0%';
    } else if (newType === 'bar') {
        options.scales = {
            y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' }, ticks: { font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        };
    } else if (newType === 'polarArea') {
        options.scales = { r: { ticks: { display: false }, grid: { color: 'rgba(0, 0, 0, 0.05)' } } };
        options.layout = { padding: { right: 150 } };
    }

    const plugins = newType === 'polarArea' ? [{
        id: 'loanStatusText',
        afterDatasetsDraw: (chart) => {
            if (chart.data.expandedIndex !== null) {
                const ctx = chart.ctx;
                const rightX = chart.chartArea.right + 20;
                const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                ctx.save();
                ctx.textAlign = 'left';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#6b7280';
                ctx.font = '12px Arial';
                ctx.fillText('Status:', rightX, centerY - 40);
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold 16px Arial';
                ctx.fillText(chart.data.expandedLabel, rightX, centerY - 15);
                ctx.fillStyle = '#6b7280';
                ctx.font = '12px Arial';
                ctx.fillText('Count:', rightX, centerY + 10);
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold 14px Arial';
                ctx.fillText(chart.data.expandedValue + ' loans', rightX, centerY + 30);
                ctx.fillStyle = '#6b7280';
                ctx.font = '12px Arial';
                ctx.fillText('Percentage:', rightX, centerY + 55);
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold 18px Arial';
                ctx.fillText(chart.data.expandedPercentage + '%', rightX, centerY + 80);
                ctx.restore();
            }
        }
    }] : [{
        id: 'loanStatusText',
        afterDatasetsDraw: (chart) => {
            if (chart.data.expandedIndex !== null) {
                const ctx = chart.ctx;
                const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#6b7280';
                ctx.font = '12px Arial';
                ctx.fillText('Status', centerX, centerY - 30);
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold 16px Arial';
                ctx.fillText(chart.data.expandedLabel, centerX, centerY - 5);
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold 24px Arial';
                ctx.fillText(chart.data.expandedValue, centerX, centerY + 25);
                ctx.fillStyle = '#6b7280';
                ctx.font = '14px Arial';
                ctx.fillText(chart.data.expandedPercentage + '%', centerX, centerY + 50);
                ctx.restore();
            }
        }
    }];

    chartInstances.loanStatusChart = new Chart(ctx, { type: newType, data: chartData, options, plugins });
}

// Toggle chart type for Transaction Types
function toggleTransactionTypesChartType(newType) {
    const chart = chartInstances.transactionTypesChart;
    if (!chart) return;

    const currentData = chart.data.originalData;
    const expandedIndex = chart.data.expandedIndex;
    const expandedLabel = chart.data.expandedLabel;
    const expandedValue = chart.data.expandedValue;
    const expandedPercentage = chart.data.expandedPercentage;

    chart.destroy();

    const canvas = document.getElementById('transactionTypesChart');
    const ctx = canvas.getContext('2d');

    const chartData = {
        labels: ['Deposit', 'Withdrawal', 'Transfer', 'Loan Payment', 'Loan Request', 'Fundraising', 'Condolence'],
        datasets: [{
            data: currentData,
            backgroundColor: [
                'rgba(16, 185, 129, 0.7)',
                'rgba(239, 68, 68, 0.7)',
                'rgba(59, 130, 246, 0.7)',
                'rgba(139, 92, 246, 0.7)',
                'rgba(99, 102, 241, 0.7)',
                'rgba(236, 72, 153, 0.7)',
                'rgba(107, 114, 128, 0.7)'
            ],
            borderColor: '#fff',
            borderWidth: newType === 'bar' ? 2 : 3
        }],
        originalData: currentData,
        originalColors: [
            'rgba(16, 185, 129, 0.7)',
            'rgba(239, 68, 68, 0.7)',
            'rgba(59, 130, 246, 0.7)',
            'rgba(139, 92, 246, 0.7)',
            'rgba(99, 102, 241, 0.7)',
            'rgba(236, 72, 153, 0.7)',
            'rgba(107, 114, 128, 0.7)'
        ],
        highlightColors: [
            'rgba(16, 185, 129, 1)',
            'rgba(239, 68, 68, 1)',
            'rgba(59, 130, 246, 1)',
            'rgba(139, 92, 246, 1)',
            'rgba(99, 102, 241, 1)',
            'rgba(236, 72, 153, 1)',
            'rgba(107, 114, 128, 1)'
        ],
        expandedIndex: expandedIndex,
        expandedLabel: expandedLabel,
        expandedValue: expandedValue,
        expandedPercentage: expandedPercentage
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        onClick: (event, elements, chart) => {
            const originalData = chart.data.originalData;
            if (elements.length > 0) {
                const index = elements[0].index;
                const total = originalData.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                const percentage = total > 0 ? ((originalData[index] / total) * 100).toFixed(1) : '0';
                const label = chart.data.labels[index];
                const value = originalData[index];
                chart.data.expandedIndex = index;
                chart.data.expandedLabel = label;
                chart.data.expandedValue = value;
                chart.data.expandedPercentage = percentage;
                chart.update();
            } else {
                chart.data.expandedIndex = null;
                chart.update();
            }
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 12, font: { size: 11, weight: '600' }, usePointStyle: true }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: (context) => {
                        const data = context.chart.data.originalData;
                        const total = data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                        const pct = total > 0 ? ((data[context.dataIndex] / total) * 100).toFixed(1) : '0';
                        const amt = data[context.dataIndex];
                        return context.label + ': UGX ' + amt.toLocaleString() + ' (' + pct + '%)';
                    }
                }
            }
        },
        animation: { duration: 600, easing: 'easeInOutQuart' }
    };

    if (newType === 'doughnut' || newType === 'pie') {
        options.cutout = newType === 'doughnut' ? '65%' : '0%';
    } else if (newType === 'bar') {
        options.scales = {
            y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' }, ticks: { font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        };
    } else if (newType === 'polarArea') {
        options.scales = { r: { ticks: { display: false }, grid: { color: 'rgba(0, 0, 0, 0.05)' } } };
        options.layout = { padding: { right: 150 } };
    }

    const plugins = [{
        id: 'expandedSegmentText',
        afterDatasetsDraw: (chart) => {
            if (chart.data.expandedIndex !== null) {
                const ctx = chart.ctx;
                if (newType === 'polarArea') {
                    const rightX = chart.chartArea.right + 20;
                    const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                    ctx.save();
                    ctx.textAlign = 'left';
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = '#6b7280';
                    ctx.font = '12px Arial';
                    ctx.fillText('Transaction Type:', rightX, centerY - 40);
                    ctx.fillStyle = '#1f2937';
                    ctx.font = 'bold 16px Arial';
                    ctx.fillText(chart.data.expandedLabel, rightX, centerY - 15);
                    ctx.fillStyle = '#6b7280';
                    ctx.font = '12px Arial';
                    ctx.fillText('Amount:', rightX, centerY + 10);
                    ctx.fillStyle = '#1f2937';
                    ctx.font = 'bold 14px Arial';
                    const formattedAmount = chart.data.expandedValue.toLocaleString('en-US');
                    ctx.fillText('UGX ' + formattedAmount, rightX, centerY + 30);
                    ctx.fillStyle = '#6b7280';
                    ctx.font = '12px Arial';
                    ctx.fillText('Percentage:', rightX, centerY + 55);
                    ctx.fillStyle = '#1f2937';
                    ctx.font = 'bold 18px Arial';
                    ctx.fillText(chart.data.expandedPercentage + '%', rightX, centerY + 80);
                    ctx.restore();
                } else if (newType === 'doughnut' || newType === 'pie') {
                    const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                    const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = '#6b7280';
                    ctx.font = '11px Arial';
                    ctx.fillText('Type', centerX, centerY - 30);
                    ctx.fillStyle = '#1f2937';
                    ctx.font = 'bold 14px Arial';
                    ctx.fillText(chart.data.expandedLabel, centerX, centerY - 10);
                    ctx.fillStyle = '#1f2937';
                    ctx.font = 'bold 16px Arial';
                    const amt = (chart.data.expandedValue / 1000000).toFixed(1);
                    ctx.fillText('UGX ' + amt + 'M', centerX, centerY + 15);
                    ctx.fillStyle = '#6b7280';
                    ctx.font = '13px Arial';
                    ctx.fillText(chart.data.expandedPercentage + '%', centerX, centerY + 40);
                    ctx.restore();
                }
            }
        }
    }];

    chartInstances.transactionTypesChart = new Chart(ctx, { type: newType, data: chartData, options, plugins });
}

// Toggle chart type for Members Growth
function toggleMembersGrowthChartType(newType) {
    const chart = chartInstances.membersChart;
    if (!chart) return;

    const currentData = chart.data.datasets[0].data;
    const currentLabels = chart.data.labels;

    chart.destroy();

    const canvas = document.getElementById('membersChart');
    const ctx = canvas.getContext('2d');

    const chartData = {
        labels: currentLabels,
        datasets: [{
            label: 'Member Growth',
            data: currentData,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: ctx ? createGradient(ctx, 'rgba(59, 130, 246, 0.6)', 'rgba(59, 130, 246, 0.08)') : 'rgba(59, 130, 246, 0.1)',
            borderWidth: newType === 'bar' ? 2 : 4,
            tension: newType === 'line' ? 0.4 : 0,
            fill: newType === 'line',
            pointRadius: newType === 'line' ? 6 : 0,
            pointHoverRadius: newType === 'line' ? 10 : 0,
            pointBackgroundColor: 'rgb(59, 130, 246)',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 3,
            pointHoverBackgroundColor: '#ffffff',
            pointHoverBorderColor: 'rgb(59, 130, 246)',
            pointHoverBorderWidth: 4,
            pointStyle: 'circle',
            borderRadius: newType === 'bar' ? 8 : 0,
            borderSkipped: newType === 'bar' ? false : true
        }]
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index',
            axis: newType === 'bar' ? 'x' : 'x'
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                align: 'end',
                labels: {
                    usePointStyle: true,
                    pointStyle: newType === 'line' ? 'circle' : 'rect',
                    font: { size: 12, weight: '600' },
                    color: '#1f2937',
                    padding: 15
                }
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                padding: 14,
                cornerRadius: 10,
                borderWidth: 1,
                borderColor: 'rgba(59, 130, 246, 0.3)',
                displayColors: false,
                callbacks: {
                    title: (context) => {
                        return context[0].label + ' 2024';
                    },
                    label: (context) => {
                        const value = context.parsed.y || context.parsed.x;
                        return [
                            `Members: ${value.toLocaleString()}`,
                            `Growth: +${value - (currentData[context.dataIndex - 1] || 0)}`
                        ];
                    },
                    afterLabel: (context) => {
                        if (context.dataIndex === currentData.length - 1) {
                            return [
                                '',
                                newType === 'line' ? '📈 Trend: Upward trajectory' : '📊 Current status',
                                '🎯 Target: 500 members by Q4'
                            ];
                        }
                        return '';
                    }
                }
            },
            annotation: {
                annotations: {
                    targetLine: {
                        type: 'line',
                        scaleID: newType === 'line' ? 'y' : 'y',
                        value: 500,
                        borderColor: 'rgba(34, 197, 94, 0.6)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        label: {
                            content: 'Target: 500',
                            enabled: true,
                            position: 'center',
                            backgroundColor: 'rgba(34, 197, 94, 0.8)',
                            color: '#ffffff',
                            font: { size: 10, weight: 'bold' },
                            padding: 6,
                            borderRadius: 4
                        }
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false,
                    drawOnChartArea: true,
                    drawTicks: false
                },
                ticks: {
                    font: { size: 11, weight: '600' },
                    color: '#6b7280',
                    callback: function(value) {
                        if (value >= 1000) {
                            return (value / 1000).toFixed(1) + 'K';
                        }
                        return value;
                    }
                },
                suggestedMax: currentData.reduce((a, b) => Math.max(a, b), 0) * 1.2
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false,
                    drawTicks: false
                },
                ticks: {
                    font: { size: 11, weight: '600' },
                    color: '#6b7280',
                    callback: function(value, index) {
                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        return months[index] || this.getLabelForValue(value);
                    }
                }
            }
        },
        animation: {
            duration: 1500,
            easing: 'easeInOutQuart',
            animations: {
                y: {
                    easing: 'easeOutQuart',
                    from: (ctx) => {
                        if (ctx.type === 'data' && ctx.mode === 'reset') {
                            return ctx.context.parsed.y;
                        }
                        return ctx.chart.scales.y.getPixelForValue(0);
                    }
                },
                x: {
                    easing: 'easeOutQuart'
                }
            }
        },
        elements: {
            point: {
                hoverBorderWidth: 4,
                hoverRadius: 10,
                radius: 6
            },
            line: {
                tension: 0.4,
                borderWidth: 4
            }
        },
        hover: {
            mode: 'index',
            intersect: false
        }
    };

    const plugins = [{
        id: 'membersGrowthHeader',
        beforeDraw: (chart) => {
            const ctx = chart.ctx;
            const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
            const topY = chart.chartArea.top - 20;

            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            // Title
            ctx.fillStyle = '#1f2937';
            ctx.font = 'bold 18px Arial';
            ctx.fillText('Member Growth Analytics', centerX, topY);

            // Subtitle
            ctx.fillStyle = '#6b7280';
            ctx.font = '14px Arial';
            ctx.fillText(`Monthly member acquisition and retention trends (${newType === 'line' ? 'Line' : 'Bar'} Chart)`, centerX, topY + 20);

            ctx.restore();
        }
    }, {
        id: 'growthRateIndicator',
        afterDraw: (chart) => {
            const data = chart.data.datasets[0].data;
            if (data.length < 2) return;

            const ctx = chart.ctx;
            const lastValue = data[data.length - 1];
            const prevValue = data[data.length - 2];
            const growth = lastValue - prevValue;
            const growthRate = prevValue > 0 ? (growth / prevValue) * 100 : 0;

            const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
            const bottomY = chart.chartArea.bottom + 30;

            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            // Growth indicator
            ctx.fillStyle = growth >= 0 ? '#10b981' : '#ef4444';
            ctx.font = 'bold 16px Arial';
            ctx.fillText(growth >= 0 ? '📈' : '📉', centerX, bottomY);

            // Growth rate
            ctx.fillStyle = '#1f2937';
            ctx.font = 'bold 14px Arial';
            ctx.fillText(`${growth >= 0 ? '+' : ''}${growthRate.toFixed(1)}% Growth Rate`, centerX, bottomY + 20);

            // Total members
            ctx.fillStyle = '#6b7280';
            ctx.font = '12px Arial';
            ctx.fillText(`Total Members: ${lastValue.toLocaleString()}`, centerX, bottomY + 40);

            // Chart type indicator
            ctx.fillStyle = '#6b7280';
            ctx.font = '12px Arial';
            ctx.fillText(`Chart Type: ${newType === 'line' ? 'Line' : 'Bar'}`, centerX, bottomY + 60);

            ctx.restore();
        }
    }];

    chartInstances.membersChart = new Chart(ctx, { type: newType, data: chartData, options, plugins });
}

// Year dropdown functionality for Members Growth chart
function createYearDropdown() {
    // Check if dropdown already exists
    if (document.getElementById('yearDropdown')) {
        return;
    }

    // Create dropdown container
    const container = document.createElement('div');
    container.style.position = 'absolute';
    container.style.top = '20px';
    container.style.right = '20px';
    container.style.zIndex = '1000';
    container.style.background = 'white';
    container.style.border = '1px solid #e5e7eb';
    container.style.borderRadius = '8px';
    container.style.padding = '8px 12px';
    container.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
    container.style.display = 'flex';
    container.style.alignItems = 'center';
    container.style.gap = '8px';

    // Create label
    const label = document.createElement('span');
    label.textContent = 'Year:';
    label.style.fontWeight = '600';
    label.style.color = '#374151';
    label.style.fontSize = '14px';

    // Create select element
    const select = document.createElement('select');
    select.id = 'yearDropdown';
    select.style.padding = '6px 12px';
    select.style.border = '1px solid #d1d5db';
    select.style.borderRadius = '6px';
    select.style.fontSize = '14px';
    select.style.color = '#374151';
    select.style.background = 'white';
    select.style.cursor = 'pointer';
    select.style.minWidth = '100px';

    // Add years (current year and 5 previous years)
    const currentYear = new Date().getFullYear();
    for (let i = 5; i >= 0; i--) {
        const year = currentYear - i;
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        select.appendChild(option);
    }

    // Add event listener
    select.addEventListener('change', (e) => {
        const selectedYear = parseInt(e.target.value);
        // Trigger year change event
        if (window.adminChartsOptimizer.onYearChange) {
            window.adminChartsOptimizer.onYearChange(selectedYear);
        }
    });

    container.appendChild(label);
    container.appendChild(select);

    // Find the chart container and add dropdown
    const chartContainer = document.querySelector('.chart-container') || document.querySelector('#membersChart').parentElement;
    if (chartContainer) {
        chartContainer.style.position = 'relative';
        chartContainer.appendChild(container);
    }
}

// Enhanced Members Growth chart with year support
function initMembersGrowthChartWithYear(data) {
    // Create year dropdown
    createYearDropdown();

    // Initialize chart with year data
    initChartsOptimized(data);

    // Set initial year in dropdown
    const dropdown = document.getElementById('yearDropdown');
    if (dropdown && data.selectedYear) {
        dropdown.value = data.selectedYear;
    }
}

// Year change handler
function handleYearChange(year) {
    // This function should be implemented by the backend/frontend integration
    // For now, it's a placeholder that can be extended
    console.log('Year changed to:', year);

    // Example: Fetch new data for the selected year
    // This would typically make an API call to get data for the specific year
    if (window.fetchMembersGrowthData) {
        window.fetchMembersGrowthData(year).then(newData => {
            // Update chart with new data
            initMembersGrowthChartWithYear(newData);
        });
    }
}

// Enhanced data preparation for Members Growth chart
function prepareMembersGrowthData(data, year) {
    // Ensure we have all 12 months with proper data
    const allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const monthlyData = data.membersGrowth || [];

    // Create a map of month data for quick lookup
    const monthMap = new Map();
    monthlyData.forEach(item => {
        monthMap.set(item.month, item.count);
    });

    // Fill in all 12 months, using 0 for missing months
    const labels = allMonths;
    const dataValues = allMonths.map(month => monthMap.get(month) || 0);

    // Calculate maximum value for proper scaling
    const maxValue = Math.max(...dataValues, data.totalMembers || 0);

    return {
        labels: labels,
        datasets: [{
            label: `Member Growth ${year || new Date().getFullYear()}`,
            data: dataValues,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 4,
            tension: 0.4,
            fill: true,
            pointRadius: 6,
            pointHoverRadius: 10,
            pointBackgroundColor: 'rgb(59, 130, 246)',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 3,
            pointHoverBackgroundColor: '#ffffff',
            pointHoverBorderColor: 'rgb(59, 130, 246)',
            pointHoverBorderWidth: 4,
            pointStyle: 'circle'
        }]
    };
}

// Update Members Chart by Year
function updateMembersChartYear(year) {
    fetch(`/api/dashboard-data?year=${year}`)
        .then(response => response.json())
        .then(data => {
            const chart = chartInstances.membersChart;
            if (!chart) return;

            const allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const monthlyData = data.membersGrowth || [];
            const monthMap = new Map();
            monthlyData.forEach(item => monthMap.set(item.month, item.count));
            const dataValues = allMonths.map(month => monthMap.get(month) || 0);

            chart.data.labels = allMonths;
            chart.data.datasets[0].data = dataValues;
            chart.data.datasets[0].label = `Member Growth ${year}`;
            chart.options.scales.y.suggestedMax = Math.max(...dataValues) * 1.2;
            chart.update();
        })
        .catch(error => console.error('Error fetching year data:', error));
}

// Show All Years Data
function showAllYearsData() {
    fetch('/api/dashboard-data?allYears=true')
        .then(response => response.json())
        .then(data => {
            const chart = chartInstances.membersChart;
            if (!chart) return;

            const yearlyData = data.allYearsGrowth || [];
            const labels = yearlyData.map(item => item.year);
            const dataValues = yearlyData.map(item => item.total);

            chart.data.labels = labels;
            chart.data.datasets[0].data = dataValues;
            chart.data.datasets[0].label = 'Total Members by Year';
            chart.options.scales.y.suggestedMax = Math.max(...dataValues) * 1.2;
            chart.update();
        })
        .catch(error => console.error('Error fetching all years data:', error));
}

// Populate Year Dropdown
function populateYearDropdown() {
    const select = document.getElementById('membersChartYear');
    if (!select) return;

    const currentYear = new Date().getFullYear();
    select.innerHTML = '';

    for (let year = currentYear; year >= 2020; year--) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        if (year === currentYear) option.selected = true;
        select.appendChild(option);
    }
}

// Update Savings Chart by Year
function updateSavingsChartYear(year) {
    fetch(`/api/dashboard-data?year=${year}`)
        .then(response => response.json())
        .then(data => {
            const chart = chartInstances.revenueChart;
            if (!chart) return;

            const allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const monthlyData = data.savingsGrowth || [];
            const monthMap = new Map();
            monthlyData.forEach(item => monthMap.set(item.month, parseFloat(item.total) || 0));
            const dataValues = allMonths.map(month => monthMap.get(month) || 0);

            chart.data.labels = allMonths;
            chart.data.datasets[0].data = dataValues;
            chart.data.datasets[0].label = `Savings ${year}`;
            chart.options.scales.y.suggestedMax = Math.max(...dataValues) * 1.2;
            chart.update();
        })
        .catch(error => console.error('Error fetching savings year data:', error));
}

// Show All Years Savings Data
function showAllYearsSavings() {
    fetch('/api/dashboard-data?allYears=true')
        .then(response => response.json())
        .then(data => {
            const chart = chartInstances.revenueChart;
            if (!chart) return;

            const yearlyData = data.allYearsGrowth || [];
            const labels = yearlyData.map(item => item.year);
            const dataValues = yearlyData.map(item => parseFloat(item.total_savings) || 0);

            chart.data.labels = labels;
            chart.data.datasets[0].data = dataValues;
            chart.data.datasets[0].label = 'Total Savings by Year';
            chart.options.scales.y.suggestedMax = Math.max(...dataValues) * 1.2;
            chart.update();
        })
        .catch(error => console.error('Error fetching all years savings data:', error));
}

// Export functions
window.adminChartsOptimizer = {
    init: initChartsOptimized,
    update: updateCharts,
    destroy: destroyCharts,
    toggleMemberRoleChart: toggleMemberRoleChartType,
    toggleLoanStatusChart: toggleLoanStatusChartType,
    toggleTransactionTypesChart: toggleTransactionTypesChartType,
    toggleMembersGrowthChart: toggleMembersGrowthChartType,
    initMembersGrowthWithYear: initMembersGrowthChartWithYear,
    onYearChange: handleYearChange,
    prepareMembersGrowthData: prepareMembersGrowthData,
    updateMembersChartYear: updateMembersChartYear,
    showAllYearsData: showAllYearsData,
    populateYearDropdown: populateYearDropdown,
    updateSavingsChartYear: updateSavingsChartYear,
    showAllYearsSavings: showAllYearsSavings
};
