<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shareholder Dashboard - BSS Investment Group</title>

    <!-- Styles & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Base Styles */
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .scroll-mt-20 { scroll-margin-top: 6rem; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Animations */
        @keyframes messageSlideIn {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes typingBounce {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-6px); }
        }
        @keyframes onlinePulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            50% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        }
        @keyframes reactionPop {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        .message-enter { animation: messageSlideIn 0.3s ease-out; }
        .typing-dot { animation: typingBounce 1.4s infinite ease-in-out; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        .online-indicator { animation: onlinePulse 2s infinite; }
        .reaction-pop { animation: reactionPop 0.2s ease-out; }

        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #8B5CF6 0%, #3B82F6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Message Bubble */
        .message-bubble { position: relative; transition: all 0.2s ease; }
        .message-bubble:hover { transform: translateY(-1px); }
        .message-bubble .reaction-panel {
            opacity: 0; transform: translateY(10px);
            transition: all 0.2s ease;
        }
        .message-bubble:hover .reaction-panel {
            opacity: 1; transform: translateY(0);
        }

        /* Chart Container */
        .chart-container { transition: all 0.3s ease; }
        .chart-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        /* Message Status */
        .message-status { transition: all 0.3s ease; }
        .message-status.sent { color: #9CA3AF; }
        .message-status.delivered { color: #3B82F6; }
        .message-status.read { color: #10B981; }

        /* Reaction Badge */
        .reaction-badge {
            position: absolute; bottom: -8px; right: -8px;
            background: #fff; border-radius: 12px;
            padding: 2px 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-size: 10px; display: flex; align-items: center; gap: 2px;
        }
    </style>
</head>

<body class="bg-gray-50" x-data="shareholderDashboard()" x-init="$watch('reverseOrderState', function(val) { console.log('Reverse order state changed:', val); })">

    <!-- ==================== HEADER ==================== -->
    <nav class="bg-gradient-to-r from-blue-900 to-blue-950 text-white shadow-lg fixed top-0 left-0 right-0 z-20">
        <div class="px-2 sm:px-4 py-3">
            <div class="grid grid-cols-3 gap-2 items-center">
                <!-- Left: BSS Investment Group -->
                <div class="flex items-center space-x-1 sm:space-x-2">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-white mr-1">
                        <i class="fas fa-bars text-base sm:text-lg"></i>
                    </button>
                    <i class="fas fa-building text-sm sm:text-base md:text-lg lg:text-xl"></i>
                    <h1 class="text-[10px] sm:text-xs md:text-sm lg:text-base xl:text-lg font-bold truncate">
                        BSS Investment Group
                    </h1>
                </div>

                <!-- Center: Title -->
                <div class="flex items-center justify-center space-x-1 sm:space-x-2">
                    <i class="fas fa-chart-pie text-sm sm:text-base md:text-lg lg:text-xl"></i>
                    <span class="text-[10px] sm:text-xs md:text-sm lg:text-base xl:text-lg font-semibold truncate">
                        Shareholder Dashboard
                    </span>
                </div>

                <!-- Right: User Info & Logout -->
                <div class="flex items-center justify-end space-x-1 sm:space-x-2">
                    <div class="hidden md:flex items-center space-x-2 cursor-pointer" @click="showProfileModal = true">
                        <div class="text-right">
                            <p class="text-xs text-blue-200">Welcome back,</p>
                            <p class="font-semibold text-sm">John Doe</p>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-blue-800 flex items-center justify-center overflow-hidden border-2 border-blue-600">
                            <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover">
                            <i x-show="!profilePicture" class="fas fa-user text-sm"></i>
                        </div>
                    </div>
                    <form action="/logout" method="POST" class="inline">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="px-2 md:px-3 py-1 bg-blue-800 hover:bg-blue-950 rounded text-xs flex items-center space-x-1 transition">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- ==================== MAIN LAYOUT ==================== -->
    <div class="flex pt-16">

        <!-- ==================== SIDEBAR ==================== -->
        <aside class="w-36 bg-gradient-to-b from-blue-50 to-blue-100 border-r border-blue-200 fixed left-0 h-[calc(100vh-4rem)] overflow-y-auto transition-transform duration-300 lg:translate-x-0 z-10 flex flex-col"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="p-2 flex-1">
                <nav class="space-y-1">
                    <a href="#overview" @click="sidebarOpen = false; activeLink = 'overview'"
                       :class="activeLink === 'overview' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-home w-3"></i><span>Overview</span>
                    </a>
                    <a href="#portfolio" @click="sidebarOpen = false; activeLink = 'portfolio'"
                       :class="activeLink === 'portfolio' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-briefcase w-3"></i><span>Portfolio</span>
                    </a>
                    <a href="#insights" @click="sidebarOpen = false; activeLink = 'insights'"
                       :class="activeLink === 'insights' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-lightbulb w-3"></i><span>Insights</span>
                    </a>
                    <a href="#loans" @click="sidebarOpen = false; activeLink = 'loans'"
                       :class="activeLink === 'loans' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-hand-holding-usd w-3"></i><span>My Loans</span>
                    </a>
                    <a href="#members" @click="sidebarOpen = false; activeLink = 'members'"
                       :class="activeLink === 'members' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-users w-3"></i><span>Members</span>
                    </a>

                    <!-- Additional Navigation Items -->
                    <a href="#profile" @click="sidebarOpen = false; activeLink = 'profile'"
                       :class="activeLink === 'profile' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-user w-3"></i><span>My Profile</span>
                    </a>
                    <a href="#savings" @click="sidebarOpen = false; activeLink = 'savings'"
                       :class="activeLink === 'savings' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-piggy-bank w-3"></i><span>Savings</span>
                    </a>
                    <a href="#dividends" @click="sidebarOpen = false; activeLink = 'dividends'"
                       :class="activeLink === 'dividends' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-coins w-3"></i><span>Dividends</span>
                    </a>
                    <a href="#investments" @click="sidebarOpen = false; activeLink = 'investments'"
                       :class="activeLink === 'investments' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-chart-line w-3"></i><span>Investments</span>
                    </a>
                    <a href="#transactions" @click="sidebarOpen = false; activeLink = 'transactions'"
                       :class="activeLink === 'transactions' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-exchange-alt w-3"></i><span>Transactions</span>
                    </a>
                    <a href="#documents" @click="sidebarOpen = false; activeLink = 'documents'"
                       :class="activeLink === 'documents' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-file-alt w-3"></i><span>Documents</span>
                    </a>
                    <a href="#notifications" @click="sidebarOpen = false; activeLink = 'notifications'"
                       :class="activeLink === 'notifications' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-bell w-3"></i><span>Notifications</span>
                        <span class="ml-auto bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">3</span>
                    </a>
                    <a href="#settings" @click="sidebarOpen = false; activeLink = 'settings'"
                       :class="activeLink === 'settings' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                       class="flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                        <i class="fas fa-cog w-3"></i><span>Settings</span>
                    </a>
                </nav>

                <!-- Quick Actions -->
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <p class="px-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Quick Actions</p>
                    <nav class="space-y-1">
                        <button @click="showLoanRequestModal = true; sidebarOpen = false"
                                class="w-full flex items-center space-x-2 px-2 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition text-xs text-left">
                            <i class="fas fa-plus-circle text-green-500 w-3"></i><span>Request Loan</span>
                        </button>
                        <button @click="showOpportunitiesModal = true; sidebarOpen = false"
                                class="w-full flex items-center space-x-2 px-2 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition text-xs text-left">
                            <i class="fas fa-rocket text-purple-500 w-3"></i><span>New Investment</span>
                        </button>
                        <button @click="showChatModal = true; sidebarOpen = false"
                                class="w-full flex items-center space-x-2 px-2 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition text-xs text-left">
                            <i class="fas fa-headset text-blue-500 w-3"></i><span>Get Support</span>
                        </button>
                    </nav>
                </div>
            </div>

                <!-- User Profile -->
                <div class="flex flex-col items-center py-3 mt-auto border-t border-blue-200">
                    <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mb-2 overflow-hidden cursor-pointer" @click="showProfileViewModal = true">
                        <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover">
                        <i x-show="!profilePicture" class="fas fa-user text-blue-600 text-3xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-800 text-center">John Doe</p>
                    <p class="text-[10px] text-gray-500">Shareholder</p>
                </div>
            </div>

            <!-- Chat Button -->
            <div class="p-2 border-t border-blue-200">
                <button @click="showChatModal = true" class="w-full px-2 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-xs flex items-center justify-center space-x-1">
                    <i class="fas fa-comments"></i><span>Chat</span>
                </button>
            </div>
        </aside>

        <!-- ==================== MAIN CONTENT ==================== -->
        <div class="flex-1 overflow-y-auto lg:ml-36">
            <div class="p-4 md:p-6 lg:p-8 space-y-8">

                <!-- ==================== SECTION 1: PORTFOLIO OVERVIEW ==================== -->
                <section id="overview" class="scroll-mt-20">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Portfolio Overview</h2>
                        <p class="text-sm text-gray-500 mt-1">Your investment summary at a glance</p>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                        <!-- Total Shares -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Shares</p>
                                    <p class="text-2xl font-bold text-green-600" x-text="portfolioData.shares || 0"></p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-certificate text-green-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-arrow-up mr-1"></i><span>+5.2% this quarter</span>
                                </div>
                            </div>
                        </div>

                        <!-- Dividend Earned -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Dividend Earned</p>
                                    <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(portfolioData.dividends)">UGX 125,000</p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-coins text-blue-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-blue-600">
                                    <i class="fas fa-calendar mr-1"></i><span>Last quarter</span>
                                </div>
                            </div>
                        </div>

                        <!-- ROI -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">ROI</p>
                                    <p class="text-2xl font-bold text-purple-600" x-text="(portfolioData.roi || 0).toFixed(1) + '%'"></p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-purple-600">
                                    <i class="fas fa-trending-up mr-1"></i><span>Above target</span>
                                </div>
                            </div>
                        </div>

                        <!-- Portfolio Value -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Portfolio Value</p>
                                    <p class="text-2xl font-bold text-orange-600" x-text="formatCurrency(portfolioData.totalValue)">UGX 2,450,000</p>
                                </div>
                                <div class="p-3 bg-orange-100 rounded-full">
                                    <i class="fas fa-briefcase text-orange-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-arrow-up mr-1"></i><span>+8.3% YTD</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 2: PORTFOLIO ANALYTICS ==================== -->
                <section id="portfolio" class="scroll-mt-20">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Portfolio Analytics</h2>
                        <p class="text-sm text-gray-500 mt-1">Detailed performance metrics and trends</p>
                    </div>

                    <!-- Key Metrics -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Share Price</p>
                                    <p class="text-2xl font-bold" x-text="'UGX ' + (portfolioData.sharePrice || 1800).toLocaleString()"></p>
                                </div>
                                <i class="fas fa-chart-line text-3xl opacity-75"></i>
                            </div>
                            <div class="mt-2 text-xs opacity-75"><i class="fas fa-arrow-up mr-1"></i>2.3% this month</div>
                        </div>

                        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Return</p>
                                    <p class="text-2xl font-bold" x-text="formatCurrency(portfolioData.totalValue + portfolioData.dividends)"></p>
                                </div>
                                <i class="fas fa-coins text-3xl opacity-75"></i>
                            </div>
                            <div class="mt-2 text-xs opacity-75"><i class="fas fa-plus mr-1"></i>Including dividends</div>
                        </div>

                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Benchmark</p>
                                    <p class="text-2xl font-bold">+3.2%</p>
                                </div>
                                <i class="fas fa-trophy text-3xl opacity-75"></i>
                            </div>
                            <div class="mt-2 text-xs opacity-75"><i class="fas fa-chart-bar mr-1"></i>vs Market average</div>
                        </div>

                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Risk Level</p>
                                    <p class="text-2xl font-bold">Low</p>
                                </div>
                                <i class="fas fa-shield-alt text-3xl opacity-75"></i>
                            </div>
                            <div class="mt-2 text-xs opacity-75"><i class="fas fa-check-circle mr-1"></i>Diversified portfolio</div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="space-y-6">
                        <!-- Charts Row 1 -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                        <!-- Portfolio Performance Chart -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Portfolio Performance</h3>
                                    <p class="text-sm text-gray-500">Value & Share Price Trends</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button @click="portfolioView = 'value'; updatePortfolioChart(portfolioHistory)"
                                            :class="portfolioView === 'value' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1 rounded text-xs transition">Value</button>
                                    <button @click="portfolioView = 'price'; updatePortfolioChart(portfolioHistory)"
                                            :class="portfolioView === 'price' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1 rounded text-xs transition">Price</button>
                                    <button @click="portfolioView = 'combined'; updatePortfolioChart(portfolioHistory)"
                                            :class="portfolioView === 'combined' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1 rounded text-xs transition">Combined</button>
                                </div>
                            </div>
                            <div style="height: 280px;"><canvas id="portfolioChart"></canvas></div>
                        </div>

                        <!-- Asset Allocation Chart -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Investment Breakdown</h3>
                                    <p class="text-sm text-gray-500">Current asset distribution</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-800" x-text="formatCurrency(portfolioData.totalValue)"></p>
                                    <p class="text-xs text-gray-500">Total invested</p>
                                </div>
                            </div>
                            <div style="height: 280px;"><canvas id="allocationChart"></canvas></div>
                        </div>
                    </div>

                    <!-- Charts Row 2 -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Dividend Trends Chart -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Dividend Performance</h3>
                                    <p class="text-sm text-gray-500">Quarterly payments & growth</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-green-600" x-text="formatCurrency(portfolioData.dividends)"></p>
                                    <p class="text-xs text-gray-500">Total received</p>
                                </div>
                            </div>
                            <div style="height: 280px;"><canvas id="dividendChart"></canvas></div>
                        </div>

                        <!-- ROI Comparison Chart -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Project Performance</h3>
                                    <p class="text-sm text-gray-500">ROI comparison & potential</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button @click="roiView = 'expected'; updateRoiChart(investmentProjects)"
                                            :class="roiView === 'expected' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1 rounded text-xs transition">Expected</button>
                                    <button @click="roiView = 'actual'; updateRoiChart(investmentProjects)"
                                            :class="roiView === 'actual' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1 rounded text-xs transition">Actual</button>
                                </div>
                            </div>
                            <div style="height: 280px;"><canvas id="roiChart"></canvas></div>
                        </div>
                    </div>

                    <!-- Member Activity Chart -->
                    <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Member Activity & Growth</h3>
                            <div class="flex space-x-2">
                                <button @click="memberChartType = 'activity'; window.updateMemberChart('activity')"
                                        :class="memberChartType === 'activity' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="px-3 py-1 rounded text-xs transition">Activity</button>
                                <button @click="memberChartType = 'growth'; window.updateMemberChart('growth')"
                                        :class="memberChartType === 'growth' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="px-3 py-1 rounded text-xs transition">Growth</button>
                                <button @click="memberChartType = 'distribution'; window.updateMemberChart('distribution')"
                                        :class="memberChartType === 'distribution' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="px-3 py-1 rounded text-xs transition">Distribution</button>
                            </div>
                        </div>
                        <div style="height: 300px;"><canvas id="memberChart"></canvas></div>
                    </div>

                    <!-- Tables Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                        <!-- Active Investment Projects -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Active Projects</h3>
                                <div class="flex items-center space-x-2">
                                    <button @click="toggleReverseOrder('projects')"
                                            :class="reverseOrderState.projects ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1 rounded text-xs transition" title="Reverse Order">
                                        <i class="fas fa-sort-numeric-alt mr-1"></i>
                                        <span x-text="reverseOrderState.projects ? 'Oldest First' : 'Newest First'"></span>
                                    </button>
                                    <button class="text-purple-600 text-sm hover:underline">View All</button>
                                </div>
                            </div>
                            <div class="space-y-4 max-h-96 overflow-y-auto">
                                <template x-for="(project, index) in getReversedData(investmentProjects, 'projects')" :key="project.id">
                                    <div class="p-4 border rounded-lg hover:bg-gray-50">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-medium" x-text="project.name">Community Water Project</h4>
                                            <span class="text-sm font-medium text-purple-600" x-text="project.progress + '%'">65%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                            <div class="bg-purple-500 h-2 rounded-full" :style="`width: ${project.progress}%`"></div>
                                        </div>
                                        <div class="flex justify-between text-sm text-gray-600">
                                            <span x-text="'Budget: ' + formatCurrency(project.budget)">Budget: UGX 5,000,000</span>
                                            <div class="flex space-x-2">
                                                <span x-text="'Expected: ' + project.expected_roi + '%'">Expected: 15.0%</span>
                                                <span x-text="'Actual: ' + project.roi + '%'">Actual: 12.5%</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Dividend History -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Dividend History</h3>
                                <div class="flex items-center space-x-2">
                                    <button @click="toggleReverseOrder('dividends')"
                                            :class="reverseOrderState.dividends ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1 rounded text-xs transition" title="Reverse Order">
                                        <i class="fas fa-sort-numeric-alt mr-1"></i>
                                        <span x-text="reverseOrderState.dividends ? 'Oldest First' : 'Newest First'"></span>
                                    </button>
                                    <button class="text-purple-600 text-sm hover:underline">Download Report</button>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="text-left py-2 text-sm font-medium text-gray-600">Period</th>
                                            <th class="text-left py-2 text-sm font-medium text-gray-600">Amount</th>
                                            <th class="text-left py-2 text-sm font-medium text-gray-600">Rate</th>
                                            <th class="text-left py-2 text-sm font-medium text-gray-600">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="dividend in getReversedData(dividendHistory, 'dividends')" :key="dividend.id">
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="py-3 text-sm" x-text="dividend.period">Q4 2023</td>
                                                <td class="py-3 text-sm font-medium text-green-600" x-text="formatCurrency(dividend.amount)">UGX 125,000</td>
                                                <td class="py-3 text-sm" x-text="dividend.rate + '%'">10%</td>
                                                <td class="py-3">
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Paid</span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 3: MARKET INSIGHTS ==================== -->
                <section id="insights" class="scroll-mt-20">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Market Insights</h2>
                        <p class="text-sm text-gray-500 mt-1">Latest announcements and opportunities</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6"
                             x-data="{ performanceData: {}, dividendAnnouncement: {}, opportunities: [] }"
                             x-init="initInsightsData()">

                            <!-- Performance Card -->
                            <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-500 cursor-pointer hover:shadow-lg transition"
                                 @click="showPerformanceModal = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-up text-green-600 mr-2"></i>
                                        <span class="text-sm font-medium">Strong Performance</span>
                                    </div>
                                    <span class="text-xs bg-green-600 text-white px-2 py-1 rounded-full"
                                          x-text="performanceData.benchmark_comparison ? '+' + performanceData.benchmark_comparison + '%' : '+3.2%'"></span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">Portfolio outperforming market</p>
                                <div class="mt-2 flex items-center text-xs text-green-700">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    <span x-text="performanceData.trend === 'up' ? 'Upward trend' : 'View details'">View details</span>
                                </div>
                            </div>

                            <!-- Dividend Announcement Card -->
                            <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500 cursor-pointer hover:shadow-lg transition"
                                 @click="showDividendModal = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                        <span class="text-sm font-medium">Dividend Announcement</span>
                                    </div>
                                    <span class="text-xs bg-blue-600 text-white px-2 py-1 rounded-full">NEW</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1"
                                   x-text="dividendAnnouncement.payment_date ? 'Payment: ' + new Date(dividendAnnouncement.payment_date).toLocaleDateString() : 'Q1 2024 dividend expected next month'"></p>
                                <div class="mt-2 flex items-center text-xs text-blue-700">
                                    <i class="fas fa-coins mr-1"></i>
                                    <span x-text="dividendAnnouncement.amount ? formatCurrency(dividendAnnouncement.amount) : 'View details'">View details</span>
                                </div>
                            </div>

                            <!-- Growth Opportunity Card -->
                            <div class="p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500 cursor-pointer hover:shadow-lg transition"
                                 @click="showOpportunitiesModal = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-chart-line text-purple-600 mr-2"></i>
                                        <span class="text-sm font-medium">Growth Opportunity</span>
                                    </div>
                                    <span class="text-xs bg-purple-600 text-white px-2 py-1 rounded-full" x-text="opportunities.length || '3'"></span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1" x-text="opportunities[0]?.title || 'New investment project launching'"></p>
                                <div class="mt-2 flex items-center text-xs text-purple-700">
                                    <i class="fas fa-rocket mr-1"></i>
                                    <span x-text="opportunities[0]?.expected_roi ? 'ROI: ' + opportunities[0].expected_roi + '%' : 'Explore opportunities'">Explore opportunities</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 4: LOAN MANAGEMENT ==================== -->
                <section id="loans" class="scroll-mt-20">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">My Loan Management</h2>
                        <p class="text-sm text-gray-500 mt-1">View and manage your loan applications</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-4 md:p-6 border-b border-gray-100">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <h3 class="text-lg font-semibold text-gray-800">Financial Overview</h3>
                                <button @click="showLoanRequestModal = true" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium">
                                    <i class="fas fa-plus mr-2"></i>Request Loan
                                </button>
                            </div>
                        </div>

                        <!-- Financial Summary Cards -->
                        <div class="p-4 md:p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-600">Total Savings</p>
                                        <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(myFinancials.savings)">UGX 0</p>
                                    </div>
                                    <i class="fas fa-piggy-bank text-green-600 text-3xl"></i>
                                </div>
                            </div>
                            <div class="p-4 bg-red-50 rounded-lg border-l-4 border-red-500">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-600">Total Loan</p>
                                        <p class="text-2xl font-bold text-red-600" x-text="formatCurrency(myFinancials.loan)">UGX 0</p>
                                    </div>
                                    <i class="fas fa-hand-holding-usd text-red-600 text-3xl"></i>
                                </div>
                            </div>
                            <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-600">Net Balance</p>
                                        <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(myFinancials.balance)">UGX 0</p>
                                    </div>
                                    <i class="fas fa-wallet text-blue-600 text-3xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Loan Applications Table -->
                        <div class="mt-6">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-md font-semibold text-gray-700">My Loan Applications</h4>
                                <button @click="toggleReverseOrder('loans')"
                                        :class="reverseOrderState.loans ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="px-3 py-1 rounded text-xs transition" title="Reverse Order">
                                    <i class="fas fa-sort-numeric-alt mr-1"></i>
                                    <span x-text="reverseOrderState.loans ? 'Oldest First' : 'Newest First'"></span>
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loan ID</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Repayment Period</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monthly Payment</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Applied</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-if="myLoans.length === 0">
                                            <tr>
                                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                                    <p>No loan applications yet. Click "Request Loan" to apply.</p>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-for="loan in getReversedData(myLoans, 'loans')" :key="loan.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-medium" x-text="loan.loan_id"></td>
                                                <td class="px-4 py-3 text-sm font-bold text-purple-600" x-text="formatCurrency(loan.amount)"></td>
                                                <td class="px-4 py-3 text-sm" x-text="loan.purpose"></td>
                                                <td class="px-4 py-3 text-sm" x-text="loan.repayment_months + ' months'"></td>
                                                <td class="px-4 py-3 text-sm font-medium text-blue-600" x-text="formatCurrency(loan.monthly_payment || 0)"></td>
                                                <td class="px-4 py-3">
                                                    <span class="px-3 py-1 text-xs font-medium rounded-full"
                                                          :class="{
                                                              'bg-yellow-100 text-yellow-800': loan.status === 'pending',
                                                              'bg-green-100 text-green-800': loan.status === 'approved',
                                                              'bg-red-100 text-red-800': loan.status === 'rejected'
                                                          }"
                                                          x-text="loan.status.toUpperCase()"></span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600" x-text="new Date(loan.created_at).toLocaleDateString()"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 5: MEMBERS ANALYTICS ==================== -->
                <section id="members" class="scroll-mt-20 pb-8">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Members Analytics</h2>
                        <p class="text-sm text-gray-500 mt-1">Organization statistics and member directory</p>
                    </div>

                    <!-- Member Statistics Cards -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer"
                             @click="filterByRole('all')">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Members</p>
                                    <p class="text-2xl font-bold" x-text="allMembers.length || 82">82</p>
                                </div>
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-users text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center text-xs opacity-75">
                                <span class="bg-white bg-opacity-20 px-2 py-1 rounded-full">
                                    <i class="fas fa-arrow-up mr-1"></i>+12% this quarter
                                </span>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer"
                             @click="filterByRole('client')">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Active Now</p>
                                    <p class="text-2xl font-bold" x-text="Math.round(allMembers.length * 0.65) || 53">53</p>
                                </div>
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-circle text-2xl animate-pulse"></i>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center text-xs opacity-75">
                                <span class="bg-white bg-opacity-20 px-2 py-1 rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i>Online status
                                </span>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer"
                             @click="filterByRole('shareholder')">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">New This Month</p>
                                    <p class="text-2xl font-bold" x-text="Math.round(allMembers.length * 0.08) || 7">7</p>
                                </div>
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-plus text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center text-xs opacity-75">
                                <span class="bg-white bg-opacity-20 px-2 py-1 rounded-full">
                                    <i class="fas fa-calendar mr-1"></i>December 2024
                                </span>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Savings</p>
                                    <p class="text-2xl font-bold" x-text="formatCurrency(filteredMembers.reduce((sum, m) => sum + (m.savings || 0), 0))">UGX 125M</p>
                                </div>
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-piggy-bank text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center text-xs opacity-75">
                                <span class="bg-white bg-opacity-20 px-2 py-1 rounded-full">
                                    <i class="fas fa-arrow-up mr-1"></i>+8.5% growth
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="space-y-6">
                        <!-- ApexCharts Row 1 -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                        <!-- Member Role Distribution -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Role Distribution</h3>
                                    <p class="text-sm text-gray-500">Members by role</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button @click="updateMemberRoleChart('donut')"
                                            :class="memberRoleChartType === 'donut' ? 'bg-purple-600 text-white' : 'bg-gray-200'"
                                            class="px-3 py-1 rounded text-xs transition">Donut</button>
                                    <button @click="updateMemberRoleChart('pie')"
                                            :class="memberRoleChartType === 'pie' ? 'bg-purple-600 text-white' : 'bg-gray-200'"
                                            class="px-3 py-1 rounded text-xs transition">Pie</button>
                                    <button @click="updateMemberRoleChart('bar')"
                                            :class="memberRoleChartType === 'bar' ? 'bg-purple-600 text-white' : 'bg-gray-200'"
                                            class="px-3 py-1 rounded text-xs transition">Bar</button>
                                </div>
                            </div>
                            <div id="memberRoleChart" style="height: 300px;"></div>
                        </div>

                        <!-- Member Growth -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Member Growth</h3>
                                    <p class="text-sm text-gray-500">Quarterly growth trend</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button @click="updateMemberGrowthChart('area')"
                                            :class="memberGrowthView === 'area' ? 'bg-purple-600 text-white' : 'bg-gray-200'"
                                            class="px-3 py-1 rounded text-xs transition">Area</button>
                                    <button @click="updateMemberGrowthChart('bar')"
                                            :class="memberGrowthView === 'bar' ? 'bg-purple-600 text-white' : 'bg-gray-200'"
                                            class="px-3 py-1 rounded text-xs transition">Bar</button>
                                    <button @click="updateMemberGrowthChart('line')"
                                            :class="memberGrowthView === 'line' ? 'bg-purple-600 text-white' : 'bg-gray-200'"
                                            class="px-3 py-1 rounded text-xs transition">Line</button>
                                </div>
                            </div>
                            <div id="memberGrowthChart" style="height: 300px;"></div>
                        </div>
                    </div>

                    <!-- ApexCharts Row 2 -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <!-- Savings Distribution -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Savings Distribution</h3>
                                    <p class="text-sm text-gray-500">By range</p>
                                </div>
                                <select @change="updateSavingsChart($event.target.value)" class="px-3 py-1 border rounded-lg text-sm">
                                    <option value="horizontal">Horizontal</option>
                                    <option value="vertical">Vertical</option>
                                </select>
                            </div>
                            <div id="savingsDistributionChart" style="height: 250px;"></div>
                        </div>

                        <!-- Activity Heatmap -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Activity Heatmap</h3>
                                    <p class="text-sm text-gray-500">Last 12 months</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                    <span class="text-xs text-gray-500">High</span>
                                    <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                                    <span class="text-xs text-gray-500">Medium</span>
                                    <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                                    <span class="text-xs text-gray-500">Low</span>
                                </div>
                            </div>
                            <div id="activityHeatmapChart" style="height: 250px;"></div>
                        </div>

                        <!-- Location Treemap -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Top Locations</h3>
                                    <p class="text-sm text-gray-500">By region</p>
                                </div>
                            </div>
                            <div id="locationChart" style="height: 250px;"></div>
                        </div>
                    </div>

                    <!-- Activity & Statistics -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
                        <!-- Recent Activity -->
                        <div class="bg-white p-6 rounded-xl shadow-lg lg:col-span-2">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
                                    <p class="text-sm text-gray-500">Latest member actions</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button @click="toggleReverseOrder('activities')"
                                            :class="reverseOrderState.activities ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1 rounded text-xs transition" title="Reverse Order">
                                        <i class="fas fa-sort-numeric-alt mr-1"></i>
                                        <span x-text="reverseOrderState.activities ? 'Oldest First' : 'Newest First'"></span>
                                    </button>
                                    <button @click="showActivityTimeline = !showActivityTimeline" class="text-purple-600 text-sm hover:underline">
                                        <i class="fas fa-history mr-1"></i>View All
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-3" id="activityTimeline">
                                <template x-for="(activity, index) in getReversedData(recentActivities, 'activities')" :key="index">
                                    <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="activity.bgColor">
                                            <i :class="activity.icon" class="text-white"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium" x-text="activity.title"></p>
                                            <p class="text-xs text-gray-500" x-text="activity.description"></p>
                                        </div>
                                        <span class="text-xs text-gray-400" x-text="activity.time"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Statistics Summary -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Statistics</h3>
                                    <p class="text-sm text-gray-500">Quick summary</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Active Rate</span>
                                        <span class="font-medium text-green-600">65%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: 65%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Retention Rate</span>
                                        <span class="font-medium text-blue-600">92%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: 92%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Growth Rate</span>
                                        <span class="font-medium text-purple-600">12%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-500 h-2 rounded-full" style="width: 12%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Avg. Savings</span>
                                        <span class="font-medium text-orange-600" x-text="formatCurrency(averageSavings)"></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-orange-500 h-2 rounded-full" style="width: 75%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Member Insights -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-4 md:p-6 border-b border-gray-100">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Member Insights & Analytics</h3>
                                    <p class="text-sm text-gray-500">Comprehensive engagement metrics</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                <button @click="insightView = 'engagement'; updateInsightsChart()"
                                        :class="insightView === 'engagement' ? 'bg-purple-600 text-white' : 'bg-gray-200'"
                                        class="px-3 py-1 rounded text-xs transition">
                                    <i class="fas fa-chart-pie mr-1"></i>Engagement
                                </button>
                                <button @click="insightView = 'funnel'; updateInsightsChart()"
                                        :class="insightView === 'funnel' ? 'bg-purple-600 text-white' : 'bg-gray-200'"
                                        class="px-3 py-1 rounded text-xs transition">
                                    <i class="fas fa-filter mr-1"></i>Funnel
                                </button>
                                <button @click="insightView = 'retention'; updateInsightsChart()"
                                        :class="insightView === 'retention' ? 'bg-purple-600 text-white' : 'bg-gray-200'"
                                        class="px-3 py-1 rounded text-xs transition">
                                    <i class="fas fa-chart-line mr-1"></i>Retention
                                </button>
                            </div>
                        </div>

                        <!-- Online Members Banner -->
                        <div class="p-4 md:p-5 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-bold text-lg"
                                             x-text="onlineMembersCount"></div>
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white animate-pulse"></div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">Members Online Now</p>
                                        <p class="text-sm text-gray-600">Real-time active users</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-green-600" x-text="Math.round(allMembers.length * 0.65)"></p>
                                    <p class="text-xs text-gray-500">Active this hour</p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center space-x-4">
                                <div class="flex-1">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-600">Activity Level</span>
                                        <span class="font-medium text-green-600">65%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-green-400 to-emerald-500 h-2 rounded-full" style="width: 65%"></div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>Updated: <span x-text="new Date().toLocaleTimeString()"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Engagement & Segments Charts -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mt-6">
                            <!-- Engagement Radar -->
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <h4 class="text-md font-semibold text-gray-800">Engagement Radar</h4>
                                        <p class="text-xs text-gray-500">Multi-dimensional member analysis</p>
                                    </div>
                                    <div class="flex space-x-1">
                                        <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
                                        <span class="text-xs text-gray-500">This Month</span>
                                        <span class="w-3 h-3 bg-blue-300 rounded-full ml-2"></span>
                                        <span class="text-xs text-gray-500">Last Month</span>
                                    </div>
                                </div>
                                <div id="engagementRadarChart" style="height: 280px;"></div>
                            </div>

                            <!-- Member Segments -->
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <h4 class="text-md font-semibold text-gray-800">Member Segments</h4>
                                        <p class="text-xs text-gray-500">Category breakdown</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-xs text-gray-600">High Value</p>
                                                <p class="text-xl font-bold text-blue-600" x-text="Math.round(allMembers.length * 0.15)"></p>
                                            </div>
                                            <i class="fas fa-crown text-blue-500 text-xl"></i>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Savings > UGX 5M</p>
                                    </div>
                                    <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-xs text-gray-600">Active</p>
                                                <p class="text-xl font-bold text-green-600" x-text="Math.round(allMembers.length * 0.65)"></p>
                                            </div>
                                            <i class="fas fa-fire text-green-500 text-xl"></i>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Active in 30 days</p>
                                    </div>
                                    <div class="p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-xs text-gray-600">Growth</p>
                                                <p class="text-xl font-bold text-purple-600" x-text="Math.round(allMembers.length * 0.08)"></p>
                                            </div>
                                            <i class="fas fa-rocket text-purple-500 text-xl"></i>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">New this month</p>
                                    </div>
                                    <div class="p-4 bg-orange-50 rounded-lg border-l-4 border-orange-500">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-xs text-gray-600">At Risk</p>
                                                <p class="text-xl font-bold text-orange-600" x-text="Math.round(allMembers.length * 0.05)"></p>
                                            </div>
                                            <i class="fas fa-exclamation-triangle text-orange-500 text-xl"></i>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Inactive > 60 days</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Engagement Score -->
                        <div class="mt-6 p-4 md:p-5 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg border border-purple-200">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-md font-semibold text-gray-800">Engagement Score Dashboard</h4>
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-purple-600" x-text="Math.round(averageEngagementScore)"></p>
                                        <p class="text-xs text-gray-500">Avg. Score</p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="p-3 bg-white rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-gray-600">Login Frequency</span>
                                        <span class="text-xs font-medium text-green-600">85%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
                                    </div>
                                </div>
                                <div class="p-3 bg-white rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-gray-600">Transaction Activity</span>
                                        <span class="text-xs font-medium text-blue-600">72%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: 72%"></div>
                                    </div>
                                </div>
                                <div class="p-3 bg-white rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-gray-600">Savings Rate</span>
                                        <span class="text-xs font-medium text-purple-600">68%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-500 h-2 rounded-full" style="width: 68%"></div>
                                    </div>
                                </div>
                                <div class="p-3 bg-white rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-gray-600">Chat Engagement</span>
                                        <span class="text-xs font-medium text-orange-600">55%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-orange-500 h-2 rounded-full" style="width: 55%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Members Directory -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-4 md:p-6 border-b border-gray-100">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Members Directory</h3>
                                    <p class="text-sm text-gray-500">Detailed member information</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                <button @click="toggleReverseOrder('members')"
                                        :class="reverseOrderState.members ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="px-3 py-2 rounded text-xs transition" title="Reverse Order">
                                    <i class="fas fa-sort-numeric-alt mr-1"></i>
                                    <span x-text="reverseOrderState.members ? 'Oldest First' : 'Newest First'"></span>
                                </button>
                                <div class="relative">
                                    <input type="text" x-model="memberSearch" @input="filterMembers()"
                                           placeholder="Search members..."
                                           class="pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                                </div>
                                <select x-model="memberRoleFilter" @change="filterMembers()" class="px-3 py-2 border rounded-lg text-sm">
                                    <option value="">All Roles</option>
                                    <option value="client">Client</option>
                                    <option value="shareholder">Shareholder</option>
                                    <option value="cashier">Cashier</option>
                                    <option value="td">TD</option>
                                    <option value="ceo">CEO</option>
                                </select>
                                <button @click="exportMembersData()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    <i class="fas fa-file-export mr-2"></i>Export
                                </button>
                                <button @click="refreshAllData()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">
                                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                                </button>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            @click="sortMembers('member_id')">
                                            <div class="flex items-center space-x-1">
                                                <span>Member ID</span>
                                                <i class="fas fa-sort text-gray-400"></i>
                                            </div>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            @click="sortMembers('full_name')">
                                            <div class="flex items-center space-x-1">
                                                <span>Full Name</span>
                                                <i class="fas fa-sort text-gray-400"></i>
                                            </div>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occupation</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            @click="sortMembers('savings')">
                                            <div class="flex items-center space-x-1">
                                                <span>Savings</span>
                                                <i class="fas fa-sort text-gray-400"></i>
                                            </div>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="member in getReversedData(filteredMembers, 'members')" :key="member.member_id">
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900" x-text="member.member_id"></td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center text-white font-bold text-xs"
                                                         x-text="member.full_name?.charAt(0)"></div>
                                                    <span class="text-sm text-gray-900" x-text="member.full_name"></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600" x-text="member.email"></td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                <span class="flex items-center">
                                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                                    <span x-text="member.location || 'N/A'"></span>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600" x-text="member.occupation || 'N/A'"></td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-green-600" x-text="formatCurrency(member.savings)"></td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-red-600" x-text="formatCurrency(member.loan)"></td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium"
                                                :class="member.balance >= 0 ? 'text-blue-600' : 'text-red-600'"
                                                x-text="formatCurrency(member.balance)"></td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs rounded-full"
                                                      :class="{
                                                          'bg-blue-100 text-blue-800': member.role === 'client',
                                                          'bg-purple-100 text-purple-800': member.role === 'shareholder',
                                                          'bg-green-100 text-green-800': member.role === 'cashier',
                                                          'bg-orange-100 text-orange-800': member.role === 'td',
                                                          'bg-red-100 text-red-800': member.role === 'ceo'
                                                      }"
                                                      x-text="member.role"></span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <button @click="viewMemberDetails(member)" class="text-blue-600 hover:text-blue-800" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button @click="messageMember(member)" class="text-purple-600 hover:text-purple-800" title="Message">
                                                        <i class="fas fa-comment"></i>
                                                    </button>
                                                    <button @click="sendPaymentRequest(member)" class="text-green-600 hover:text-green-800" title="Request Payment">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                            <!-- Table Footer -->
                            <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="text-sm text-gray-600">
                                Showing <span class="font-medium" x-text="filteredMembers.length"></span> of <span class="font-medium" x-text="allMembers.length"></span> members
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-sm">
                                    <span class="font-medium">Total Savings: </span>
                                    <span class="text-green-600 font-bold" x-text="formatCurrency(filteredMembers.reduce((sum, m) => sum + (m.savings || 0), 0))"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium">Total Loans: </span>
                                    <span class="text-red-600 font-bold" x-text="formatCurrency(filteredMembers.reduce((sum, m) => sum + (m.loan || 0), 0))"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium">Net Balance: </span>
                                    <span class="text-blue-600 font-bold" x-text="formatCurrency(filteredMembers.reduce((sum, m) => sum + (m.balance || 0), 0))"></span>
                                </div>
                            </div>
                            <!-- Pagination -->
                            <div class="flex items-center space-x-2">
                                <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1"
                                        class="px-3 py-1 border rounded-lg text-sm disabled:opacity-50"
                                        :class="currentPage === 1 ? 'bg-gray-100' : 'hover:bg-gray-50'">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <span class="text-sm text-gray-600">Page <span class="font-medium" x-text="currentPage"></span> of <span class="font-medium" x-text="totalPages"></span></span>
                                <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages"
                                        class="px-3 py-1 border rounded-lg text-sm disabled:opacity-50"
                                        :class="currentPage === totalPages ? 'bg-gray-100' : 'hover:bg-gray-50'">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 6: MY PROFILE ==================== -->
                <section id="profile" class="scroll-mt-20">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">My Profile</h2>
                        <p class="text-sm text-gray-500 mt-1">Manage your personal information</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center space-x-6">
                                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center text-white text-3xl font-bold">
                                    <span x-text="profilePicture ? '' : 'JD'"></span>
                                    <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover rounded-full">
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">John Doe</h3>
                                    <p class="text-gray-600">Shareholder</p>
                                    <p class="text-sm text-gray-500">Member ID: BSS001</p>
                                    <button @click="showProfileModal = true" class="mt-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">
                                        <i class="fas fa-camera mr-2"></i>Change Photo
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" value="John Doe" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" value="john.doe@example.com" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                    <input type="tel" value="+256 701 234 567" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                    <input type="text" value="Kampala, Uganda" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 7: SAVINGS ==================== -->
                <section id="savings" class="scroll-mt-20">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">My Savings</h2>
                        <p class="text-sm text-gray-500 mt-1">Track your savings and deposits</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Savings</p>
                                    <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(myFinancials.savings)">UGX 1,500,000</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-piggy-bank text-green-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">This Month</p>
                                    <p class="text-2xl font-bold text-blue-600">UGX 250,000</p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Interest Earned</p>
                                    <p class="text-2xl font-bold text-purple-600">UGX 45,000</p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Withdrawals</p>
                                    <p class="text-2xl font-bold text-orange-600">UGX 100,000</p>
                                </div>
                                <div class="p-3 bg-orange-100 rounded-full">
                                    <i class="fas fa-wallet text-orange-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Savings History</h3>
                            <div class="flex items-center space-x-2">
                                <button @click="toggleReverseOrder('savings')"
                                        :class="reverseOrderState.savings ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="px-3 py-1 rounded text-xs transition" title="Reverse Order">
                                    <i class="fas fa-sort-numeric-alt mr-1"></i>
                                    <span x-text="reverseOrderState.savings ? 'Oldest First' : 'Newest First'"></span>
                                </button>
                                <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    <i class="fas fa-download mr-2"></i>Export
                                </button>
                            </div>
                        </div>
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">Dec 15, 2024</td>
                                        <td class="px-4 py-3 text-sm"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Deposit</span></td>
                                        <td class="px-4 py-3 text-sm font-medium text-green-600">+UGX 100,000</td>
                                        <td class="px-4 py-3 text-sm">UGX 1,500,000</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">Dec 1, 2024</td>
                                        <td class="px-4 py-3 text-sm"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Deposit</span></td>
                                        <td class="px-4 py-3 text-sm font-medium text-green-600">+UGX 150,000</td>
                                        <td class="px-4 py-3 text-sm">UGX 1,400,000</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">Nov 15, 2024</td>
                                        <td class="px-4 py-3 text-sm"><span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Withdrawal</span></td>
                                        <td class="px-4 py-3 text-sm font-medium text-red-600">-UGX 100,000</td>
                                        <td class="px-4 py-3 text-sm">UGX 1,250,000</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 8: DIVIDENDS ==================== -->
                <section id="dividends" class="scroll-mt-20">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Dividend Management</h2>
                        <p class="text-sm text-gray-500 mt-1">View your dividend earnings and history</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Dividends</p>
                                    <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(portfolioData.dividends)">UGX 125,000</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-coins text-green-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Q4 2024</p>
                                    <p class="text-2xl font-bold text-blue-600">UGX 35,000</p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-calendar text-blue-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Dividend Rate</p>
                                    <p class="text-2xl font-bold text-purple-600">10.5%</p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Pending</p>
                                    <p class="text-2xl font-bold text-orange-600">UGX 0</p>
                                </div>
                                <div class="p-3 bg-orange-100 rounded-full">
                                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Dividend History</h3>
                            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                <i class="fas fa-download mr-2"></i>Export Statement
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shares</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rate</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="dividend in dividendHistory" :key="dividend.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium" x-text="dividend.period">Q4 2023</td>
                                            <td class="px-4 py-3 text-sm">1,250</td>
                                            <td class="px-4 py-3 text-sm" x-text="dividend.rate + '%'">10%</td>
                                            <td class="px-4 py-3 text-sm font-medium text-green-600" x-text="formatCurrency(dividend.amount)">UGX 125,000</td>
                                            <td class="px-4 py-3 text-sm">Dec 15, 2023</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Paid</span></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 9: INVESTMENTS ==================== -->
                <section id="investments" class="scroll-mt-20">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">My Investments</h2>
                        <p class="text-sm text-gray-500 mt-1">Track your investment portfolio</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Invested</p>
                                    <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(portfolioData.totalValue)">UGX 2,450,000</p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Returns</p>
                                    <p class="text-2xl font-bold text-green-600">UGX 312,500</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-trending-up text-green-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">ROI</p>
                                    <p class="text-2xl font-bold text-purple-600" x-text="(portfolioData.roi || 0).toFixed(1) + '%'">12.5%</p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Active Projects</p>
                                    <p class="text-2xl font-bold text-orange-600" x-text="investmentProjects.length">3</p>
                                </div>
                                <div class="p-3 bg-orange-100 rounded-full">
                                    <i class="fas fa-project-diagram text-orange-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Investment Details</h3>
                            <div class="flex items-center space-x-2">
                                <button @click="toggleReverseOrder('investments')"
                                        :class="reverseOrderState.investments ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="px-3 py-1 rounded text-xs transition" title="Reverse Order">
                                    <i class="fas fa-sort-numeric-alt mr-1"></i>
                                    <span x-text="reverseOrderState.investments ? 'Oldest First' : 'Newest First'"></span>
                                </button>
                                <button @click="showOpportunitiesModal = true" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">
                                    <i class="fas fa-plus mr-2"></i>New Investment
                                </button>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <template x-for="project in getReversedData(investmentProjects, 'investments')" :key="project.id">
                                <div class="p-4 border rounded-lg hover:bg-gray-50">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium" x-text="project.name">Community Water Project</h4>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                        <div class="bg-purple-500 h-2 rounded-full" :style="`width: ${project.progress}%`"></div>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span x-text="'Invested: ' + formatCurrency(project.budget * 0.65)">Invested: UGX 3,250,000</span>
                                        <span x-text="'Progress: ' + project.progress + '%'">Progress: 65%</span>
                                    </div>
                                    <div class="flex justify-between text-sm mt-2">
                                        <span class="text-gray-500">Expected ROI: <span class="text-green-600 font-medium" x-text="project.expected_roi + '%'">15.0%</span></span>
                                        <span class="text-gray-500">Actual ROI: <span class="text-blue-600 font-medium" x-text="project.roi + '%'">12.5%</span></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 10: TRANSACTIONS ==================== -->
                <section id="transactions" class="scroll-mt-20">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Transaction History</h2>
                        <p class="text-sm text-gray-500 mt-1">View all your financial transactions</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-4 md:p-6 border-b border-gray-100">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <select class="px-3 py-2 border rounded-lg text-sm">
                                        <option value="">All Types</option>
                                        <option value="deposit">Deposits</option>
                                        <option value="withdrawal">Withdrawals</option>
                                        <option value="loan">Loans</option>
                                        <option value="dividend">Dividends</option>
                                    </select>
                                    <select class="px-3 py-2 border rounded-lg text-sm">
                                        <option value="">All Status</option>
                                        <option value="completed">Completed</option>
                                        <option value="pending">Pending</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <button @click="toggleReverseOrder('transactions')"
                                            :class="reverseOrderState.transactions ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-2 rounded text-xs transition" title="Reverse Order">
                                        <i class="fas fa-sort-numeric-alt mr-1"></i>
                                        <span x-text="reverseOrderState.transactions ? 'Oldest First' : 'Newest First'"></span>
                                    </button>
                                    <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                        <i class="fas fa-download mr-2"></i>Export
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium">TXN-2024-001</td>
                                        <td class="px-4 py-3 text-sm">Dec 15, 2024</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Deposit</span></td>
                                        <td class="px-4 py-3 text-sm">Monthly savings deposit</td>
                                        <td class="px-4 py-3 text-sm font-medium text-green-600">+UGX 100,000</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium">TXN-2024-002</td>
                                        <td class="px-4 py-3 text-sm">Dec 10, 2024</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Dividend</span></td>
                                        <td class="px-4 py-3 text-sm">Q4 2023 dividend payment</td>
                                        <td class="px-4 py-3 text-sm font-medium text-green-600">+UGX 35,000</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium">TXN-2024-003</td>
                                        <td class="px-4 py-3 text-sm">Dec 5, 2024</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Investment</span></td>
                                        <td class="px-4 py-3 text-sm">Solar Power Initiative</td>
                                        <td class="px-4 py-3 text-sm font-medium text-red-600">-UGX 500,000</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium">TXN-2024-004</td>
                                        <td class="px-4 py-3 text-sm">Dec 1, 2024</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Withdrawal</span></td>
                                        <td class="px-4 py-3 text-sm">ATM withdrawal</td>
                                        <td class="px-4 py-3 text-sm font-medium text-red-600">-UGX 50,000</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium">TXN-2024-005</td>
                                        <td class="px-4 py-3 text-sm">Nov 28, 2024</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Loan</span></td>
                                        <td class="px-4 py-3 text-sm">Loan disbursement LN-2024-001</td>
                                        <td class="px-4 py-3 text-sm font-medium text-green-600">+UGX 500,000</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-sm text-gray-600">Showing 5 of 48 transactions</span>
                            <div class="flex items-center space-x-2">
                                <button class="px-3 py-1 border rounded-lg text-sm disabled:opacity-50 bg-gray-100">Previous</button>
                                <button class="px-3 py-1 border rounded-lg text-sm hover:bg-gray-50">Next</button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 11: DOCUMENTS ==================== -->
                <section id="documents" class="scroll-mt-20">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">My Documents</h2>
                        <p class="text-sm text-gray-500 mt-1">Access and manage your documents</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition cursor-pointer">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                                </div>
                                <span class="text-xs text-gray-500">Dec 15, 2024</span>
                            </div>
                            <h4 class="font-semibold text-gray-800">Membership Certificate</h4>
                            <p class="text-sm text-gray-500 mt-1">Official membership document</p>
                            <div class="flex items-center space-x-2 mt-4">
                                <button class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                                <button class="px-3 py-2 border rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition cursor-pointer">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-excel text-green-600 text-xl"></i>
                                </div>
                                <span class="text-xs text-gray-500">Dec 10, 2024</span>
                            </div>
                            <h4 class="font-semibold text-gray-800">Dividend Statement</h4>
                            <p class="text-sm text-gray-500 mt-1">Q4 2023 dividend details</p>
                            <div class="flex items-center space-x-2 mt-4">
                                <button class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                                <button class="px-3 py-2 border rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition cursor-pointer">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-word text-blue-600 text-xl"></i>
                                </div>
                                <span class="text-xs text-gray-500">Nov 28, 2024</span>
                            </div>
                            <h4 class="font-semibold text-gray-800">Loan Agreement</h4>
                            <p class="text-sm text-gray-500 mt-1">LN-2024-001 contract</p>
                            <div class="flex items-center space-x-2 mt-4">
                                <button class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                                <button class="px-3 py-2 border rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition cursor-pointer">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-invoice text-purple-600 text-xl"></i>
                                </div>
                                <span class="text-xs text-gray-500">Nov 15, 2024</span>
                            </div>
                            <h4 class="font-semibold text-gray-800">Transaction History</h4>
                            <p class="text-sm text-gray-500 mt-1">Complete transaction log</p>
                            <div class="flex items-center space-x-2 mt-4">
                                <button class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                                <button class="px-3 py-2 border rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition cursor-pointer">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-contract text-orange-600 text-xl"></i>
                                </div>
                                <span class="text-xs text-gray-500">Oct 20, 2024</span>
                            </div>
                            <h4 class="font-semibold text-gray-800">Investment Contract</h4>
                            <p class="text-sm text-gray-500 mt-1">Solar Power Initiative</p>
                            <div class="flex items-center space-x-2 mt-4">
                                <button class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                                <button class="px-3 py-2 border rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-2 border-dashed border-gray-300 hover:border-purple-500 transition cursor-pointer">
                            <div class="flex flex-col items-center justify-center h-full py-8">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-plus text-gray-400 text-xl"></i>
                                </div>
                                <h4 class="font-semibold text-gray-600">Upload Document</h4>
                                <p class="text-sm text-gray-500 mt-1">PDF, DOC, JPG up to 10MB</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 12: NOTIFICATIONS ==================== -->
                <section id="notifications" class="scroll-mt-20">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Notifications</h2>
                        <p class="text-sm text-gray-500 mt-1">Stay updated with your activities</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                                <span class="text-sm font-medium">3 unread notifications</span>
                            </div>
                            <button class="text-sm text-purple-600 hover:underline">Mark all as read</button>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <div class="p-4 hover:bg-gray-50 transition cursor-pointer bg-blue-50">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-coins text-blue-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-medium text-gray-800">Dividend Payment Received</h4>
                                            <span class="text-xs text-gray-500">2 hours ago</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Your Q4 2023 dividend of UGX 35,000 has been credited to your account.</p>
                                        <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">New</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 hover:bg-gray-50 transition cursor-pointer bg-blue-50">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-medium text-gray-800">Loan Approved</h4>
                                            <span class="text-xs text-gray-500">5 hours ago</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Your loan application LN-2024-001 has been approved for UGX 500,000.</p>
                                        <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">New</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 hover:bg-gray-50 transition cursor-pointer bg-blue-50">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-rocket text-purple-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-medium text-gray-800">New Investment Opportunity</h4>
                                            <span class="text-xs text-gray-500">1 day ago</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">A new investment opportunity "Agricultural Expansion" is now available.</p>
                                        <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">New</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 hover:bg-gray-50 transition cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-bell text-gray-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-medium text-gray-800">Meeting Reminder</h4>
                                            <span class="text-xs text-gray-500">2 days ago</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Reminder: Shareholder meeting scheduled for December 20, 2024 at 10:00 AM.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 hover:bg-gray-50 transition cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-chart-line text-gray-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-medium text-gray-800">Portfolio Update</h4>
                                            <span class="text-xs text-gray-500">3 days ago</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Your portfolio value has increased by 2.5% this week.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-t border-gray-100 text-center">
                            <button class="text-sm text-purple-600 hover:underline">View All Notifications</button>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 13: SETTINGS ==================== -->
                <section id="settings" class="scroll-mt-20 pb-8">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Settings</h2>
                        <p class="text-sm text-gray-500 mt-1">Manage your account preferences</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-800">Account Settings</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Profile Information</h4>
                                        <p class="text-sm text-gray-500">Update your personal details</p>
                                    </div>
                                </div>
                                <button class="px-4 py-2 border rounded-lg hover:bg-gray-50 text-sm">Edit</button>
                            </div>
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-lock text-green-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Password & Security</h4>
                                        <p class="text-sm text-gray-500">Change password and security settings</p>
                                    </div>
                                </div>
                                <button class="px-4 py-2 border rounded-lg hover:bg-gray-50 text-sm">Update</button>
                            </div>
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                        <i class="fas fa-bell text-purple-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Notification Preferences</h4>
                                        <p class="text-sm text-gray-500">Manage how you receive notifications</p>
                                    </div>
                                </div>
                                <button class="px-4 py-2 border rounded-lg hover:bg-gray-50 text-sm">Configure</button>
                            </div>
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                                        <i class="fas fa-envelope text-orange-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Email Preferences</h4>
                                        <p class="text-sm text-gray-500">Control email communications</p>
                                    </div>
                                </div>
                                <button class="px-4 py-2 border rounded-lg hover:bg-gray-50 text-sm">Manage</button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-800">Preferences</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-cyan-100 flex items-center justify-center">
                                        <i class="fas fa-language text-cyan-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Language</h4>
                                        <p class="text-sm text-gray-500">Select your preferred language</p>
                                    </div>
                                </div>
                                <select class="px-3 py-2 border rounded-lg text-sm">
                                    <option>English</option>
                                    <option>Luganda</option>
                                    <option>Swahili</option>
                                </select>
                            </div>
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center">
                                        <i class="fas fa-moon text-pink-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Dark Mode</h4>
                                        <p class="text-sm text-gray-500">Toggle dark mode appearance</p>
                                    </div>
                                </div>
                                <button class="px-4 py-2 bg-gray-200 rounded-lg text-sm">
                                    <i class="fas fa-sun mr-1"></i>Light
                                </button>
                            </div>
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-money-bill-wave text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Currency Display</h4>
                                        <p class="text-sm text-gray-500">Choose currency format</p>
                                    </div>
                                </div>
                                <select class="px-3 py-2 border rounded-lg text-sm">
                                    <option>UGX (Uganda Shilling)</option>
                                    <option>USD (US Dollar)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-red-600">Danger Zone</h3>
                        </div>
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <i class="fas fa-trash text-red-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Delete Account</h4>
                                    <p class="text-sm text-gray-500">Permanently delete your account and data</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">Delete</button>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>

    <!-- ==================== MODALS ==================== -->

    <!-- Loan Request Modal -->
    <div x-show="showLoanRequestModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Request Loan</h3>
            <form @submit.prevent="submitLoanRequest()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loan Amount (UGX)</label>
                        <input type="number" x-model="loanRequest.amount" placeholder="Enter amount"
                               class="w-full p-3 border rounded" required min="1000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
                        <textarea x-model="loanRequest.purpose" placeholder="Reason for loan"
                                  class="w-full p-3 border rounded" rows="3" required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Repayment Period (Months)</label>
                        <select x-model="loanRequest.repayment_months" class="w-full p-3 border rounded" required>
                            <option value="">Select period</option>
                            <option value="0.5">2 Weeks</option>
                            <option value="1">1 Month</option>
                            <option value="3">3 Months</option>
                            <option value="6">6 Months</option>
                            <option value="12">12 Months</option>
                            <option value="24">24 Months</option>
                            <option value="36">36 Months</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showLoanRequestModal = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Chat Modal -->
    <div x-show="showChatModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg w-full max-w-md h-[600px] flex flex-col">
            <div class="flex justify-between items-center p-4 border-b bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-t-lg">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center">
                        <i class="fas fa-headset text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Support Team</h3>
                        <p class="text-xs text-purple-100"><i class="fas fa-circle text-green-400 text-[8px]"></i> Online</p>
                    </div>
                </div>
                <button @click="showChatModal = false" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="flex-1 p-4 overflow-y-auto bg-gray-50" id="chatMessages">
                <div class="space-y-3">
                    <template x-for="(msg, index) in chatMessages" :key="index">
                        <div :class="msg.sender === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="msg.sender === 'user' ? 'bg-purple-600 text-white' : 'bg-white text-gray-800'"
                                 class="p-3 rounded-lg shadow-sm max-w-xs">
                                <p class="text-sm" x-text="msg.text"></p>
                                <span class="text-xs opacity-75" x-text="msg.time"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="p-4 border-t bg-white rounded-b-lg">
                <div class="flex space-x-2 mb-2">
                    <button @click="sendQuickMessage('I need help with my loan')" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-full text-xs">Loan Help</button>
                    <button @click="sendQuickMessage('Check my dividends')" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-full text-xs">Dividends</button>
                    <button @click="sendQuickMessage('Investment inquiry')" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-full text-xs">Invest</button>
                    <button @click="showMemberChatModal = true; showChatModal = false" class="px-3 py-1 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-full text-xs">
                        <i class="fas fa-users mr-1"></i>Members
                    </button>
                </div>
                <div class="flex space-x-2">
                    <input type="text" x-model="chatInput" @keyup.enter="sendMessage" placeholder="Type a message..."
                           class="flex-1 p-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <button @click="sendMessage" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Details Modal -->
    <div x-show="showPerformanceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800">Portfolio Performance Analysis</h3>
                <button @click="showPerformanceModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-sm text-gray-600">Current Performance</p>
                        <p class="text-3xl font-bold text-green-600">+12.8%</p>
                        <p class="text-xs text-gray-500 mt-1">Year to date</p>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-600">vs Market Benchmark</p>
                        <p class="text-3xl font-bold text-blue-600">+3.2%</p>
                        <p class="text-xs text-gray-500 mt-1">Outperforming</p>
                    </div>
                </div>
                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Performance Breakdown</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <span class="text-sm">Water Projects</span>
                            <span class="text-sm font-bold text-green-600">+15.2%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <span class="text-sm">Education Programs</span>
                            <span class="text-sm font-bold text-green-600">+8.5%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <span class="text-sm">Healthcare Initiatives</span>
                            <span class="text-sm font-bold text-green-600">+18.3%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dividend Announcement Modal -->
    <div x-show="showDividendModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800">Dividend Announcements</h3>
                <button @click="showDividendModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-bullhorn text-blue-600 mr-2"></i>
                        <span class="font-semibold text-blue-900">Q1 2024 Dividend Announcement</span>
                    </div>
                    <p class="text-sm text-gray-700 mb-2">The Board of Directors has declared a dividend payment for Q1 2024.</p>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <p class="text-xs text-gray-600">Dividend Rate</p>
                            <p class="text-lg font-bold text-blue-600">10.5%</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Payment Date</p>
                            <p class="text-lg font-bold text-blue-600">March 15, 2024</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Your Estimated Amount</p>
                            <p class="text-lg font-bold text-green-600">UGX 135,000</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Eligible Shares</p>
                            <p class="text-lg font-bold text-purple-600">1,250</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Previous Dividends</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <div>
                                <p class="text-sm font-medium">Q4 2023</p>
                                <p class="text-xs text-gray-500">Paid: Dec 15, 2023</p>
                            </div>
                            <span class="text-sm font-bold text-green-600">UGX 125,000</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <div>
                                <p class="text-sm font-medium">Q3 2023</p>
                                <p class="text-xs text-gray-500">Paid: Sep 15, 2023</p>
                            </div>
                            <span class="text-sm font-bold text-green-600">UGX 118,000</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Opportunities Modal -->
    <div x-show="showOpportunitiesModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800">Investment Opportunities</h3>
                <button @click="showOpportunitiesModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="space-y-4" x-data="{ opportunities: [] }" x-init="initOpportunitiesData()">
                    <template x-for="opp in opportunities" :key="opp.id || opp.title">
                        <div class="border rounded-lg p-5 hover:shadow-lg transition">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800" x-text="opp.title">Renewable Energy Project</h4>
                                    <p class="text-sm text-gray-600" x-text="opp.description">Solar power installation for rural communities</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-medium rounded-full"
                                      :class="opp.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'"
                                      x-text="opp.status === 'active' ? 'Active' : 'Upcoming'">Active</span>
                            </div>
                            <div class="grid grid-cols-4 gap-4 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500">Target Amount</p>
                                    <p class="text-sm font-bold" x-text="'UGX ' + (opp.target_amount / 1000000).toFixed(0) + 'M'">UGX 50M</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Min. Investment</p>
                                    <p class="text-sm font-bold" x-text="'UGX ' + (opp.minimum_investment / 1000).toFixed(0) + 'K'">UGX 500K</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Expected ROI</p>
                                    <p class="text-sm font-bold text-green-600" x-text="opp.expected_roi + '%'">18.5%</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Risk Level</p>
                                    <p class="text-sm font-bold"
                                       :class="opp.risk_level === 'low' ? 'text-green-600' : opp.risk_level === 'medium' ? 'text-yellow-600' : 'text-orange-600'"
                                       x-text="opp.risk_level.charAt(0).toUpperCase() + opp.risk_level.slice(1)">Medium</p>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500"
                                      x-text="opp.status === 'active' ? 'Deadline: ' + new Date(opp.deadline).toLocaleDateString() : 'Launch: ' + new Date(opp.launch_date).toLocaleDateString()">
                                    Deadline: March 30, 2024
                                </span>
                                <button class="px-4 py-2 text-white rounded-lg hover:opacity-90 text-sm"
                                        :class="opp.status === 'active' ? 'bg-purple-600 hover:bg-purple-700' : 'bg-gray-300 cursor-not-allowed'"
                                        :disabled="opp.status !== 'active'">
                                    <i class="fas fa-hand-holding-usd mr-1"></i>
                                    <span x-text="opp.status === 'active' ? 'Invest Now' : 'Coming Soon'">Invest Now</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Picture Upload Modal -->
    <div x-show="showProfileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800">Update Profile Picture</h3>
                <button @click="showProfileModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="flex flex-col items-center mb-6">
                    <div class="w-32 h-32 rounded-full bg-blue-100 flex items-center justify-center mb-4 overflow-hidden">
                        <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover">
                        <i x-show="!profilePicture" class="fas fa-user text-blue-600 text-5xl"></i>
                    </div>
                    <input type="file" id="profilePictureInput" accept="image/*" class="hidden" @change="handleProfilePictureUpload">
                    <button @click="document.getElementById('profilePictureInput').click()"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fas fa-upload mr-2"></i>Choose Picture
                    </button>
                    <p class="text-xs text-gray-500 mt-2">JPG, PNG, GIF (Max 2MB)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile View Modal -->
    <div x-show="showProfileViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800">Profile</h3>
                <button @click="showProfileViewModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="flex flex-col items-center">
                    <div class="w-48 h-48 rounded-full bg-blue-100 flex items-center justify-center mb-4 overflow-hidden">
                        <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover">
                        <i x-show="!profilePicture" class="fas fa-user text-blue-600 text-8xl"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-800 mb-1">John Doe</h4>
                    <p class="text-sm text-gray-600 mb-1">Shareholder</p>
                    <p class="text-xs text-gray-500">Member ID: BSS001</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Chat Modal -->
    <div x-show="showMemberChatModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-2 sm:p-4"
         style="display: none;" x-cloak>
        <div class="bg-white rounded-2xl w-full h-full sm:h-[85vh] sm:max-h-[90vh] flex shadow-2xl overflow-hidden border border-gray-200 sm:rounded-2xl">

            <!-- Left Panel: Members List -->
            <div class="w-full sm:w-72 md:w-80 flex-shrink-0 flex flex-col bg-gradient-to-b from-gray-50 to-white border-r border-gray-200"
                 :class="{'hidden': selectedMemberChat && window.innerWidth < 640}">
                <div class="p-5 bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-users text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold">Messages</h3>
                                <p class="text-xs text-violet-200" x-text="filteredMembersChat.length + ' contacts'"></p>
                            </div>
                        </div>
                        <button @click="showMemberChatModal = false; showChatModal = true" class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-full transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <!-- Search Bar -->
                    <div class="relative">
                        <input type="text" x-model="memberChatSearch" @input="filterMembersForChat()"
                               placeholder="Search conversations..."
                               class="w-full px-4 py-3 pl-11 rounded-xl bg-white/20 backdrop-blur-sm text-white placeholder-white/70 text-sm focus:outline-none focus:ring-2 focus:ring-white/50 transition">
                        <i class="fas fa-search absolute left-4 top-3.5 text-white/70"></i>
                    </div>
                    <!-- Quick Filters -->
                    <div class="flex space-x-2 mt-3 overflow-x-auto pb-1">
                        <button @click="chatFilter = 'all'" :class="chatFilter === 'all' ? 'bg-white text-violet-600' : 'bg-white/20 text-white'"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition">All</button>
                        <button @click="chatFilter = 'online'" :class="chatFilter === 'online' ? 'bg-white text-violet-600' : 'bg-white/20 text-white'"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition">Online</button>
                        <button @click="chatFilter = 'unread'" :class="chatFilter === 'unread' ? 'bg-white text-violet-600' : 'bg-white/20 text-white'"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition">Unread</button>
                    </div>
                </div>

                <!-- Members List -->
                <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300">
                    <div class="p-2">
                        <p class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Contacts</p>
                        <template x-for="member in filteredMembersChat" :key="member.member_id">
                            <div @click="selectMemberChat(member)"
                                 :class="selectedMemberChat?.member_id === member.member_id ? 'bg-violet-100 border-l-4 border-violet-600' : 'hover:bg-gray-100'"
                                 class="p-3 mb-1 rounded-xl cursor-pointer transition-all duration-200 border-l-4 border-transparent">
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-violet-400 to-indigo-500 flex items-center justify-center text-white font-bold shadow-lg">
                                            <span x-text="member.full_name?.charAt(0) || 'U'"></span>
                                        </div>
                                        <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start">
                                            <p class="text-sm font-semibold text-gray-800 truncate" x-text="member.full_name"></p>
                                            <span class="text-xs text-gray-400" x-text="'12:30 PM'"></span>
                                        </div>
                                        <div class="flex items-center justify-between mt-1">
                                            <p class="text-xs text-gray-500 truncate" x-text="member.role"></p>
                                            <span x-show="member.unread" class="bg-violet-600 text-white text-xs rounded-full px-2 py-0.5 font-medium shadow-sm">3</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="filteredMembersChat.length === 0" class="text-center py-8">
                            <i class="fas fa-user-friends text-4xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">No contacts found</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Chat Area -->
            <div class="flex-1 flex flex-col bg-gradient-to-br from-gray-50 to-gray-100"
                 :class="{'w-full': window.innerWidth < 640, 'hidden': !selectedMemberChat && window.innerWidth < 640}">

                <!-- Chat Header -->
                <div class="p-4 bg-white border-b flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <button @click="selectedMemberChat = null" class="sm:hidden p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-full transition">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-violet-400 to-indigo-500 flex items-center justify-center text-white font-bold shadow-lg">
                                <span x-text="selectedMemberChat?.full_name?.charAt(0) || 'U'"></span>
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800" x-text="selectedMemberChat?.full_name || ''"></h3>
                            <p class="text-xs text-green-600 flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                Active now
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="startVideoCall()" class="p-3 bg-violet-100 hover:bg-violet-200 text-violet-600 rounded-full transition" title="Video Call">
                            <i class="fas fa-video"></i>
                        </button>
                        <button @click="startVoiceCall()" class="p-3 bg-violet-100 hover:bg-violet-200 text-violet-600 rounded-full transition" title="Voice Call">
                            <i class="fas fa-phone"></i>
                        </button>
                    </div>
                </div>

                <!-- Messages Area -->
                <div x-show="selectedMemberChat" class="flex-1 p-2 sm:p-6 overflow-y-auto bg-gradient-to-br from-violet-50/50 to-indigo-50/50" id="memberChatMessages">
                    <div class="flex justify-center mb-6">
                        <span class="bg-white/80 backdrop-blur-sm px-4 py-1.5 rounded-full text-xs text-gray-600 shadow-sm font-medium">Today</span>
                    </div>
                    <div class="space-y-4">
                        <template x-for="(msg, index) in memberChatMessages" :key="index">
                            <div :class="msg.sender === 'me' ? 'flex justify-end' : 'flex justify-start'" class="animate-fade-in">
                                <div class="max-w-md">
                                    <div :class="msg.sender === 'me' ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-2xl rounded-tr-sm' : 'bg-white text-gray-800 rounded-2xl rounded-tl-sm shadow-lg'"
                                         class="p-4 relative">
                                        <p class="text-sm leading-relaxed" x-text="msg.text"></p>
                                        <div class="flex items-center justify-end space-x-2 mt-2">
                                            <span class="text-[10px] opacity-70" x-text="msg.time"></span>
                                            <template x-if="msg.sender === 'me'">
                                                <i class="fas fa-check-double text-blue-200 text-[10px]"></i>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="isTyping" class="flex justify-start animate-fade-in">
                            <div class="bg-white rounded-2xl rounded-tl-sm shadow-lg p-4">
                                <div class="flex space-x-2">
                                    <div class="w-2.5 h-2.5 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2.5 h-2.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2.5 h-2.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div x-show="!selectedMemberChat" class="flex-1 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 p-4">
                    <div class="text-center max-w-sm">
                        <div class="w-16 h-16 sm:w-24 sm:h-24 mx-auto mb-4 bg-gradient-to-br from-violet-100 to-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-comments text-2xl sm:text-4xl text-violet-400"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">BSS Investment Chat</h3>
                        <p class="text-gray-500 text-xs sm:text-sm">Select a member from the list to start messaging</p>
                    </div>
                </div>

                <!-- Input Area -->
                <div x-show="selectedMemberChat" class="p-2 sm:p-4 bg-white border-t">
                    <div class="flex items-end space-x-2 sm:space-x-3">
                        <input type="text" x-model="memberChatInput" @keydown.enter.prevent="sendMemberMessage"
                               placeholder="Type a message..." rows="1"
                               class="flex-1 px-5 py-3 border-2 border-gray-200 rounded-2xl text-sm focus:outline-none focus:border-violet-500 focus:ring-2 focus:ring-violet-200 resize-none transition-all"
                               style="max-height: 120px;">
                        <button @click="sendMemberMessage"
                                class="p-3 bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-full hover:from-violet-700 hover:to-indigo-700 transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== SCRIPTS ==================== -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

    <!-- External Dashboard JavaScript -->
    <script src="{{ asset('js/shareholder-dashboard.js') }}"></script>
</body>
</html>
