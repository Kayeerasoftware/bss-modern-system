<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSS Investment Group - Dashboard Selection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-university text-3xl text-blue-600"></i>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">BSS Investment Group</h1>
                        <p class="text-gray-600">Modern Investment Management System</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        <i class="fas fa-check-circle mr-1"></i>System Online
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Welcome Section -->
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Welcome to BSS Investment Group 1</h2>
            <p class="text-xl text-gray-600 mb-8">Choose your dashboard to access role-specific features and analytics</p>
            <div class="flex justify-center space-x-4">
                <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm">
                    <i class="fas fa-users mr-2"></i>{{ $totalMembers ?? 1247 }} Active Members
                </span>
                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm">
                    <i class="fas fa-chart-line mr-2"></i>UGX {{ number_format(($totalAssets ?? 125800000) / 1000000, 1) }}M Total Assets
                </span>
                <span class="px-4 py-2 bg-purple-100 text-purple-800 rounded-full text-sm">
                    <i class="fas fa-project-diagram mr-2"></i>{{ $activeProjects ?? 12 }} Active Projects
                </span>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Client Dashboard -->
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <i class="fas fa-user-circle text-3xl mb-2"></i>
                            <h3 class="text-xl font-bold">Client Dashboard</h3>
                            <p class="text-green-100">Personal Financial Management</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">UGX 500K</div>
                            <div class="text-sm text-green-100">Avg. Savings</div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Personal savings tracking
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Loan application & status
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Transaction history & analytics
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Savings goals & progress
                        </div>
                    </div>
                    <a href="/client-dashboard" class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Access Client Dashboard
                    </a>
                </div>
            </div>

            <!-- Shareholder Dashboard -->
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <i class="fas fa-chart-pie text-3xl mb-2"></i>
                            <h3 class="text-xl font-bold">Shareholder Dashboard</h3>
                            <p class="text-purple-100">Portfolio & Investment Management</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">16.7%</div>
                            <div class="text-sm text-purple-100">Avg. ROI</div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-purple-500 mr-2"></i>
                            Portfolio performance tracking
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-purple-500 mr-2"></i>
                            Dividend history & projections
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-purple-500 mr-2"></i>
                            Investment project analytics
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-purple-500 mr-2"></i>
                            Market insights & trends
                        </div>
                    </div>
                    <a href="/shareholder-dashboard" class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Access Shareholder Dashboard
                    </a>
                </div>
            </div>

            <!-- Cashier Dashboard -->
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <i class="fas fa-cash-register text-3xl mb-2"></i>
                            <h3 class="text-xl font-bold">Cashier Dashboard</h3>
                            <p class="text-blue-100">Financial Operations Management</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">47</div>
                            <div class="text-sm text-blue-100">Daily Transactions</div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-blue-500 mr-2"></i>
                            Transaction processing & approval
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-blue-500 mr-2"></i>
                            Loan management & disbursement
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-blue-500 mr-2"></i>
                            Cash flow monitoring
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-blue-500 mr-2"></i>
                            Financial reporting & summaries
                        </div>
                    </div>
                    <a href="/cashier-dashboard" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Access Cashier Dashboard
                    </a>
                </div>
            </div>

            <!-- Technical Director Dashboard -->
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <i class="fas fa-project-diagram text-3xl mb-2"></i>
                            <h3 class="text-xl font-bold">Technical Director</h3>
                            <p class="text-indigo-100">Project Management & Coordination</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">12</div>
                            <div class="text-sm text-indigo-100">Active Projects</div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-indigo-500 mr-2"></i>
                            Project progress tracking
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-indigo-500 mr-2"></i>
                            Team performance analytics
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-indigo-500 mr-2"></i>
                            Resource allocation management
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-indigo-500 mr-2"></i>
                            Risk assessment & mitigation
                        </div>
                    </div>
                    <a href="/td-dashboard" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Access TD Dashboard
                    </a>
                </div>
            </div>

            <!-- CEO Dashboard -->
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <i class="fas fa-crown text-3xl mb-2 text-yellow-400"></i>
                            <h3 class="text-xl font-bold">CEO Dashboard</h3>
                            <p class="text-gray-300">Executive Overview & Strategy</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-green-400">+18.5%</div>
                            <div class="text-sm text-gray-300">YTD Growth</div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-gray-700 mr-2"></i>
                            Strategic initiatives tracking
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-gray-700 mr-2"></i>
                            Executive KPI monitoring
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-gray-700 mr-2"></i>
                            Market analysis & intelligence
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-gray-700 mr-2"></i>
                            Financial health assessment
                        </div>
                    </div>
                    <a href="/ceo-dashboard" class="w-full bg-gray-800 text-white py-3 px-4 rounded-lg hover:bg-gray-700 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Access CEO Dashboard
                    </a>
                </div>
            </div>

            <!-- Admin Dashboard -->
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <i class="fas fa-cog text-3xl mb-2"></i>
                            <h3 class="text-xl font-bold">Admin Dashboard</h3>
                            <p class="text-red-100">System Management & Control</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-green-400">98.7%</div>
                            <div class="text-sm text-red-100">System Health</div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-red-500 mr-2"></i>
                            User management & permissions
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-red-500 mr-2"></i>
                            System monitoring & logs
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-red-500 mr-2"></i>
                            Database management & backup
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-red-500 mr-2"></i>
                            Security alerts & maintenance
                        </div>
                    </div>
                    <a href="/admin-dashboard" class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Access Admin Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- System Features -->
        <div class="mt-16 bg-white rounded-xl shadow-lg p-8">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Complete Investment Management System</h3>
                <p class="text-gray-600">Modern, comprehensive solution for investment group management</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-2xl text-blue-600"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Advanced Analytics</h4>
                    <p class="text-gray-600">Real-time charts, graphs, and comprehensive reporting for data-driven decisions</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl text-green-600"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Secure & Reliable</h4>
                    <p class="text-gray-600">Bank-level security with role-based access control and data protection</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-mobile-alt text-2xl text-purple-600"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Modern Interface</h4>
                    <p class="text-gray-600">Responsive design that works perfectly on all devices and screen sizes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-16">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="text-center text-gray-600">
                <p>&copy; 2024 BSS Investment Group. All rights reserved.</p>
                <p class="mt-2">Modern Investment Management System - Version 2.0</p>
            </div>
        </div>
    </footer>
</body>
</html>