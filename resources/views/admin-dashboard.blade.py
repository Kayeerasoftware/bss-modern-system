<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BSS Investment Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50" x-data="adminDashboard()">
    <!-- Header -->
    <nav class="bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-cog text-2xl"></i>
                    <div>
                        <h1 class="text-xl font-bold">System Administrator Dashboard</h1>
                        <p class="text-red-200 text-sm">Complete System Management & Control</p>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-right">
                        <p class="text-sm text-red-200">System Health</p>
                        <p class="font-semibold text-lg text-green-400">98.7%</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-red-200">Active Users</p>
                        <p class="font-semibold text-lg">247</p>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- System Overview -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-green-600" x-text="systemStats.totalUsers">1,247</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>+12 today</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Database Size</p>
                        <p class="text-2xl font-bold text-blue-600">2.4 GB</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-database text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-blue-600">
                        <span>85% capacity</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">API Calls</p>
                        <p class="text-2xl font-bold text-purple-600">45.2K</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-exchange-alt text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-purple-600">
                        <span>Today</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Server Load</p>
                        <p class="text-2xl font-bold text-orange-600">23%</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i class="fas fa-server text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <span>Optimal</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Security Alerts</p>
                        <p class="text-2xl font-bold text-red-600" x-text="securityAlerts.length">3</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-shield-alt text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-red-600">
                        <span>Requires attention</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- User Activity -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">User Activity</h3>
                    <select class="text-sm border rounded px-3 py-1">
                        <option>Last 24 hours</option>
                        <option>Last 7 days</option>
                        <option>Last 30 days</option>
                    </select>
                </div>
                <div style="height: 250px;">
                    <canvas id="userActivityChart"></canvas>
                </div>
            </div>

            <!-- System Performance -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">System Performance</h3>
                    <span class="text-sm text-gray-500">Real-time metrics</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- User Management & Security -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- User Management -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">User Management</h3>
                    <button @click="showAddUser = true" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        <i class="fas fa-plus mr-2"></i>Add User
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 text-sm font-medium text-gray-600">User</th>
                                <th class="text-left py-2 text-sm font-medium text-gray-600">Role</th>
                                <th class="text-left py-2 text-sm font-medium text-gray-600">Status</th>
                                <th class="text-left py-2 text-sm font-medium text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="user in recentUsers.slice(0, 5)" :key="user.id">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-gray-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-sm" x-text="user.name">John Doe</div>
                                                <div class="text-xs text-gray-500" x-text="user.email">john@bss.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs rounded-full" 
                                              :class="user.role === 'admin' ? 'bg-red-100 text-red-800' : 
                                                     user.role === 'manager' ? 'bg-blue-100 text-blue-800' : 
                                                     'bg-green-100 text-green-800'"
                                              x-text="user.role">Client</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs rounded-full" 
                                              :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                              x-text="user.status">Active</span>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex space-x-2">
                                            <button class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Security Alerts -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Security Alerts</h3>
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full" x-text="securityAlerts.length + ' active'">3 active</span>
                </div>
                <div class="space-y-3">
                    <template x-for="alert in securityAlerts" :key="alert.id">
                        <div class="p-3 rounded-lg border-l-4" :class="alert.severity === 'high' ? 'bg-red-50 border-red-500' : 
                                                                        alert.severity === 'medium' ? 'bg-yellow-50 border-yellow-500' : 
                                                                        'bg-blue-50 border-blue-500'">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-medium text-sm" x-text="alert.title">Failed Login Attempts</h4>
                                    <p class="text-xs text-gray-600 mt-1" x-text="alert.description">Multiple failed login attempts detected</p>
                                </div>
                                <div class="text-xs text-gray-500" x-text="alert.time">5m ago</div>
                            </div>
                            <div class="mt-2 flex space-x-2">
                                <button class="text-xs px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                    Block IP
                                </button>
                                <button class="text-xs px-2 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                    Investigate
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- System Logs & Database Management -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Quick Actions -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button @click="performBackup()" class="w-full bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>
                        Database Backup
                    </button>
                    <button class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 flex items-center justify-center">
                        <i class="fas fa-broom mr-2"></i>
                        Clear Cache
                    </button>
                    <button class="w-full bg-purple-600 text-white p-3 rounded-lg hover:bg-purple-700 flex items-center justify-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Generate Report
                    </button>
                    <button class="w-full bg-orange-600 text-white p-3 rounded-lg hover:bg-orange-700 flex items-center justify-center">
                        <i class="fas fa-sync mr-2"></i>
                        System Update
                    </button>
                    <button class="w-full bg-red-600 text-white p-3 rounded-lg hover:bg-red-700 flex items-center justify-center">
                        <i class="fas fa-power-off mr-2"></i>
                        Maintenance Mode
                    </button>
                </div>
            </div>

            <!-- System Logs -->
            <div class="bg-white p-6 rounded-xl shadow-lg lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent System Logs</h3>
                    <button class="text-red-600 text-sm hover:underline">View All Logs</button>
                </div>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <template x-for="log in systemLogs" :key="log.id">
                        <div class="p-2 text-sm border-l-4" :class="log.level === 'error' ? 'border-red-500 bg-red-50' : 
                                                                   log.level === 'warning' ? 'border-yellow-500 bg-yellow-50' : 
                                                                   'border-green-500 bg-green-50'">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-mono text-xs text-gray-500" x-text="log.timestamp">2024-01-20 14:30:25</span>
                                    <span class="ml-2 font-medium" x-text="log.message">User login successful</span>
                                </div>
                                <span class="px-2 py-1 text-xs rounded" 
                                      :class="log.level === 'error' ? 'bg-red-200 text-red-800' : 
                                             log.level === 'warning' ? 'bg-yellow-200 text-yellow-800' : 
                                             'bg-green-200 text-green-800'"
                                      x-text="log.level">INFO</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Database Statistics -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Database Statistics</h3>
                <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    <i class="fas fa-sync mr-2"></i>Refresh
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <template x-for="table in databaseStats" :key="table.name">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium" x-text="table.name">Users</h4>
                            <i class="fas fa-table text-gray-400"></i>
                        </div>
                        <div class="text-2xl font-bold text-red-600 mb-1" x-text="table.records.toLocaleString()">1,247</div>
                        <div class="text-sm text-gray-600" x-text="table.size">2.4 MB</div>
                        <div class="mt-2">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Growth</span>
                                <span x-text="table.growth" :class="table.growth.startsWith('+') ? 'text-green-600' : 'text-red-600'">+5.2%</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div x-show="showAddUser" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Add New User</h3>
            <form @submit.prevent="addUser()">
                <div class="space-y-4">
                    <input type="text" x-model="newUser.name" placeholder="Full Name" class="w-full p-3 border rounded" required>
                    <input type="email" x-model="newUser.email" placeholder="Email Address" class="w-full p-3 border rounded" required>
                    <select x-model="newUser.role" class="w-full p-3 border rounded" required>
                        <option value="">Select Role</option>
                        <option value="admin">Administrator</option>
                        <option value="manager">Manager</option>
                        <option value="cashier">Cashier</option>
                        <option value="member">Member</option>
                    </select>
                    <input type="password" x-model="newUser.password" placeholder="Password" class="w-full p-3 border rounded" required>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showAddUser = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Add User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeAdminCharts, 500);
        });
        
        function initializeAdminCharts() {
            const activityCtx = document.getElementById('userActivityChart')?.getContext('2d');
            if (activityCtx) {
                new Chart(activityCtx, {
                    type: 'line',
                    data: {
                        labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
                        datasets: [{
                            label: 'Active Users',
                            data: [45, 25, 120, 180, 220, 150],
                            borderColor: '#EF4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            const performanceCtx = document.getElementById('performanceChart')?.getContext('2d');
            if (performanceCtx) {
                new Chart(performanceCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['CPU Usage', 'Memory Usage', 'Disk Usage', 'Network'],
                        datasets: [{
                            data: [23, 45, 67, 12],
                            backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#8B5CF6'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }
        }
        function adminDashboard() {
            return {
                showAddUser: false,
                newUser: {},
                systemStats: {
                    totalUsers: 1247,
                    databaseSize: '2.4 GB',
                    apiCalls: '45.2K',
                    serverLoad: '23%'
                },
                recentUsers: [
                    {id: 1, name: 'John Doe', email: 'john@bss.com', role: 'client', status: 'active'},
                    {id: 2, name: 'Jane Smith', email: 'jane@bss.com', role: 'manager', status: 'active'},
                    {id: 3, name: 'Robert Johnson', email: 'robert@bss.com', role: 'cashier', status: 'inactive'},
                    {id: 4, name: 'Mary Wilson', email: 'mary@bss.com', role: 'admin', status: 'active'}
                ],
                securityAlerts: [
                    {id: 1, title: 'Failed Login Attempts', description: 'Multiple failed login attempts detected', severity: 'high', time: '5m ago'},
                    {id: 2, title: 'Unusual API Activity', description: 'High API usage from single IP', severity: 'medium', time: '15m ago'},
                    {id: 3, title: 'Database Connection Spike', description: 'Unusual database connection pattern', severity: 'low', time: '1h ago'}
                ],
                systemLogs: [
                    {id: 1, timestamp: '2024-01-20 14:30:25', message: 'User login successful', level: 'info'},
                    {id: 2, timestamp: '2024-01-20 14:29:15', message: 'Database backup completed', level: 'info'},
                    {id: 3, timestamp: '2024-01-20 14:28:45', message: 'Failed login attempt detected', level: 'warning'},
                    {id: 4, timestamp: '2024-01-20 14:27:30', message: 'System cache cleared', level: 'info'},
                    {id: 5, timestamp: '2024-01-20 14:26:12', message: 'API rate limit exceeded', level: 'error'}
                ],
                databaseStats: [
                    {name: 'Users', records: 1247, size: '2.4 MB', growth: '+5.2%'},
                    {name: 'Members', records: 1180, size: '3.1 MB', growth: '+8.1%'},
                    {name: 'Transactions', records: 15420, size: '12.8 MB', growth: '+12.3%'},
                    {name: 'Projects', records: 45, size: '0.8 MB', growth: '+2.1%'}
                ],
                
                init() {
                    this.loadAdminData();
                },
                
                async loadAdminData() {
                    try {
                        const response = await fetch('/api/admin-data');
                        const data = await response.json();
                        
                        this.systemStats = data.system_stats;
                        this.recentUsers = data.recent_users.map(user => ({
                            id: user.id,
                            name: user.name,
                            email: user.email,
                            role: user.role,
                            status: user.is_active ? 'active' : 'inactive'
                        }));
                        this.systemLogs = data.system_logs;
                        this.databaseStats = data.database_stats;
                        this.securityAlerts = data.security_alerts;
                        
                        this.initCharts(data);
                    } catch (error) {
                        console.error('Error loading admin data:', error);
                        this.initCharts();
                    }
                },
                
                addUser() {
                    fetch('/api/members', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            full_name: this.newUser.name,
                            email: this.newUser.email,
                            role: this.newUser.role,
                            location: 'Default Location',
                            occupation: 'Default Occupation',
                            contact: '+256700000000'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.recentUsers.unshift({
                                id: data.member.id,
                                name: data.member.full_name,
                                email: data.member.email,
                                role: data.member.role,
                                status: 'active'
                            });
                            this.showAddUser = false;
                            this.newUser = {};
                            this.loadAdminData();
                            alert('User added successfully!');
                        } else {
                            alert('Error adding user');
                        }
                    })
                    .catch(error => {
                        console.error('Error adding user:', error);
                        alert('Error adding user');
                    });
                },
                
                performBackup() {
                    if (confirm('Start database backup? This may take a few minutes.')) {
                        alert('Database backup started. You will be notified when complete.');
                    }
                },
                
                initCharts(data = null) {
                    // User Activity Chart
                    const activityCtx = document.getElementById('userActivityChart').getContext('2d');
                    
                    let activityData = [45, 25, 120, 180, 220, 150];
                    if (data && data.user_activity) {
                        activityData = data.user_activity.slice(0, 6); // Take first 6 hours
                    }
                    
                    new Chart(activityCtx, {
                        type: 'line',
                        data: {
                            labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
                            datasets: [{
                                label: 'Active Users',
                                data: activityData,
                                borderColor: '#EF4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    // Performance Chart
                    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
                    
                    let performanceData = [23, 45, 67, 12];
                    if (data && data.performance_metrics) {
                        performanceData = [
                            data.performance_metrics.cpu_usage,
                            data.performance_metrics.memory_usage,
                            data.performance_metrics.disk_usage,
                            data.performance_metrics.network_usage
                        ];
                    }
                    
                    new Chart(performanceCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['CPU Usage', 'Memory Usage', 'Disk Usage', 'Network'],
                            datasets: [{
                                data: performanceData,
                                backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#8B5CF6']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>