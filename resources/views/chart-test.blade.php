<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart Test - BSS System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container { width: 400px; height: 300px; margin: 20px; display: inline-block; }
        canvas { border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>BSS Dashboard Charts Test</h1>
    
    <div class="chart-container">
        <h3>Line Chart Test</h3>
        <canvas id="lineChart"></canvas>
    </div>
    
    <div class="chart-container">
        <h3>Bar Chart Test</h3>
        <canvas id="barChart"></canvas>
    </div>
    
    <div class="chart-container">
        <h3>Doughnut Chart Test</h3>
        <canvas id="doughnutChart"></canvas>
    </div>

    <script>
        // Test Line Chart
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Savings',
                    data: [300000, 350000, 400000, 420000, 480000, 500000],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Test Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['9AM', '10AM', '11AM', '12PM', '1PM', '2PM'],
                datasets: [{
                    label: 'Transactions',
                    data: [5, 8, 12, 15, 10, 18],
                    backgroundColor: '#3B82F6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Test Doughnut Chart
        const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
        new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Deposits', 'Withdrawals', 'Transfers'],
                datasets: [{
                    data: [70, 20, 10],
                    backgroundColor: ['#10B981', '#EF4444', '#8B5CF6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        console.log('All charts initialized successfully!');
    </script>
</body>
</html>