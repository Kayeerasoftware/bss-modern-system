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
        .scroll-mt-20 { scroll-margin-top: 5rem; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Search Styles */
        .nav-item.search-item {
            display: flex;
            align-items: center;
            space-x: 0.5rem;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .nav-item.search-item:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

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

        .sidebar-toggle {
            position: fixed;
            top: calc(3rem + 0.25rem);
            left: calc(144px - 10px);
            width: 20px;
            height: 20px;
            background: white;
            border: 1px solid rgba(239, 68, 68, 0.5);
            border-radius: 50%;
            color: #ef4444;
            cursor: pointer;
            z-index: 1200;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            font-size: 0.6rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .sidebar-toggle:hover {
            background: #ef4444;
            color: white;
            transform: scale(1.1);
        }

        .sidebar-toggle i {
            transition: transform 0.3s;
            transform: rotate(180deg);
        }

        aside.sidebar.collapsed ~ .sidebar-toggle {
            left: calc(64px - 10px);
        }

        aside.sidebar.collapsed ~ .sidebar-toggle i {
            transform: rotate(0deg);
        }

        aside.sidebar {
            position: fixed;
            top: 3rem;
            left: 0;
            height: calc(100vh - 3rem);
            width: 144px;
            transition: width 0.3s;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 999;
        }

        aside.sidebar.collapsed {
            width: 64px;
        }

        aside.sidebar.collapsed .user-info,
        aside.sidebar.collapsed .nav-item span,
        aside.sidebar.collapsed .search-item input,
        aside.sidebar.collapsed .clear-search,
        aside.sidebar.collapsed .nav-badge,
        aside.sidebar.collapsed .nav-count,
        aside.sidebar.collapsed p {
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        aside.sidebar:not(.collapsed) .nav-item span,
        aside.sidebar:not(.collapsed) .search-item input,
        aside.sidebar:not(.collapsed) p {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.5s ease 0.3s, transform 0.5s ease 0.3s;
        }

        aside.sidebar.collapsed .sidebar-user {
            padding: 0.5rem;
        }

        aside.sidebar.collapsed .user-avatar {
            width: 40px;
            height: 40px;
            margin: 0 auto;
        }

        aside.sidebar.collapsed .user-avatar i {
            font-size: 1.25rem;
        }

        aside.sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 0.6rem;
        }

        aside.sidebar.collapsed .nav-item i {
            font-size: 1rem;
            margin: 0;
        }

        aside.sidebar.collapsed .search-item {
            justify-content: center;
            padding: 0.6rem;
            cursor: pointer;
        }

        .main-content {
            margin-left: 144px;
            padding-top: 3rem;
            transition: margin-left 0.3s;
        }
        
        .main-content.sidebar-collapsed {
            margin-left: 64px;
        }
        
        /* Consistent spacing for all sections */
        section {
            padding-top: 0.25rem !important;
        }
        
        section .mb-6 {
            margin-bottom: 1rem !important;
        }
        
        @media (max-width: 1023px) {
            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 1023px) {
            .sidebar-toggle {
                display: none !important;
            }
        }

        @media (max-width: 767px) {
            .sidebar-toggle {
                display: none !important;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/sidebar-toggle.css') }}">
    <script src="{{ asset('js/shareholder-dashboard.js') }}"></script>
    <script src="{{ asset('js/footer-optimizer.js') }}"></script>
    <script>
        function alignWithNavbar() {
            const navbar = document.querySelector('.topnav');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const toggleBtn = document.querySelector('.sidebar-toggle');
            
            if (navbar) {
                const navHeight = navbar.offsetHeight;
                
                if (sidebar) {
                    sidebar.style.top = navHeight + 'px';
                    sidebar.style.height = `calc(100vh - ${navHeight}px)`;
                }
                
                if (mainContent) {
                    mainContent.style.paddingTop = navHeight + 'px';
                }
                
                if (toggleBtn) {
                    toggleBtn.style.top = (navHeight + 4) + 'px';
                }
            }
        }
        
        function forceScrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            alignWithNavbar();
            
            // Force scroll to top when sidebar links are clicked
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.addEventListener('click', forceScrollToTop);
            });
        });
        
        window.addEventListener('resize', alignWithNavbar);
    </script>
</head>

<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen transition-colors duration-300 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900" x-data="{activeLink: 'overview', sidebarOpen: false, sidebarCollapsed: false, showLoanRequestModal: false, showChatModal: false, showPerformanceModal: false, showDividendModal: false, showOpportunitiesModal: false, showProfileModal: false, showProfileViewModal: false, showLogoModal: false, showShareholderModal: false, showCalendarModal: false, showProfileDropdown: false, showMemberChatModal: false, darkMode: localStorage.getItem('darkMode') === 'true', portfolioData: {shares: 1250, dividends: 125000, roi: 12.5, totalValue: 2450000, sharePrice: 1800}, myFinancials: {savings: 1500000, loan: 500000, balance: 1000000}, myLoans: [], chatMessages: [{sender: 'support', text: 'Hello! How can I help you today?', time: new Date().toLocaleTimeString()}], chatInput: '', loanRequest: {amount: '', purpose: '', repayment_months: ''}, investmentProjects: [{id: 1, name: 'Community Water Project', progress: 65, budget: 5000000, expected_roi: 15.0, roi: 12.5}, {id: 2, name: 'Solar Power Initiative', progress: 45, budget: 8000000, expected_roi: 18.5, roi: 16.2}], dividendHistory: [{id: 1, period: 'Q4 2023', amount: 125000, rate: 10, status: 'paid'}], formatCurrency(amount) { return 'UGX ' + (amount || 0).toLocaleString(); }, toggleDarkMode() { this.darkMode = !this.darkMode; localStorage.setItem('darkMode', this.darkMode); document.documentElement.classList.toggle('dark', this.darkMode); }, submitLoanRequest() { if (!this.loanRequest.amount || !this.loanRequest.purpose || !this.loanRequest.repayment_months) { alert('Please fill in all fields'); return; } const newLoan = {id: Date.now(), loan_id: 'LN-2024-' + String(this.myLoans.length + 1).padStart(3, '0'), amount: parseInt(this.loanRequest.amount), purpose: this.loanRequest.purpose, repayment_months: parseFloat(this.loanRequest.repayment_months), status: 'pending', created_at: new Date().toISOString(), monthly_payment: Math.round(parseInt(this.loanRequest.amount) * 1.1 / parseFloat(this.loanRequest.repayment_months))}; this.myLoans.push(newLoan); this.showLoanRequestModal = false; this.loanRequest = {amount: '', purpose: '', repayment_months: ''}; alert('Loan request submitted successfully!'); }, sendMessage() { if (!this.chatInput.trim()) return; this.chatMessages.push({sender: 'user', text: this.chatInput, time: new Date().toLocaleTimeString()}); this.chatInput = ''; setTimeout(() => { this.chatMessages.push({sender: 'support', text: 'Thank you for your message. Our team will get back to you shortly.', time: new Date().toLocaleTimeString()}); }, 1000); }, sendQuickMessage(message) { this.chatMessages.push({sender: 'user', text: message, time: new Date().toLocaleTimeString()}); setTimeout(() => { this.chatMessages.push({sender: 'support', text: 'I understand you need help with that. Let me connect you with the right specialist.', time: new Date().toLocaleTimeString()}); }, 1000); }}" x-init="document.documentElement.classList.toggle('dark', darkMode)">

    <!-- ==================== TOP NAVIGATION ==================== -->
    <nav class="topnav fixed top-0 left-0 right-0 h-12 bg-gradient-to-r from-violet-200 via-blue-200 to-cyan-200 dark:from-gray-800 dark:via-gray-700 dark:to-gray-800 backdrop-blur-xl border-b border-violet-300 dark:border-gray-600 z-[1000] shadow-2xl ring-2 ring-gradient-to-r ring-from-violet-400 ring-to-cyan-400 ring-opacity-80 shadow-violet-500/30 dark:shadow-gray-900/30 transition-colors duration-300">
        <div class="nav-container flex items-center justify-between h-full px-6 max-w-full mx-auto">
            <div class="nav-left flex items-center gap-2">
                <button @click="sidebarOpen = !sidebarOpen" class="menu-toggle lg:hidden bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 text-base text-blue-600 cursor-pointer px-2 py-1.5 rounded-lg transition-all duration-300 hover:from-blue-100 hover:to-purple-100 hover:-translate-y-0.5 hover:shadow-md">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo flex items-center gap-2 font-bold text-sm tracking-tight px-3 py-1.5 bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl border border-blue-200 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:border-blue-300 cursor-pointer" @click="showLogoModal = !showLogoModal; console.log('Logo clicked, showLogoModal:', showLogoModal)" :class="showLogoModal ? 'ring-2 ring-blue-400' : ''">
                    <img src="{{ asset('bunya logo.jpg') }}" alt="BSS Logo" class="logo-img h-6 w-auto object-contain drop-shadow-md transition-all duration-300">
                    <h2 class="m-0 bg-gradient-to-br from-blue-600 to-purple-600 bg-clip-text text-transparent text-xs font-bold hidden md:block">BSS OB/OG INVESTMENT GROUP SYSTEM</h2>
                </div>
                <button @click="showChatModal = true" class="nav-btn relative flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 px-3 py-1.5 rounded-xl cursor-pointer transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:border-blue-300 group" title="Get Support">
                    <i class="fas fa-headset text-blue-600 drop-shadow-md"></i>
                    <span class="bg-gradient-to-br from-blue-600 to-purple-600 bg-clip-text text-transparent text-xs font-bold font-mono ml-2 hidden lg:inline">Get Support</span>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[0.6rem] font-bold px-1.5 py-0.5 rounded-full animate-pulse">Live</span>
                </button>
            </div>

            <div class="nav-center flex-1 flex justify-center">
                <div class="flex items-center gap-2 font-bold text-sm tracking-tight px-8 py-1.5 rounded-xl border transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md cursor-pointer hidden lg:flex" style="background: linear-gradient(135deg, #dcfce7, #bbf7d0); border-color: #22c55e;" @click="showShareholderModal = !showShareholderModal" :class="showShareholderModal ? 'ring-2 ring-green-400' : ''">
                    <div class="bg-white/20 p-1 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-chart-line text-sm drop-shadow-md" style="color: #16a34a;"></i>
                    </div>
                    <span class="text-sm font-bold whitespace-nowrap" style="background: linear-gradient(135deg, #16a34a, #22c55e); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Shareholder Dashboard</span>
                </div>
                <button class="nav-btn relative flex items-center justify-center rounded-xl border transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md px-3 py-1.5 lg:hidden cursor-pointer" style="background: linear-gradient(135deg, #dcfce7, #bbf7d0); border-color: #22c55e;" title="Shareholder Dashboard" @click="showShareholderModal = !showShareholderModal" :class="showShareholderModal ? 'ring-2 ring-green-400' : ''">
                    <i class="fas fa-chart-line drop-shadow-md" style="color: #16a34a;"></i>
                </button>
            </div>
      {{-- calender model       --}}
            <div class="nav-right flex items-center gap-2">
                <button @click="showCalendarModal = true" class="nav-btn relative flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 px-3 py-1.5 rounded-xl cursor-pointer transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:border-blue-300" title="Calendar" x-data="{ currentTime: new Date() }" x-init="setInterval(() => { currentTime = new Date() }, 1000)">
                    <i class="fas fa-calendar-alt text-blue-600 drop-shadow-md"></i>
                    <span class="bg-gradient-to-br from-blue-600 to-purple-600 bg-clip-text text-transparent text-xs font-bold font-mono ml-2 hidden md:inline" x-text="currentTime.toLocaleDateString() + ' ' + currentTime.toLocaleTimeString()"></span>
                </button>
                <button @click="activeLink = 'notifications'; document.getElementById('notifications').scrollIntoView({ behavior: 'smooth' })" class="nav-btn relative flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 px-3 py-1.5 rounded-xl cursor-pointer transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:border-blue-300" title="Notifications">
                    <i class="fas fa-bell text-blue-600 drop-shadow-md"></i>
                    <span class="badge absolute -top-2 right-0 bg-gradient-to-br from-red-500 to-red-600 text-white text-[0.625rem] font-bold px-1.5 py-0.5 rounded-xl min-w-[1.125rem] text-center shadow-md border-2 border-white animate-pulse">3</span>
                </button>
                <button @click="showMemberChatModal = true" class="nav-btn relative flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 px-3 py-1.5 rounded-xl cursor-pointer transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:border-blue-300" title="Messages">
                    <i class="fas fa-envelope text-blue-600 drop-shadow-md"></i>
                    <span class="status-dot absolute -top-1 right-1.5 w-2.5 h-2.5 bg-gradient-to-br from-green-500 to-green-600 rounded-full border-2 border-white shadow-md animate-pulse"></span>
                </button>
                <div class="profile relative">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl cursor-pointer transition-all duration-300 bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 hover:-translate-y-0.5 hover:shadow-md hover:border-blue-300" @click="showProfileDropdown = !showProfileDropdown">
                        <span class="bg-gradient-to-br from-blue-600 to-purple-600 bg-clip-text text-transparent text-xs font-bold tracking-tight hidden lg:inline">Welcome back, John Doe</span>
                        <i class="fas fa-chevron-down dropdown-arrow transition-all duration-300 text-blue-600 text-xs drop-shadow-md hidden lg:inline" :class="showProfileDropdown ? 'rotate-180' : ''"></i>
                        <div class="profile-avatar relative">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" alt="Profile" class="w-6 h-6 rounded-full object-cover border-2 border-green-500 shadow-md transition-all duration-300">
                            <div class="status-indicator online absolute bottom-0 right-0 w-2 h-2 rounded-full border-2 border-white bg-gradient-to-br from-green-500 to-green-600 shadow-md animate-pulse"></div>
                        </div>
                    </div>
                    <div x-show="showProfileDropdown"
                         @click.away="showProfileDropdown = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="dropdown-menu absolute top-full right-0 mt-3 w-48 bg-white border border-blue-200 rounded-lg shadow-xl z-[1000] overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 text-center">
                            <div class="w-12 h-12 rounded-full bg-gray-300 mx-auto mb-2 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=48&h=48&fit=crop&crop=face" alt="Profile" class="w-full h-full object-cover">
                            </div>
                            <p class="text-sm font-semibold text-gray-800">John Doe</p>
                            <p class="text-xs text-gray-500">Shareholder</p>
                        </div>
                        <a href="#" class="dropdown-item flex items-center gap-2 px-3 py-2 text-gray-800 no-underline transition-all duration-200 bg-transparent border-0 w-full text-left cursor-pointer text-xs font-medium relative hover:bg-gradient-to-br hover:from-blue-50 hover:to-purple-50 hover:text-blue-600">
                            <i class="fas fa-user text-blue-600 text-xs w-3 text-center"></i> Profile
                        </a>
                        <a href="#" class="dropdown-item flex items-center gap-2 px-3 py-2 text-gray-800 no-underline transition-all duration-200 bg-transparent border-0 w-full text-left cursor-pointer text-xs font-medium relative hover:bg-gradient-to-br hover:from-blue-50 hover:to-purple-50 hover:text-blue-600">
                            <i class="fas fa-cog text-blue-600 text-xs w-3 text-center"></i> Settings
                        </a>
                        <div class="dropdown-divider h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent my-1"></div>
                        <form action="http://127.0.0.1:8000/" method="GET" class="m-0">
                            <button type="submit" class="dropdown-item flex items-center gap-2 px-3 py-2 text-gray-800 no-underline transition-all duration-200 bg-transparent border-0 w-full text-left cursor-pointer text-xs font-medium relative hover:bg-gradient-to-br hover:from-blue-50 hover:to-purple-50 hover:text-blue-600">
                                <i class="fas fa-sign-out-alt text-blue-600 text-xs w-3 text-center"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Logo Dropdown -->
    <div x-show="showLogoModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" @click.away="showLogoModal = false" class="fixed top-12 left-0 right-0 bg-white border-b border-gray-200 shadow-xl z-[1001] p-6">
        <div class="max-w-md mx-auto text-center">
            <img src="{{ asset('bunya logo.jpg') }}" alt="BSS Logo" class="h-16 w-auto object-contain mx-auto mb-3 drop-shadow-lg">
            <h2 class="text-lg font-bold bg-gradient-to-br from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">BSS OB/OG INVESTMENT GROUP SYSTEM</h2>
            <p class="text-sm text-gray-600 mb-1">Your trusted investment partner</p>
            <p class="text-xs text-gray-400">Building wealth together since 2020</p>
        </div>
    </div>

    <!-- Shareholder Dropdown -->
    <div x-show="showShareholderModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" @click.away="showShareholderModal = false" class="fixed top-12 left-0 right-0 bg-white border-b border-gray-200 shadow-xl z-[1001] p-6">
        <div class="max-w-md mx-auto text-center">
            <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-line text-white text-2xl"></i>
            </div>
            <h2 class="text-lg font-bold bg-gradient-to-br from-green-600 to-emerald-600 bg-clip-text text-transparent mb-2">Shareholder Dashboard</h2>
            <p class="text-sm text-gray-600 mb-1">Investment portfolio management</p>
            <p class="text-xs text-gray-400">Track your shares, dividends & returns</p>
        </div>
    </div>
    <div x-show="showLogoModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" @click.away="showLogoModal = false" class="fixed top-12 left-0 right-0 bg-white border-b border-gray-200 shadow-xl z-[1001] p-6">
        <div class="max-w-md mx-auto text-center">
            <img src="{{ asset('bunya logo.jpg') }}" alt="BSS Logo" class="h-16 w-auto object-contain mx-auto mb-3 drop-shadow-lg">
            <h2 class="text-lg font-bold bg-gradient-to-br from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">BSS OB/OG INVESTMENT GROUP SYSTEM</h2>
            <p class="text-sm text-gray-600 mb-1">Your trusted investment partner</p>
            <p class="text-xs text-gray-400">Building wealth together since 2020</p>
        </div>
    </div>

    <!-- ==================== MAIN LAYOUT ==================== -->
    <div class="flex pt-12">

        <!-- ==================== SIDEBAR ==================== -->
        <aside class="sidebar w-36 bg-gradient-to-b from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 border-r border-blue-200 dark:border-gray-700 fixed left-0 top-12 h-[calc(100vh-3rem)] overflow-y-scroll transition-all duration-300 lg:translate-x-0 z-10 flex flex-col"
               :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'collapsed' : '']"
               id="sidebar">

            <div class="p-2 flex-1">
                <nav class="space-y-1 sidebar-nav">

                                        <!-- User Profile -->
                    <div class="p-2 border-t border-blue-200">
                        <div class="flex flex-col items-center py-3">
                            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-2 overflow-hidden cursor-pointer" @click="showProfileViewModal = true">
                                <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover">
                                <i x-show="!profilePicture" class="fas fa-user text-blue-600 text-2xl"></i>
                            </div>
                            <p class="text-xs font-semibold text-gray-800 text-center">John Doe</p>
                            <p class="text-[10px] text-gray-500">Shareholder</p>
                        </div>
                    </div>

                    <!-- Search Bar -->
                    <div class="nav-item search-item relative">
                        <i class="fas fa-search text-gray-500" @click="if(sidebarCollapsed) { sidebarCollapsed = false; }"></i>
                        <input type="text"
                               x-model="sidebarSearch"
                               @input="handleSidebarSearch($event.target.value)"
                               placeholder="Search menu..."
                               class="flex-1 bg-transparent border-none outline-none text-xs text-gray-700 placeholder-gray-500 ml-2">
                        <button x-show="sidebarSearch"
                                @click="clearSidebarSearch()"
                                class="text-gray-400 hover:text-gray-600 transition ml-2">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

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

                <!-- Sidebar Toggle Button -->
                <button class="sidebar-toggle hidden lg:block" @click="sidebarCollapsed = !sidebarCollapsed" title="Toggle Sidebar">
                    <i class="fas fa-chevron-left"></i>
                </button>

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


        </aside>

        <!-- Sidebar Toggle Button -->
        <button class="sidebar-toggle hidden lg:block" @click="sidebarCollapsed = !sidebarCollapsed">
            <i class="fas fa-chevron-right"></i>
        </button>

        <!-- ==================== MAIN CONTENT ==================== -->
        <div class="main-content flex-1 overflow-y-auto h-screen" :class="sidebarCollapsed ? 'sidebar-collapsed' : ''">
            <div class="p-4 md:p-6 lg:p-8 h-full">

                <!-- ==================== SECTION 1: PORTFOLIO OVERVIEW ==================== -->
                <section id="overview" class="scroll-mt-20" x-show="activeLink === 'overview'">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Portfolio Overview</h2>
                        <p class="text-sm text-gray-500 mt-1">Your investment summary at a glance</p>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                        <!-- Total Shares -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500 hover:shadow-xl transition-all duration-300 cursor-pointer" @click="activeLink = 'investments'; document.getElementById('investments').scrollIntoView({ behavior: 'smooth' })">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Total Shares Owned</p>
                                    <p class="text-2xl font-bold text-green-600" x-text="portfolioData.shares || 1250">1,250</p>
                                    <p class="text-xs text-gray-500 mt-1">@ UGX 1,800 per share</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-certificate text-green-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-sm text-green-600">
                                        <i class="fas fa-arrow-up mr-1"></i><span>+5.2% this quarter</span>
                                    </div>
                                    <span class="text-xs text-gray-500">15.2% of total</span>
                                </div>
                            </div>
                        </div>

                        <!-- Dividend Earned -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500 hover:shadow-xl transition-all duration-300 cursor-pointer" @click="activeLink = 'dividends'; document.getElementById('dividends').scrollIntoView({ behavior: 'smooth' })">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Total Dividends Earned</p>
                                    <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(portfolioData.dividends)">UGX 125,000</p>
                                    <p class="text-xs text-gray-500 mt-1">10% annual yield</p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-coins text-blue-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-sm text-blue-600">
                                        <i class="fas fa-calendar mr-1"></i><span>Q4 2024: UGX 35K</span>
                                    </div>
                                    <span class="text-xs text-green-600 font-medium">Next: Jan 15</span>
                                </div>
                            </div>
                        </div>

                        <!-- ROI -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500 hover:shadow-xl transition-all duration-300 cursor-pointer" @click="activeLink = 'portfolio'; document.getElementById('portfolio').scrollIntoView({ behavior: 'smooth' })">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Annual ROI</p>
                                    <p class="text-2xl font-bold text-purple-600" x-text="(portfolioData.roi || 12.5).toFixed(1) + '%'">12.5%</p>
                                    <p class="text-xs text-gray-500 mt-1">vs 8% market avg</p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-sm text-purple-600">
                                        <i class="fas fa-trending-up mr-1"></i><span>+4.5% above target</span>
                                    </div>
                                    <span class="text-xs text-green-600 font-medium">Excellent</span>
                                </div>
                            </div>
                        </div>

                        <!-- Portfolio Value -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500 hover:shadow-xl transition-all duration-300 cursor-pointer" @click="showPerformanceModal = true">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Total Portfolio Value</p>
                                    <p class="text-2xl font-bold text-orange-600" x-text="formatCurrency(portfolioData.totalValue)">UGX 2,450,000</p>
                                    <p class="text-xs text-gray-500 mt-1">Initial: UGX 2,000,000</p>
                                </div>
                                <div class="p-3 bg-orange-100 rounded-full">
                                    <i class="fas fa-briefcase text-orange-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-sm text-green-600">
                                        <i class="fas fa-arrow-up mr-1"></i><span>+22.5% total gain</span>
                                    </div>
                                    <span class="text-xs text-green-600 font-medium">+UGX 450K</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BSS Investment Group Information Panel -->
                    <div class="mt-8 bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 rounded-xl shadow-lg border border-blue-200 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-building text-white text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-800">BSS Investment Group Overview</h3>
                                        <p class="text-sm text-gray-600">Your trusted investment partner since 2020</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Member Since</p>
                                    <p class="text-lg font-bold text-blue-600">Jan 2022</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Group Statistics -->
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-users text-blue-600 mr-2"></i>Group Statistics
                                    </h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Total Members:</span>
                                            <span class="font-medium text-gray-800">82 Active</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Total Assets:</span>
                                            <span class="font-medium text-green-600">UGX 125M</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Active Projects:</span>
                                            <span class="font-medium text-purple-600">5 Running</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Success Rate:</span>
                                            <span class="font-medium text-green-600">94.2%</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Achievements -->
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-trophy text-yellow-500 mr-2"></i>Recent Achievements
                                    </h4>
                                    <div class="space-y-2">
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            <span class="text-gray-700">Water Project Completed</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            <span class="text-gray-700">15% ROI Target Exceeded</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            <span class="text-gray-700">New Member Milestone</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-star text-yellow-500 mr-2"></i>
                                            <span class="text-gray-700">Top Performer Q4 2024</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upcoming Events -->
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>Upcoming Events
                                    </h4>
                                    <div class="space-y-2">
                                        <div class="text-sm">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-700">AGM 2025</span>
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Jan 20</span>
                                            </div>
                                        </div>
                                        <div class="text-sm">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-700">Dividend Payment</span>
                                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Jan 15</span>
                                            </div>
                                        </div>
                                        <div class="text-sm">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-700">New Project Launch</span>
                                                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">Feb 1</span>
                                            </div>
                                        </div>
                                        <div class="text-sm">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-700">Financial Review</span>
                                                <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded">Mar 15</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 2: PORTFOLIO ANALYTICS ==================== -->
                <section id="portfolio" class="scroll-mt-20" x-show="activeLink === 'portfolio'">
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
                <section id="insights" class="scroll-mt-20" x-show="activeLink === 'insights'">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Market Insights</h2>
                        <p class="text-sm text-gray-500 mt-1">Latest announcements and opportunities</p>
                    </div>

                    <!-- Market Insights Cards -->
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
                                    <span class="text-xs bg-green-600 text-white px-2 py-1 rounded-full">+4.5%</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">Portfolio outperforming market by 4.5%</p>
                                <div class="mt-2 flex items-center text-xs text-green-700">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    <span>Consistent upward trend</span>
                                </div>
                            </div>

                            <!-- Dividend Announcement Card -->
                            <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500 cursor-pointer hover:shadow-lg transition"
                                 @click="showDividendModal = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                        <span class="text-sm font-medium">Q1 2025 Dividend</span>
                                    </div>
                                    <span class="text-xs bg-blue-600 text-white px-2 py-1 rounded-full">NEW</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">Payment scheduled for January 15, 2025</p>
                                <div class="mt-2 flex items-center text-xs text-blue-700">
                                    <i class="fas fa-coins mr-1"></i>
                                    <span>Expected: UGX 42,000</span>
                                </div>
                            </div>

                            <!-- Growth Opportunity Card -->
                            <div class="p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500 cursor-pointer hover:shadow-lg transition"
                                 @click="showOpportunitiesModal = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-rocket text-purple-600 mr-2"></i>
                                        <span class="text-sm font-medium">New Opportunities</span>
                                    </div>
                                    <span class="text-xs bg-purple-600 text-white px-2 py-1 rounded-full">3</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">Agricultural Expansion Project launching</p>
                                <div class="mt-2 flex items-center text-xs text-purple-700">
                                    <i class="fas fa-percentage mr-1"></i>
                                    <span>Expected ROI: 18.5%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Market Analysis & Predictions -->
                    <div class="mt-6 bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50 rounded-xl shadow-lg border border-green-200 p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-chart-area text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Market Analysis & Predictions</h3>
                                    <p class="text-sm text-gray-600">AI-powered insights for BSS Investment Group</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Confidence Level</p>
                                <p class="text-lg font-bold text-green-600">94.2%</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-trending-up text-green-600 mr-2"></i>Market Outlook
                                </h4>
                                <p class="text-2xl font-bold text-green-600 mb-1">Bullish</p>
                                <p class="text-xs text-gray-600">Next 6 months: Strong growth expected in agricultural and renewable energy sectors</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-shield-alt text-blue-600 mr-2"></i>Risk Assessment
                                </h4>
                                <p class="text-2xl font-bold text-blue-600 mb-1">Low</p>
                                <p class="text-xs text-gray-600">Diversified portfolio with 85% success rate in similar market conditions</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-target text-purple-600 mr-2"></i>ROI Forecast
                                </h4>
                                <p class="text-2xl font-bold text-purple-600 mb-1">16.8%</p>
                                <p class="text-xs text-gray-600">Projected annual return based on current portfolio and market trends</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-clock text-orange-600 mr-2"></i>Best Entry
                                </h4>
                                <p class="text-2xl font-bold text-orange-600 mb-1">Now</p>
                                <p class="text-xs text-gray-600">Optimal timing for new investments in Q1 2025 opportunities</p>
                            </div>
                        </div>
                    </div>

                    <!-- Investment Opportunities Radar -->
                    <div class="mt-6 bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-radar text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800">Investment Opportunities Radar</h3>
                                        <p class="text-sm text-gray-500">Curated opportunities matching your risk profile</p>
                                    </div>
                                </div>
                                <button class="text-purple-600 text-sm hover:underline">View All Opportunities</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                            <div class="space-y-4">
                                <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-semibold text-gray-800 flex items-center">
                                            <i class="fas fa-seedling text-green-600 mr-2"></i>Agricultural Tech Expansion
                                        </h4>
                                        <span class="text-xs bg-green-600 text-white px-2 py-1 rounded-full">HOT</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 text-sm mb-3">
                                        <div><span class="text-gray-600">ROI:</span> <span class="font-bold text-green-600">22.5%</span></div>
                                        <div><span class="text-gray-600">Risk:</span> <span class="font-bold text-yellow-600">Medium</span></div>
                                        <div><span class="text-gray-600">Duration:</span> <span class="font-bold text-gray-800">18 months</span></div>
                                    </div>
                                    <p class="text-xs text-gray-600 mb-3">Modern farming techniques with IoT integration. Target: UGX 15M investment, 150+ farmers benefiting.</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-green-600 font-medium">Match Score: 96%</span>
                                        <button class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Learn More</button>
                                    </div>
                                </div>
                                <div class="p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg border border-blue-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-semibold text-gray-800 flex items-center">
                                            <i class="fas fa-solar-panel text-blue-600 mr-2"></i>Solar Energy Cooperative
                                        </h4>
                                        <span class="text-xs bg-blue-600 text-white px-2 py-1 rounded-full">NEW</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 text-sm mb-3">
                                        <div><span class="text-gray-600">ROI:</span> <span class="font-bold text-blue-600">19.8%</span></div>
                                        <div><span class="text-gray-600">Risk:</span> <span class="font-bold text-green-600">Low</span></div>
                                        <div><span class="text-gray-600">Duration:</span> <span class="font-bold text-gray-800">24 months</span></div>
                                    </div>
                                    <p class="text-xs text-gray-600 mb-3">Community solar installation project. Government-backed with guaranteed energy purchase agreements.</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-blue-600 font-medium">Match Score: 92%</span>
                                        <button class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Learn More</button>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-semibold text-gray-800 flex items-center">
                                            <i class="fas fa-store text-purple-600 mr-2"></i>Digital Marketplace Platform
                                        </h4>
                                        <span class="text-xs bg-purple-600 text-white px-2 py-1 rounded-full">TRENDING</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 text-sm mb-3">
                                        <div><span class="text-gray-600">ROI:</span> <span class="font-bold text-purple-600">28.3%</span></div>
                                        <div><span class="text-gray-600">Risk:</span> <span class="font-bold text-orange-600">High</span></div>
                                        <div><span class="text-gray-600">Duration:</span> <span class="font-bold text-gray-800">12 months</span></div>
                                    </div>
                                    <p class="text-xs text-gray-600 mb-3">E-commerce platform connecting rural producers to urban markets. High growth potential with tech integration.</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-purple-600 font-medium">Match Score: 88%</span>
                                        <button class="text-xs bg-purple-600 text-white px-3 py-1 rounded hover:bg-purple-700">Learn More</button>
                                    </div>
                                </div>
                                <div class="p-4 bg-gradient-to-r from-orange-50 to-red-50 rounded-lg border border-orange-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-semibold text-gray-800 flex items-center">
                                            <i class="fas fa-graduation-cap text-orange-600 mr-2"></i>Education Infrastructure
                                        </h4>
                                        <span class="text-xs bg-orange-600 text-white px-2 py-1 rounded-full">STABLE</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 text-sm mb-3">
                                        <div><span class="text-gray-600">ROI:</span> <span class="font-bold text-orange-600">14.2%</span></div>
                                        <div><span class="text-gray-600">Risk:</span> <span class="font-bold text-green-600">Very Low</span></div>
                                        <div><span class="text-gray-600">Duration:</span> <span class="font-bold text-gray-800">36 months</span></div>
                                    </div>
                                    <p class="text-xs text-gray-600 mb-3">School construction and digital learning centers. Government partnership with steady returns and social impact.</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-orange-600 font-medium">Match Score: 85%</span>
                                        <button class="text-xs bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700">Learn More</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BSS Investment Group News & Updates -->
                    <div class="mt-6 bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-newspaper text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800">Latest News & Updates</h3>
                                        <p class="text-sm text-gray-500">Stay informed about BSS Investment Group</p>
                                    </div>
                                </div>
                                <button class="text-blue-600 text-sm hover:underline">View All</button>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <div class="p-4 hover:bg-gray-50 transition cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-medium text-gray-800 text-sm">Community Water Project Successfully Completed</h4>
                                            <span class="text-xs text-gray-500">Dec 20, 2024</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">The UGX 5M water project has been completed ahead of schedule, delivering clean water to 500+ families. ROI exceeded expectations at 15.2%.</p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Completed</span>
                                            <span class="text-xs text-green-600 font-medium">+15.2% ROI</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 hover:bg-gray-50 transition cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-rocket text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-medium text-gray-800 text-sm">New Agricultural Expansion Project Announced</h4>
                                            <span class="text-xs text-gray-500">Dec 18, 2024</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">BSS is launching a UGX 12M agricultural project focusing on modern farming techniques. Expected to generate 18.5% annual returns.</p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">New Opportunity</span>
                                            <span class="text-xs text-blue-600 font-medium">18.5% Expected ROI</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 hover:bg-gray-50 transition cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-users text-purple-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-medium text-gray-800 text-sm">Annual General Meeting 2025 Scheduled</h4>
                                            <span class="text-xs text-gray-500">Dec 15, 2024</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Join us for our AGM on January 20, 2025, at 10:00 AM. We'll review 2024 performance and discuss 2025 strategic plans.</p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">Important</span>
                                            <span class="text-xs text-purple-600 font-medium">Jan 20, 2025</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 hover:bg-gray-50 transition cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-chart-line text-orange-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-medium text-gray-800 text-sm">Q4 2024 Performance Report Released</h4>
                                            <span class="text-xs text-gray-500">Dec 10, 2024</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">BSS achieved 12.8% average ROI in Q4 2024, surpassing our 10% target. Total group assets now exceed UGX 125 million.</p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded">Performance</span>
                                            <span class="text-xs text-green-600 font-medium">Target Exceeded</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 4: LOAN MANAGEMENT ==================== -->
                <section id="loans" class="scroll-mt-20" x-show="activeLink === 'loans'">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Comprehensive Loan Management</h2>
                        <p class="text-sm text-gray-500 mt-1">Complete loan lifecycle management and financial planning</p>
                    </div>

                    <!-- Loan Overview Dashboard -->
                    <div class="bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50 rounded-xl shadow-lg border border-green-200 p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-hand-holding-usd text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Loan Portfolio Overview</h3>
                                    <p class="text-sm text-gray-600">Your complete borrowing profile and creditworthiness</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Credit Score</p>
                                <p class="text-lg font-bold text-green-600">Excellent</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-chart-line text-green-600 mr-2"></i>Loan Capacity
                                </h4>
                                <p class="text-2xl font-bold text-green-600 mb-1">UGX 3.5M</p>
                                <p class="text-xs text-gray-600">Maximum eligible amount based on your savings and income profile</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-percentage text-blue-600 mr-2"></i>Interest Rate
                                </h4>
                                <p class="text-2xl font-bold text-blue-600 mb-1">8.5%</p>
                                <p class="text-xs text-gray-600">Preferential rate for shareholders with excellent credit history</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-clock text-purple-600 mr-2"></i>Processing Time
                                </h4>
                                <p class="text-2xl font-bold text-purple-600 mb-1">24 Hours</p>
                                <p class="text-xs text-gray-600">Fast-track approval for existing members with good standing</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-shield-alt text-orange-600 mr-2"></i>Collateral
                                </h4>
                                <p class="text-2xl font-bold text-orange-600 mb-1">Flexible</p>
                                <p class="text-xs text-gray-600">Savings-backed loans available with minimal documentation</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-4 md:p-6 border-b border-gray-100">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <h3 class="text-lg font-semibold text-gray-800">Financial Summary & Loan Calculator</h3>
                                <button @click="showLoanRequestModal = true" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium">
                                    <i class="fas fa-plus mr-2"></i>Apply for New Loan
                                </button>
                            </div>
                        </div>

                        <!-- Enhanced Financial Summary Cards -->
                        <div class="p-4 md:p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-500 ring-1 ring-green-200 shadow-sm hover:shadow-md transition-all">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-600 font-medium">Total Savings Balance</p>
                                        <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(myFinancials.savings)">UGX 1,500,000</p>
                                        <p class="text-xs text-gray-500 mt-1">Available as collateral</p>
                                    </div>
                                    <i class="fas fa-piggy-bank text-green-600 text-3xl"></i>
                                </div>
                                <div class="mt-3">
                                    <div class="flex items-center text-xs text-green-600">
                                        <i class="fas fa-arrow-up mr-1"></i><span>+12% growth this year</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 bg-red-50 rounded-lg border-l-4 border-red-500 ring-1 ring-red-200 shadow-sm hover:shadow-md transition-all">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-600 font-medium">Outstanding Loans</p>
                                        <p class="text-2xl font-bold text-red-600" x-text="formatCurrency(myFinancials.loan)">UGX 500,000</p>
                                        <p class="text-xs text-gray-500 mt-1">Across active facilities</p>
                                    </div>
                                    <i class="fas fa-hand-holding-usd text-red-600 text-3xl"></i>
                                </div>
                                <div class="mt-3">
                                    <div class="flex items-center text-xs text-red-600">
                                        <i class="fas fa-calendar mr-1"></i><span>Next payment: Jan 15</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500 ring-1 ring-blue-200 shadow-sm hover:shadow-md transition-all">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-600 font-medium">Available Credit</p>
                                        <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(myFinancials.balance + 2000000)">UGX 3,000,000</p>
                                        <p class="text-xs text-gray-500 mt-1">Ready to borrow</p>
                                    </div>
                                    <i class="fas fa-wallet text-blue-600 text-3xl"></i>
                                </div>
                                <div class="mt-3">
                                    <div class="flex items-center text-xs text-blue-600">
                                        <i class="fas fa-check-circle mr-1"></i><span>Pre-approved</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Loan Calculator -->
                        <div class="mt-6 p-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg border border-purple-200">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-calculator text-purple-600 mr-2"></i>Quick Loan Calculator
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4" x-data="{ loanAmount: 1000000, loanTerm: 12, interestRate: 8.5 }">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Loan Amount (UGX)</label>
                                    <input type="number" x-model="loanAmount" class="w-full p-2 border rounded text-sm" min="100000" max="3500000" step="50000">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Term (Months)</label>
                                    <select x-model="loanTerm" class="w-full p-2 border rounded text-sm">
                                        <option value="6">6 months</option>
                                        <option value="12">12 months</option>
                                        <option value="18">18 months</option>
                                        <option value="24">24 months</option>
                                        <option value="36">36 months</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Interest Rate (%)</label>
                                    <input type="number" x-model="interestRate" class="w-full p-2 border rounded text-sm bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Monthly Payment</label>
                                    <div class="p-2 bg-white border rounded text-sm font-bold text-purple-600" x-text="'UGX ' + Math.round((loanAmount * (interestRate/100/12) * Math.pow(1 + interestRate/100/12, loanTerm)) / (Math.pow(1 + interestRate/100/12, loanTerm) - 1)).toLocaleString()"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Loan Applications Table -->
                        <div class="mt-6">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-md font-semibold text-gray-700">My Loan History & Applications</h4>
                                <div class="flex items-center space-x-2">
                                    <button @click="toggleReverseOrder('loans')"
                                            :class="reverseOrderState.loans ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-1 rounded text-xs transition" title="Reverse Order">
                                        <i class="fas fa-sort-numeric-alt mr-1"></i>
                                        <span x-text="reverseOrderState.loans ? 'Oldest First' : 'Newest First'"></span>
                                    </button>
                                    <button class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                                        <i class="fas fa-download mr-1"></i>Export
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loan ID</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monthly Payment</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-if="myLoans.length === 0">
                                            <tr>
                                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                                    <div class="flex flex-col items-center">
                                                        <i class="fas fa-hand-holding-usd text-4xl mb-2 text-gray-300"></i>
                                                        <p class="font-medium">No loan applications yet</p>
                                                        <p class="text-sm">Click "Apply for New Loan" to get started with competitive rates</p>
                                                    </div>
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
                                                <td class="px-4 py-3 text-sm font-medium text-red-600" x-text="formatCurrency(loan.amount * 0.7)"></td>
                                                <td class="px-4 py-3">
                                                    <span class="px-3 py-1 text-xs font-medium rounded-full"
                                                          :class="{
                                                              'bg-yellow-100 text-yellow-800': loan.status === 'pending',
                                                              'bg-green-100 text-green-800': loan.status === 'approved',
                                                              'bg-red-100 text-red-800': loan.status === 'rejected',
                                                              'bg-blue-100 text-blue-800': loan.status === 'active'
                                                          }"
                                                          x-text="loan.status.toUpperCase()"></span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center space-x-2">
                                                        <button class="text-blue-600 hover:text-blue-800 text-xs" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="text-green-600 hover:text-green-800 text-xs" title="Make Payment">
                                                            <i class="fas fa-credit-card"></i>
                                                        </button>
                                                        <button class="text-purple-600 hover:text-purple-800 text-xs" title="Download Statement">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 5: MEMBERS ANALYTICS ==================== -->
                <section id="members" class="scroll-mt-20 pb-8" x-show="activeLink === 'members'">
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
                <section id="profile" class="scroll-mt-20" x-show="activeLink === 'profile'">
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
                        
                        <!-- Detailed Profile Information -->
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" value="John Doe" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                    <input type="date" value="1985-03-15" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                    <input type="text" value="Male" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
                                    <input type="text" value="Married" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                                    <input type="text" value="Ugandan" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Religion</label>
                                    <input type="text" value="Christian" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-gray-100">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <input type="email" value="john.doe@example.com" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" value="+256 701 234 567" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alternative Phone</label>
                                    <input type="tel" value="+256 782 345 678" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
                                    <input type="tel" value="+256 701 234 567" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Physical Address</label>
                                    <textarea class="w-full p-3 border rounded-lg bg-gray-50" rows="2" readonly>Plot 123, Kampala Road, Kampala Central Division, Kampala District, Uganda</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-gray-100">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Professional Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
                                    <input type="text" value="Business Manager" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Employer</label>
                                    <input type="text" value="ABC Trading Company Ltd" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Work Experience</label>
                                    <input type="text" value="8 Years" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Income Range</label>
                                    <input type="text" value="UGX 2,000,000 - 3,000,000" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Work Address</label>
                                    <textarea class="w-full p-3 border rounded-lg bg-gray-50" rows="2" readonly>Plot 456, Industrial Area, Kampala, Uganda</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-gray-100">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Educational Background</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Highest Education Level</label>
                                    <input type="text" value="Bachelor's Degree" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Field of Study</label>
                                    <input type="text" value="Business Administration" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Institution</label>
                                    <input type="text" value="Makerere University" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Year of Graduation</label>
                                    <input type="text" value="2008" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-gray-100">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Emergency Contact</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Name</label>
                                    <input type="text" value="Jane Doe" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                                    <input type="text" value="Spouse" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" value="+256 703 456 789" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <input type="email" value="jane.doe@example.com" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-gray-100">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">BSS Membership Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Member Since</label>
                                    <input type="date" value="2022-01-15" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Membership Type</label>
                                    <input type="text" value="Shareholder" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Referral Source</label>
                                    <input type="text" value="Friend Referral" class="w-full p-3 border rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <input type="text" value="Active" class="w-full p-3 border rounded-lg bg-green-50 text-green-800" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 border-t border-gray-100">
                            <div class="flex justify-end space-x-3">
                                <button class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-edit mr-2"></i>Edit Profile
                                </button>
                                <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                    <i class="fas fa-download mr-2"></i>Download Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 7: MY SAVINGS ==================== -->
                <section id="savings" class="scroll-mt-20" x-show="activeLink === 'savings'">
                    <div class="mb-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">My Savings</h2>
                                <p class="text-sm text-gray-500 mt-1">Track your savings growth and manage deposits</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    <i class="fas fa-plus mr-2"></i>Make Deposit
                                </button>
                                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                    <i class="fas fa-download mr-2"></i>Export
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Savings Overview Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                        <!-- Total Savings -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500 hover:shadow-xl transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Total Savings</p>
                                    <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(myFinancials.savings)">UGX 1,500,000</p>
                                    <p class="text-xs text-gray-500 mt-1">Principal amount</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-piggy-bank text-green-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-arrow-up mr-1"></i><span>+12.5% this year</span>
                                </div>
                            </div>
                        </div>

                        <!-- This Month Savings -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">This Month</p>
                                    <p class="text-2xl font-bold text-blue-600">UGX 250,000</p>
                                    <p class="text-xs text-gray-500 mt-1">December deposits</p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-blue-600">
                                    <i class="fas fa-target mr-1"></i><span>Goal: UGX 300K</span>
                                </div>
                            </div>
                        </div>

                        <!-- Interest Earned -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500 hover:shadow-xl transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Interest Earned</p>
                                    <p class="text-2xl font-bold text-purple-600">UGX 45,000</p>
                                    <p class="text-xs text-gray-500 mt-1">3% annual rate</p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-purple-600">
                                    <i class="fas fa-chart-line mr-1"></i><span>Compounding</span>
                                </div>
                            </div>
                        </div>

                        <!-- Available Balance -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500 hover:shadow-xl transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Available Balance</p>
                                    <p class="text-2xl font-bold text-orange-600">UGX 1,445,000</p>
                                    <p class="text-xs text-gray-500 mt-1">After withdrawals</p>
                                </div>
                                <div class="p-3 bg-orange-100 rounded-full">
                                    <i class="fas fa-wallet text-orange-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-orange-600">
                                    <i class="fas fa-shield-alt mr-1"></i><span>Protected</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Savings Goals & Progress -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Savings Goals -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Savings Goals</h3>
                                <button class="text-blue-600 text-sm hover:underline">
                                    <i class="fas fa-plus mr-1"></i>Add Goal
                                </button>
                            </div>
                            <div class="space-y-4">
                                <div class="p-4 bg-blue-50 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-medium text-gray-800">Emergency Fund</h4>
                                        <span class="text-sm font-bold text-blue-600">75%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: 75%"></div>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>UGX 750,000 / UGX 1,000,000</span>
                                        <span class="text-blue-600">UGX 250K to go</span>
                                    </div>
                                </div>
                                <div class="p-4 bg-green-50 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-medium text-gray-800">Investment Capital</h4>
                                        <span class="text-sm font-bold text-green-600">60%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: 60%"></div>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>UGX 600,000 / UGX 1,000,000</span>
                                        <span class="text-green-600">UGX 400K to go</span>
                                    </div>
                                </div>
                                <div class="p-4 bg-purple-50 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-medium text-gray-800">Education Fund</h4>
                                        <span class="text-sm font-bold text-purple-600">30%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                        <div class="bg-purple-500 h-2 rounded-full" style="width: 30%"></div>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>UGX 150,000 / UGX 500,000</span>
                                        <span class="text-purple-600">UGX 350K to go</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Savings Growth Chart -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Growth Trend</h3>
                                <div class="flex space-x-2">
                                    <button class="px-3 py-1 bg-blue-600 text-white rounded text-xs">6M</button>
                                    <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-xs">1Y</button>
                                    <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-xs">All</button>
                                </div>
                            </div>
                            <div style="height: 250px;">
                                <canvas id="savingsGrowthChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Savings History -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Transaction History</h3>
                                    <p class="text-sm text-gray-500">Complete record of your savings activity</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <select class="px-3 py-2 border rounded-lg text-sm">
                                        <option value="">All Types</option>
                                        <option value="deposit">Deposits</option>
                                        <option value="withdrawal">Withdrawals</option>
                                        <option value="interest">Interest</option>
                                    </select>
                                    <button @click="toggleReverseOrder('savings')"
                                            :class="reverseOrderState.savings ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-2 rounded text-xs transition" title="Reverse Order">
                                        <i class="fas fa-sort-numeric-alt mr-1"></i>
                                        <span x-text="reverseOrderState.savings ? 'Oldest First' : 'Newest First'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">Dec 15, 2024</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-plus mr-1"></i>Deposit
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">Monthly savings contribution</td>
                                        <td class="px-4 py-3 text-sm font-medium text-green-600">+UGX 100,000</td>
                                        <td class="px-4 py-3 text-sm font-medium">UGX 1,500,000</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">Dec 1, 2024</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-plus mr-1"></i>Deposit
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">Regular monthly deposit</td>
                                        <td class="px-4 py-3 text-sm font-medium text-green-600">+UGX 150,000</td>
                                        <td class="px-4 py-3 text-sm font-medium">UGX 1,400,000</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">Nov 30, 2024</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                                <i class="fas fa-percentage mr-1"></i>Interest
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">Monthly interest credit</td>
                                        <td class="px-4 py-3 text-sm font-medium text-purple-600">+UGX 3,750</td>
                                        <td class="px-4 py-3 text-sm font-medium">UGX 1,253,750</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">Nov 15, 2024</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-minus mr-1"></i>Withdrawal
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">Emergency fund withdrawal</td>
                                        <td class="px-4 py-3 text-sm font-medium text-red-600">-UGX 100,000</td>
                                        <td class="px-4 py-3 text-sm font-medium">UGX 1,250,000</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">Nov 1, 2024</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-plus mr-1"></i>Deposit
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">Monthly savings contribution</td>
                                        <td class="px-4 py-3 text-sm font-medium text-green-600">+UGX 200,000</td>
                                        <td class="px-4 py-3 text-sm font-medium">UGX 1,350,000</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
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

                <!-- ==================== SECTION 8: DIVIDENDS ==================== -->
                <section id="dividends" class="scroll-mt-20" x-show="activeLink === 'dividends'">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Dividend Management</h2>
                        <p class="text-sm text-gray-500 mt-1">View your dividend earnings and history</p>
                    </div>

                    <!-- Enhanced Dividend Overview Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Total Dividends Earned</p>
                                    <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(portfolioData.dividends)">UGX 125,000</p>
                                    <p class="text-xs text-gray-500 mt-1">Since joining BSS</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-coins text-green-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-arrow-up mr-1"></i><span>+15% from last year</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Q4 2024 Payment</p>
                                    <p class="text-2xl font-bold text-blue-600">UGX 35,000</p>
                                    <p class="text-xs text-gray-500 mt-1">Paid Dec 15, 2024</p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-blue-600">
                                    <i class="fas fa-check-circle mr-1"></i><span>Payment completed</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Annual Dividend Yield</p>
                                    <p class="text-2xl font-bold text-purple-600">10.5%</p>
                                    <p class="text-xs text-gray-500 mt-1">Above market average</p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-purple-600">
                                    <i class="fas fa-trophy mr-1"></i><span>Top performer</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Next Payment (Q1 2025)</p>
                                    <p class="text-2xl font-bold text-orange-600">UGX 42,000</p>
                                    <p class="text-xs text-gray-500 mt-1">Expected Jan 15, 2025</p>
                                </div>
                                <div class="p-3 bg-orange-100 rounded-full">
                                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-orange-600">
                                    <i class="fas fa-calendar-alt mr-1"></i><span>22 days remaining</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dividend Performance Analysis -->
                    <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 rounded-xl shadow-lg border border-blue-200 p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-chart-pie text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Dividend Performance Analysis</h3>
                                    <p class="text-sm text-gray-600">Your dividend earnings breakdown and projections</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Avg. Quarterly</p>
                                <p class="text-lg font-bold text-blue-600">UGX 31,250</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Dividend Growth -->
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-trending-up text-green-600 mr-2"></i>Growth Trend
                                </h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">2024 Total:</span>
                                        <span class="font-medium text-green-600">UGX 125,000</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">2023 Total:</span>
                                        <span class="font-medium text-gray-800">UGX 108,750</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Growth Rate:</span>
                                        <span class="font-medium text-green-600">+15%</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Consistency:</span>
                                        <span class="font-medium text-blue-600">100%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quarterly Breakdown -->
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>2024 Quarterly
                                </h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Q4 2024:</span>
                                        <span class="font-medium text-green-600">UGX 35,000</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Q3 2024:</span>
                                        <span class="font-medium text-gray-800">UGX 32,500</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Q2 2024:</span>
                                        <span class="font-medium text-gray-800">UGX 30,000</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Q1 2024:</span>
                                        <span class="font-medium text-gray-800">UGX 27,500</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 2025 Projections -->
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-crystal-ball text-purple-600 mr-2"></i>2025 Projections
                                </h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Q1 2025:</span>
                                        <span class="font-medium text-purple-600">UGX 42,000</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Projected Annual:</span>
                                        <span class="font-medium text-purple-600">UGX 160,000</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Growth Target:</span>
                                        <span class="font-medium text-green-600">+28%</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Confidence:</span>
                                        <span class="font-medium text-green-600">High</span>
                                    </div>
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
                <section id="investments" class="scroll-mt-20" x-show="activeLink === 'investments'">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">My Investment Portfolio</h2>
                        <p class="text-sm text-gray-500 mt-1">Comprehensive investment tracking and management</p>
                    </div>

                    <!-- Investment Overview Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Total Portfolio Value</p>
                                    <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(portfolioData.totalValue)">UGX 2,450,000</p>
                                    <p class="text-xs text-gray-500 mt-1">Across 3 active projects</p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-arrow-up mr-1"></i><span>+18.5% this year</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Total Returns Generated</p>
                                    <p class="text-2xl font-bold text-green-600">UGX 387,500</p>
                                    <p class="text-xs text-gray-500 mt-1">Realized + Unrealized</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-coins text-green-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-trending-up mr-1"></i><span>15.8% avg ROI</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Active Investments</p>
                                    <p class="text-2xl font-bold text-purple-600" x-text="investmentProjects.length">3</p>
                                    <p class="text-xs text-gray-500 mt-1">2 performing well</p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-project-diagram text-purple-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-purple-600">
                                    <i class="fas fa-check-circle mr-1"></i><span>All on track</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 font-medium">Available for Investment</p>
                                    <p class="text-2xl font-bold text-orange-600">UGX 750,000</p>
                                    <p class="text-xs text-gray-500 mt-1">Ready to deploy</p>
                                </div>
                                <div class="p-3 bg-orange-100 rounded-full">
                                    <i class="fas fa-wallet text-orange-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-orange-600">
                                    <i class="fas fa-plus-circle mr-1"></i><span>New opportunities</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Investment Performance Dashboard -->
                    <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 rounded-xl shadow-lg border border-blue-200 p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-chart-area text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Investment Performance Dashboard</h3>
                                    <p class="text-sm text-gray-600">Real-time portfolio analytics and insights</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Portfolio Score</p>
                                <p class="text-lg font-bold text-green-600">A+</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-bullseye text-green-600 mr-2"></i>Performance
                                </h4>
                                <p class="text-2xl font-bold text-green-600 mb-1">Excellent</p>
                                <p class="text-xs text-gray-600">Beating market by 4.2% with consistent returns across all investments</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-shield-alt text-blue-600 mr-2"></i>Risk Level
                                </h4>
                                <p class="text-2xl font-bold text-blue-600 mb-1">Moderate</p>
                                <p class="text-xs text-gray-600">Well-diversified portfolio with balanced risk-reward ratio</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-calendar-check text-purple-600 mr-2"></i>Time Horizon
                                </h4>
                                <p class="text-2xl font-bold text-purple-600 mb-1">18 Months</p>
                                <p class="text-xs text-gray-600">Average investment duration with staggered maturity dates</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-star text-yellow-500 mr-2"></i>Rating
                                </h4>
                                <p class="text-2xl font-bold text-yellow-500 mb-1">4.8/5</p>
                                <p class="text-xs text-gray-600">Based on returns, risk management, and diversification</p>
                            </div>
                        </div>
                    </div>

                    <!-- Active Investment Projects -->
                    <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Active Investment Projects</h3>
                                <p class="text-sm text-gray-500">Detailed view of your current investments</p>
                            </div>
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
                        <div class="space-y-6">
                            <template x-for="project in getReversedData(investmentProjects, 'investments')" :key="project.id">
                                <div class="p-6 border rounded-xl hover:bg-gray-50 transition-all duration-200 bg-gradient-to-r from-white to-gray-50">
                                    <!-- Project Header -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                                <i class="fas fa-project-diagram text-white text-lg"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-lg" x-text="project.name">Community Water Project</h4>
                                                <p class="text-sm text-gray-600">Started January 2024 • Duration: 12 months</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-medium">Active</span>
                                            <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800 font-medium">On Track</span>
                                        </div>
                                    </div>

                                    <!-- Progress Section -->
                                    <div class="mb-4">
                                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                                            <span class="font-medium">Project Progress</span>
                                            <span class="font-bold" x-text="project.progress + '% Complete'">65% Complete</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-500" :style="`width: ${project.progress}%`"></div>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                                            <span>Started</span>
                                            <span>Current</span>
                                            <span>Completion</span>
                                        </div>
                                    </div>

                                    <!-- Investment Details Grid -->
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                        <div class="bg-blue-50 p-4 rounded-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm text-gray-600 font-medium">Your Investment</span>
                                                <i class="fas fa-wallet text-blue-600"></i>
                                            </div>
                                            <p class="text-xl font-bold text-blue-600" x-text="formatCurrency(project.budget * 0.15)">UGX 750,000</p>
                                            <p class="text-xs text-gray-500">15% of total project</p>
                                        </div>
                                        <div class="bg-green-50 p-4 rounded-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm text-gray-600 font-medium">Current Value</span>
                                                <i class="fas fa-chart-line text-green-600"></i>
                                            </div>
                                            <p class="text-xl font-bold text-green-600" x-text="formatCurrency(project.budget * 0.15 * (1 + project.roi / 100))">UGX 843,750</p>
                                            <p class="text-xs text-gray-500" x-text="'+' + project.roi + '% gain'">+12.5% gain</p>
                                        </div>
                                        <div class="bg-purple-50 p-4 rounded-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm text-gray-600 font-medium">Expected Returns</span>
                                                <i class="fas fa-target text-purple-600"></i>
                                            </div>
                                            <p class="text-xl font-bold text-purple-600" x-text="formatCurrency(project.budget * 0.15 * (project.expected_roi / 100))">UGX 112,500</p>
                                            <p class="text-xs text-gray-500" x-text="project.expected_roi + '% target ROI'">15.0% target ROI</p>
                                        </div>
                                        <div class="bg-orange-50 p-4 rounded-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm text-gray-600 font-medium">Time Remaining</span>
                                                <i class="fas fa-clock text-orange-600"></i>
                                            </div>
                                            <p class="text-xl font-bold text-orange-600">4.2 Months</p>
                                            <p class="text-xs text-gray-500">Expected completion</p>
                                        </div>
                                    </div>

                                    <!-- Project Metrics -->
                                    <div class="border-t border-gray-100 pt-4">
                                        <div class="flex justify-between items-center text-sm">
                                            <div class="flex items-center space-x-6">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-gray-600">Total Budget:</span>
                                                    <span class="font-bold text-gray-800" x-text="formatCurrency(project.budget)">UGX 5,000,000</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-gray-600">Risk Level:</span>
                                                    <span class="font-bold text-yellow-600">Medium</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-gray-600">Sector:</span>
                                                    <span class="font-bold text-blue-600">Infrastructure</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    <i class="fas fa-chart-bar mr-1"></i>View Analytics
                                                </button>
                                                <button class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                    <i class="fas fa-download mr-1"></i>Report
                                                </button>
                                                <button class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                                    <i class="fas fa-cog mr-1"></i>Manage
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Investment Insights & Recommendations -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Portfolio Allocation -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-pie-chart text-blue-600 mr-2"></i>Portfolio Allocation
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                                        <span class="text-sm font-medium">Infrastructure</span>
                                    </div>
                                    <span class="text-sm font-bold text-blue-600">60%</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                                        <span class="text-sm font-medium">Renewable Energy</span>
                                    </div>
                                    <span class="text-sm font-bold text-green-600">25%</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                                        <span class="text-sm font-medium">Agriculture</span>
                                    </div>
                                    <span class="text-sm font-bold text-purple-600">15%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Investment Recommendations -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Smart Recommendations
                            </h3>
                            <div class="space-y-3">
                                <div class="p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                                    <p class="text-sm font-medium text-gray-800">Diversification Opportunity</p>
                                    <p class="text-xs text-gray-600 mt-1">Consider adding technology sector investments to balance your portfolio</p>
                                </div>
                                <div class="p-3 bg-green-50 rounded-lg border-l-4 border-green-500">
                                    <p class="text-sm font-medium text-gray-800">Reinvestment Suggestion</p>
                                    <p class="text-xs text-gray-600 mt-1">Your water project returns can be reinvested in the new solar initiative</p>
                                </div>
                                <div class="p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                    <p class="text-sm font-medium text-gray-800">Performance Alert</p>
                                    <p class="text-xs text-gray-600 mt-1">All investments are performing above market average - excellent portfolio management</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 10: TRANSACTIONS ==================== -->
                <section id="transactions" class="scroll-mt-20" x-show="activeLink === 'transactions'">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Complete Transaction History</h2>
                        <p class="text-sm text-gray-500 mt-1">Comprehensive financial activity tracking and analysis</p>
                    </div>

                    <!-- Transaction Analytics Dashboard -->
                    <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 rounded-xl shadow-lg border border-blue-200 p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-chart-bar text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Transaction Analytics</h3>
                                    <p class="text-sm text-gray-600">Real-time financial activity insights and patterns</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">This Month</p>
                                <p class="text-lg font-bold text-blue-600">47 Transactions</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-arrow-down text-green-600 mr-2"></i>Total Inflows
                                </h4>
                                <p class="text-2xl font-bold text-green-600 mb-1">UGX 2.8M</p>
                                <p class="text-xs text-gray-600">Deposits, dividends, and loan disbursements this month</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-arrow-up text-red-600 mr-2"></i>Total Outflows
                                </h4>
                                <p class="text-2xl font-bold text-red-600 mb-1">UGX 1.2M</p>
                                <p class="text-xs text-gray-600">Withdrawals, loan payments, and investment contributions</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-exchange-alt text-purple-600 mr-2"></i>Net Flow
                                </h4>
                                <p class="text-2xl font-bold text-purple-600 mb-1">+UGX 1.6M</p>
                                <p class="text-xs text-gray-600">Positive cash flow indicating healthy financial growth</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-chart-line text-orange-600 mr-2"></i>Avg. Transaction
                                </h4>
                                <p class="text-2xl font-bold text-orange-600 mb-1">UGX 85K</p>
                                <p class="text-xs text-gray-600">Average transaction size showing consistent activity</p>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Categories Overview -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Transaction Breakdown -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-pie-chart text-blue-600 mr-2"></i>Transaction Breakdown
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                                        <span class="text-sm font-medium">Deposits & Savings</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-green-600">45%</span>
                                        <p class="text-xs text-gray-500">21 transactions</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                                        <span class="text-sm font-medium">Investments</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-blue-600">25%</span>
                                        <p class="text-xs text-gray-500">12 transactions</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                                        <span class="text-sm font-medium">Dividends</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-purple-600">20%</span>
                                        <p class="text-xs text-gray-500">9 transactions</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-orange-500 rounded-full"></div>
                                        <span class="text-sm font-medium">Loans & Payments</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-orange-600">10%</span>
                                        <p class="text-xs text-gray-500">5 transactions</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity Summary -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-clock text-purple-600 mr-2"></i>Recent Activity Highlights
                            </h3>
                            <div class="space-y-3">
                                <div class="p-3 bg-green-50 rounded-lg border-l-4 border-green-500">
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-medium text-gray-800">Largest Deposit</p>
                                        <span class="text-sm font-bold text-green-600">UGX 500,000</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">Monthly savings contribution - Dec 15</p>
                                </div>
                                <div class="p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-medium text-gray-800">Investment Contribution</p>
                                        <span class="text-sm font-bold text-blue-600">UGX 300,000</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">Solar Power Initiative - Dec 10</p>
                                </div>
                                <div class="p-3 bg-purple-50 rounded-lg border-l-4 border-purple-500">
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-medium text-gray-800">Dividend Received</p>
                                        <span class="text-sm font-bold text-purple-600">UGX 35,000</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">Q4 2024 dividend payment - Dec 5</p>
                                </div>
                                <div class="p-3 bg-orange-50 rounded-lg border-l-4 border-orange-500">
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-medium text-gray-800">Loan Payment</p>
                                        <span class="text-sm font-bold text-orange-600">UGX 45,000</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">Monthly installment - Dec 1</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-4 md:p-6 border-b border-gray-100">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Detailed Transaction History</h3>
                                    <p class="text-sm text-gray-500">Complete record of all financial activities</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <select class="px-3 py-2 border rounded-lg text-sm">
                                        <option value="">All Types</option>
                                        <option value="deposit">Deposits</option>
                                        <option value="withdrawal">Withdrawals</option>
                                        <option value="loan">Loans</option>
                                        <option value="dividend">Dividends</option>
                                        <option value="investment">Investments</option>
                                    </select>
                                    <select class="px-3 py-2 border rounded-lg text-sm">
                                        <option value="">All Status</option>
                                        <option value="completed">Completed</option>
                                        <option value="pending">Pending</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                    <input type="date" class="px-3 py-2 border rounded-lg text-sm" placeholder="From Date">
                                    <input type="date" class="px-3 py-2 border rounded-lg text-sm" placeholder="To Date">
                                </div>
                                <div class="flex items-center space-x-3">
                                    <button @click="toggleReverseOrder('transactions')"
                                            :class="reverseOrderState.transactions ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-3 py-2 rounded text-xs transition" title="Reverse Order">
                                        <i class="fas fa-sort-numeric-alt mr-1"></i>
                                        <span x-text="reverseOrderState.transactions ? 'Oldest First' : 'Newest First'"></span>
                                    </button>
                                    <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                        <i class="fas fa-download mr-2"></i>Export CSV
                                    </button>
                                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                        <i class="fas fa-file-pdf mr-2"></i>PDF Report
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-blue-600">TXN-2024-047</td>
                                        <td class="px-4 py-3 text-sm">
                                            <div>
                                                <p class="font-medium">Dec 15, 2024</p>
                                                <p class="text-xs text-gray-500">2:30 PM</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 flex items-center w-fit">
                                                <i class="fas fa-plus mr-1"></i>Deposit
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">Monthly savings contribution</td>
                                        <td class="px-4 py-3 text-sm font-medium text-green-600">+UGX 500,000</td>
                                        <td class="px-4 py-3 text-sm font-medium">UGX 1,500,000</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center space-x-2">
                                                <button class="text-blue-600 hover:text-blue-800 text-xs" title="View Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </button>
                                                <button class="text-green-600 hover:text-green-800 text-xs" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-blue-600">TXN-2024-046</td>
                                        <td class="px-4 py-3 text-sm">
                                            <div>
                                                <p class="font-medium">Dec 10, 2024</p>
                                                <p class="text-xs text-gray-500">10:15 AM</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800 flex items-center w-fit">
                                                <i class="fas fa-coins mr-1"></i>Dividend
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">Q4 2024 dividend payment</td>
                                        <td class="px-4 py-3 text-sm font-medium text-green-600">+UGX 35,000</td>
                                        <td class="px-4 py-3 text-sm font-medium">UGX 1,035,000</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center space-x-2">
                                                <button class="text-blue-600 hover:text-blue-800 text-xs" title="View Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </button>
                                                <button class="text-green-600 hover:text-green-800 text-xs" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-blue-600">TXN-2024-045</td>
                                        <td class="px-4 py-3 text-sm">
                                            <div>
                                                <p class="font-medium">Dec 5, 2024</p>
                                                <p class="text-xs text-gray-500">4:45 PM</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 flex items-center w-fit">
                                                <i class="fas fa-chart-line mr-1"></i>Investment
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">Solar Power Initiative contribution</td>
                                        <td class="px-4 py-3 text-sm font-medium text-red-600">-UGX 300,000</td>
                                        <td class="px-4 py-3 text-sm font-medium">UGX 1,000,000</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center space-x-2">
                                                <button class="text-blue-600 hover:text-blue-800 text-xs" title="View Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </button>
                                                <button class="text-green-600 hover:text-green-800 text-xs" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-blue-600">TXN-2024-044</td>
                                        <td class="px-4 py-3 text-sm">
                                            <div>
                                                <p class="font-medium">Dec 1, 2024</p>
                                                <p class="text-xs text-gray-500">9:20 AM</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800 flex items-center w-fit">
                                                <i class="fas fa-hand-holding-usd mr-1"></i>Loan Payment
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">Monthly loan installment LN-2024-001</td>
                                        <td class="px-4 py-3 text-sm font-medium text-red-600">-UGX 45,000</td>
                                        <td class="px-4 py-3 text-sm font-medium">UGX 1,300,000</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center space-x-2">
                                                <button class="text-blue-600 hover:text-blue-800 text-xs" title="View Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </button>
                                                <button class="text-green-600 hover:text-green-800 text-xs" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-blue-600">TXN-2024-043</td>
                                        <td class="px-4 py-3 text-sm">
                                            <div>
                                                <p class="font-medium">Nov 28, 2024</p>
                                                <p class="text-xs text-gray-500">11:30 AM</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 flex items-center w-fit">
                                                <i class="fas fa-minus mr-1"></i>Withdrawal
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">Emergency fund withdrawal</td>
                                        <td class="px-4 py-3 text-sm font-medium text-red-600">-UGX 100,000</td>
                                        <td class="px-4 py-3 text-sm font-medium">UGX 1,345,000</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center space-x-2">
                                                <button class="text-blue-600 hover:text-blue-800 text-xs" title="View Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </button>
                                                <button class="text-green-600 hover:text-green-800 text-xs" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                            <span class="text-sm text-gray-600">Showing 5 of 47 transactions this month</span>
                            <div class="flex items-center space-x-4">
                                <div class="text-sm">
                                    <span class="font-medium">Total Inflow: </span>
                                    <span class="text-green-600 font-bold">UGX 2,835,000</span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium">Total Outflow: </span>
                                    <span class="text-red-600 font-bold">UGX 1,245,000</span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium">Net Flow: </span>
                                    <span class="text-blue-600 font-bold">+UGX 1,590,000</span>
                                </div>
                            </div>
                            <!-- Pagination -->
                            <div class="flex items-center space-x-2">
                                <button class="px-3 py-1 border rounded-lg text-sm disabled:opacity-50 bg-gray-100">Previous</button>
                                <span class="text-sm text-gray-600">Page 1 of 10</span>
                                <button class="px-3 py-1 border rounded-lg text-sm hover:bg-gray-50">Next</button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== SECTION 11: DOCUMENTS ==================== -->
                <section id="documents" class="scroll-mt-20" x-show="activeLink === 'documents'">
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

                <!-- ==================== SECTION 12: SETTINGS ==================== -->
                <section id="settings" class="scroll-mt-20" x-show="activeLink === 'settings'">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-gray-100">Settings</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your account preferences and system settings</p>
                    </div>

                    <div class="space-y-6">
                        <!-- Appearance Settings -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                                    <i class="fas fa-palette text-purple-600 mr-3"></i>Appearance
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Customize your dashboard appearance</p>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Dark Mode Toggle -->
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-gray-600 to-gray-800 rounded-full flex items-center justify-center">
                                                <i class="fas fa-moon text-white"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-800 dark:text-gray-100">Dark Mode</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Switch between light and dark themes</p>
                                            </div>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="darkMode" @change="toggleDarkMode()" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Settings -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                                    <i class="fas fa-user-cog text-blue-600 mr-3"></i>Account Settings
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your account information and security</p>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-key text-white"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-800 dark:text-gray-100">Change Password</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Update your account password</p>
                                            </div>
                                        </div>
                                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                            Change
                                        </button>
                                    </div>
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-shield-alt text-white"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-800 dark:text-gray-100">Two-Factor Authentication</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Add extra security to your account</p>
                                            </div>
                                        </div>
                                        <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                            Enable
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                                    <i class="fas fa-bell text-yellow-600 mr-3"></i>Notifications
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Control how you receive notifications</p>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-800 dark:text-gray-100">Email Notifications</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Receive updates via email</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" checked class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-800 dark:text-gray-100">SMS Notifications</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Receive updates via SMS</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="notifications" class="scroll-mt-20" x-show="activeLink === 'notifications'">
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
                <section id="settings" class="scroll-mt-20 pb-8" x-show="activeLink === 'settings'">
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

    <!-- ==================== FOOTER ==================== -->
    <footer class="bg-slate-900 text-white py-12 main-content" :class="sidebarCollapsed ? 'sidebar-collapsed' : ''">
        <div class="px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="{{ asset('bunya logo.jpg') }}" alt="BSS Logo" class="h-10 w-auto">
                        <div>
                            <h3 class="text-xl font-bold">BSS Investment Group</h3>
                            <p class="text-sm text-blue-200">Building wealth together since 2020</p>
                        </div>
                    </div>
                    <p class="text-gray-300 text-sm mb-4">Your trusted partner in investment management, providing secure and profitable opportunities for sustainable financial growth.</p>
                    <div class="flex space-x-4">
                        <i class="fab fa-facebook-f text-blue-400 hover:text-blue-300 cursor-pointer transition text-lg"></i>
                        <i class="fab fa-twitter text-blue-400 hover:text-blue-300 cursor-pointer transition text-lg"></i>
                        <i class="fab fa-linkedin-in text-blue-400 hover:text-blue-300 cursor-pointer transition text-lg"></i>
                        <i class="fab fa-instagram text-blue-400 hover:text-blue-300 cursor-pointer transition text-lg"></i>
                        <i class="fab fa-youtube text-blue-400 hover:text-blue-300 cursor-pointer transition text-lg"></i>
                        <i class="fab fa-whatsapp text-blue-400 hover:text-blue-300 cursor-pointer transition text-lg"></i>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-blue-200">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#overview" class="text-gray-300 hover:text-white transition">Portfolio</a></li>
                        <li><a href="#loans" class="text-gray-300 hover:text-white transition">Loans</a></li>
                        <li><a href="#investments" class="text-gray-300 hover:text-white transition">Investments</a></li>
                        <li><a href="#dividends" class="text-gray-300 hover:text-white transition">Dividends</a></li>
                        <li><a href="#members" class="text-gray-300 hover:text-white transition">Members</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-blue-200">Contact & Support</h4>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li class="flex items-center"><i class="fas fa-envelope mr-2 text-blue-400"></i>info@bss-investment.com</li>
                        <li class="flex items-center"><i class="fas fa-phone mr-2 text-blue-400"></i>+256 700 123 456</li>
                        <li class="flex items-center"><i class="fas fa-map-marker-alt mr-2 text-blue-400"></i>Kampala, Uganda</li>
                        <li class="flex items-center"><i class="fas fa-globe mr-2 text-blue-400"></i>www.bss-investment.com</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-400">© 2024 BSS Investment Group. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-sm text-gray-400 hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="text-sm text-gray-400 hover:text-white transition">Terms of Service</a>
                    <a href="#" class="text-sm text-gray-400 hover:text-white transition">Support</a>
                </div>
            </div>
        </div>
    </footer>
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

    <!-- Calendar Modal -->
    <div x-show="showCalendarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800">Calendar</h3>
                <button @click="showCalendarModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="text-center mb-4">
                    <h4 class="text-lg font-semibold text-blue-600" x-text="new Date().toLocaleDateString('en-US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'})"></h4>
                    <p class="text-sm text-gray-500" x-text="new Date().toLocaleTimeString()"></p>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-gray-500 mb-2">
                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center text-sm">
                    <template x-for="day in Array.from({length: 31}, (_, i) => i + 1)" :key="day">
                        <button class="p-2 hover:bg-blue-100 rounded" :class="day === new Date().getDate() ? 'bg-blue-600 text-white' : 'text-gray-700'" x-text="day"></button>
                    </template>
                </div>
                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm font-medium text-blue-800">Upcoming Events</p>
                    <p class="text-xs text-gray-600 mt-1">• Shareholder Meeting - Dec 20, 2024</p>
                    <p class="text-xs text-gray-600">• Dividend Payment - Dec 25, 2024</p>
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
                <!-- ==================== SECTION 12: SETTINGS ==================== -->
                <section id="settings" class="scroll-mt-20" x-show="activeLink === 'settings'">
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-gray-100">Settings</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your account preferences and system settings</p>
                    </div>

                    <div class="space-y-6">
                        <!-- Appearance Settings -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                                    <i class="fas fa-palette text-purple-600 mr-3"></i>Appearance
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Customize your dashboard appearance</p>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Dark Mode Toggle -->
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-gray-600 to-gray-800 rounded-full flex items-center justify-center">
                                                <i class="fas fa-moon text-white"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-800 dark:text-gray-100">Dark Mode</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Switch between light and dark themes</p>
                                            </div>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="darkMode" @change="toggleDarkMode()" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>