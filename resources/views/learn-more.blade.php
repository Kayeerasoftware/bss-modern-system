<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn More - BSS Investment Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('images/for web 2.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -2;
        }
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: -1;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg rounded-3xl mt-8 mx-4">
        <div class="px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logo.png') }}" alt="BSS Logo" class="w-12 h-12 rounded-lg">
                <h1 class="text-2xl font-bold text-white">BSS Investment Group</h1>
            </div>
            <div class="flex gap-3">
                <a href="/" class="text-white hover:text-blue-200 px-4 py-2 rounded-lg hover:bg-white/10 transition">Home</a>
                <a href="/login" class="bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-blue-50 transition">Login</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-16 text-center mx-4">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-5xl font-bold text-white mb-4">About BSS Investment Group</h2>
            <p class="text-xl text-gray-200">Comprehensive Financial Management for Savings & Credit Organizations</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-12 px-4">
        <h3 class="text-3xl font-bold text-white text-center mb-12">Our Features</h3>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Feature 1 -->
            <div class="bg-white/95 rounded-2xl p-6 hover:shadow-2xl transition">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 w-16 h-16 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Member Management</h4>
                <p class="text-gray-600">Complete member registration, profile management, and tracking system with bio-data and photo management.</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white/95 rounded-2xl p-6 hover:shadow-2xl transition">
                <div class="bg-gradient-to-br from-green-500 to-green-600 w-16 h-16 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-money-bill-wave text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Loan Management</h4>
                <p class="text-gray-600">Full loan lifecycle management including applications, approvals, disbursements, and repayment tracking.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white/95 rounded-2xl p-6 hover:shadow-2xl transition">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 w-16 h-16 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-chart-line text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Financial Reports</h4>
                <p class="text-gray-600">Comprehensive reporting and analytics for informed decision-making and compliance.</p>
            </div>

            <!-- Feature 4 -->
            <div class="bg-white/95 rounded-2xl p-6 hover:shadow-2xl transition">
                <div class="bg-gradient-to-br from-pink-500 to-pink-600 w-16 h-16 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-exchange-alt text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Transaction Processing</h4>
                <p class="text-gray-600">Secure deposits, withdrawals, and transfers with complete audit trails and receipts.</p>
            </div>

            <!-- Feature 5 -->
            <div class="bg-white/95 rounded-2xl p-6 hover:shadow-2xl transition">
                <div class="bg-gradient-to-br from-yellow-500 to-orange-600 w-16 h-16 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-piggy-bank text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Savings Tracking</h4>
                <p class="text-gray-600">Monitor member savings, interest calculations, and account balances in real-time.</p>
            </div>

            <!-- Feature 6 -->
            <div class="bg-white/95 rounded-2xl p-6 hover:shadow-2xl transition">
                <div class="bg-gradient-to-br from-red-500 to-red-600 w-16 h-16 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-sms text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">SMS Notifications</h4>
                <p class="text-gray-600">Automated SMS alerts for transactions, loan updates, and important announcements via AfricasTalking.</p>
            </div>
        </div>
    </section>

    <!-- Roles Section -->
    <section class="py-12 bg-white/10 backdrop-blur-sm rounded-3xl mx-4">
        <div class="px-4">
            <h3 class="text-3xl font-bold text-white text-center mb-12">User Roles & Access</h3>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Admin -->
                <div class="bg-white/95 rounded-xl p-6 hover:shadow-2xl transition">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #ef4444, #dc2626)">
                            <i class="fas fa-user-shield text-2xl text-white"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">Administrator</h4>
                            <p class="text-xs text-gray-500">System Management</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-red-500 mr-2"></div>
                            <span class="text-sm text-gray-700">User management & permissions</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-red-500 mr-2"></div>
                            <span class="text-sm text-gray-700">System monitoring & logs</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-red-500 mr-2"></div>
                            <span class="text-sm text-gray-700">Database management & backup</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-red-500 mr-2"></div>
                            <span class="text-sm text-gray-700">Security alerts & maintenance</span>
                        </div>
                    </div>
                </div>

                <!-- CEO -->
                <div class="bg-white/95 rounded-xl p-6 hover:shadow-2xl transition">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #374151, #1f2937)">
                            <i class="fas fa-crown text-2xl text-white"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">CEO</h4>
                            <p class="text-xs text-gray-500">Executive Overview</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-gray-700 mr-2"></div>
                            <span class="text-sm text-gray-700">Strategic initiatives tracking</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-gray-700 mr-2"></div>
                            <span class="text-sm text-gray-700">Executive KPI monitoring</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-gray-700 mr-2"></div>
                            <span class="text-sm text-gray-700">Market analysis & intelligence</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-gray-700 mr-2"></div>
                            <span class="text-sm text-gray-700">Financial health assessment</span>
                        </div>
                    </div>
                </div>

                <!-- TD -->
                <div class="bg-white/95 rounded-xl p-6 hover:shadow-2xl transition">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #6366f1, #4f46e5)">
                            <i class="fas fa-project-diagram text-2xl text-white"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">Technical Director</h4>
                            <p class="text-xs text-gray-500">Project Management</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-indigo-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Project progress tracking</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-indigo-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Team performance analytics</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-indigo-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Resource allocation management</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-indigo-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Risk assessment & mitigation</span>
                        </div>
                    </div>
                </div>

                <!-- Cashier -->
                <div class="bg-white/95 rounded-xl p-6 hover:shadow-2xl transition">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #3b82f6, #2563eb)">
                            <i class="fas fa-cash-register text-2xl text-white"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">Cashier</h4>
                            <p class="text-xs text-gray-500">Financial Operations</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-blue-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Transaction processing & approval</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-blue-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Loan management & disbursement</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-blue-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Cash flow monitoring</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-blue-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Financial reporting & summaries</span>
                        </div>
                    </div>
                </div>

                <!-- Shareholder -->
                <div class="bg-white/95 rounded-xl p-6 hover:shadow-2xl transition">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed)">
                            <i class="fas fa-chart-pie text-2xl text-white"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">Shareholder</h4>
                            <p class="text-xs text-gray-500">Portfolio Management</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-purple-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Portfolio performance tracking</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-purple-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Dividend history & projections</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-purple-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Investment project analytics</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-purple-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Market insights & trends</span>
                        </div>
                    </div>
                </div>

                <!-- Client -->
                <div class="bg-white/95 rounded-xl p-6 hover:shadow-2xl transition">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #10b981, #059669)">
                            <i class="fas fa-user-circle text-2xl text-white"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">Client/Member</h4>
                            <p class="text-xs text-gray-500">Personal Management</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-green-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Personal savings tracking</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-green-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Loan application & status</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-green-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Transaction history & analytics</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-green-600 mr-2"></div>
                            <span class="text-sm text-gray-700">Savings goals & progress</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 text-center mx-4">
        <div class="max-w-3xl mx-auto px-4">
            <h3 class="text-4xl font-bold text-white mb-6">Ready to Get Started?</h3>
            <p class="text-xl text-gray-200 mb-8">Join BSS Investment Group today and transform your financial management.</p>
            <div class="flex gap-4 justify-center">
                <a href="/register" class="bg-white text-blue-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-blue-50 transition shadow-xl">Register Now</a>
                <a href="/login" class="bg-blue-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-blue-700 transition shadow-xl">Login</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 rounded-3xl mx-4 mb-8">
        <div class="px-4 text-center">
            <p>&copy; {{ date('Y') }} BSS Investment Group. All rights reserved.</p>
            <p class="text-gray-400 text-sm mt-2">Business Support System - Comprehensive Financial Management</p>
        </div>
    </footer>
    </div>
</body>
</html>
