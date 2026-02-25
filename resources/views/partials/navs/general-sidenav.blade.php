<aside class="sidebar w-36 bg-gradient-to-b from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 border-r border-blue-200 dark:border-gray-700 fixed left-0 top-12 h-[calc(100vh-3rem)] overflow-hidden transition-all duration-300 lg:translate-x-0 z-10 flex flex-col"
       :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'collapsed' : '']"
       id="sidebar">

    <!-- Fixed Profile Section -->
    <div class="p-2 profile-section">
        <div class="p-2 border-b border-blue-200">
            <div class="flex flex-col items-center py-3">
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-2 overflow-hidden cursor-pointer" @click="showProfileViewModal = true">
                    <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover">
                    <i x-show="!profilePicture" class="fas fa-user text-blue-600 text-2xl"></i>
                </div>
                <p class="text-xs font-semibold text-gray-800 text-center">John Doe</p>
                <p class="text-[10px] text-gray-500">Shareholder</p>
            </div>
        </div>
        
        <div class="nav-item search-item relative">
            <i class="fas fa-search text-gray-500 text-xs" @click="if(sidebarCollapsed) { sidebarCollapsed = false; }"></i>
            <input type="text" x-model="sidebarSearch" placeholder="Search menu..." class="flex-1 bg-transparent border-none outline-none text-xs text-gray-700 placeholder-gray-500 ml-2">
            <button x-show="sidebarSearch" @click="sidebarSearch = ''" class="text-gray-400 hover:text-gray-600 transition ml-2">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Scrollable Content -->
    <div class="flex-1 scrollable-content">
        <div class="p-2 h-full overflow-y-auto">
        <nav class="space-y-1 sidebar-nav">

            <a href="#overview" @click="sidebarOpen = false; activeLink = 'overview'" :class="activeLink === 'overview' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-home w-3 text-xs"></i><span>Overview</span>
            </a>
            <a href="#portfolio" @click="sidebarOpen = false; activeLink = 'portfolio'" :class="activeLink === 'portfolio' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-briefcase w-3 text-xs"></i><span>Portfolio</span>
            </a>
            <a href="#insights" @click="sidebarOpen = false; activeLink = 'insights'" :class="activeLink === 'insights' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-lightbulb w-3 text-xs"></i><span>Insights</span>
            </a>
            <a href="#loans" @click="sidebarOpen = false; activeLink = 'loans'" :class="activeLink === 'loans' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-hand-holding-usd w-3 text-xs"></i><span>My Loans</span>
            </a>
            <a href="#members" @click="sidebarOpen = false; activeLink = 'members'" :class="activeLink === 'members' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-users w-3 text-xs"></i><span>Members</span>
            </a>
            <a href="#profile" @click="sidebarOpen = false; activeLink = 'profile'" :class="activeLink === 'profile' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-user w-3 text-xs"></i><span>My Profile</span>
            </a>
            <a href="#savings" @click="sidebarOpen = false; activeLink = 'savings'" :class="activeLink === 'savings' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-piggy-bank w-3 text-xs"></i><span>Savings</span>
            </a>
            <a href="#dividends" @click="sidebarOpen = false; activeLink = 'dividends'" :class="activeLink === 'dividends' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-coins w-3 text-xs"></i><span>Dividends</span>
            </a>
            <a href="#investments" @click="sidebarOpen = false; activeLink = 'investments'" :class="activeLink === 'investments' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-chart-line w-3 text-xs"></i><span>Investments</span>
            </a>
            <a href="#transactions" @click="sidebarOpen = false; activeLink = 'transactions'" :class="activeLink === 'transactions' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-exchange-alt w-3 text-xs"></i><span>Transactions</span>
            </a>
            <a href="#documents" @click="sidebarOpen = false; activeLink = 'documents'" :class="activeLink === 'documents' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-file-alt w-3 text-xs"></i><span>Documents</span>
            </a>
            <a href="#notifications" @click="sidebarOpen = false; activeLink = 'notifications'" :class="activeLink === 'notifications' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-bell w-3 text-xs"></i><span>Notifications</span>
                <span class="ml-auto bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full nav-badge">3</span>
            </a>
            <a href="#settings" @click="sidebarOpen = false; activeLink = 'settings'" :class="activeLink === 'settings' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-cog w-3 text-xs"></i><span>Settings</span>
            </a>
        </nav>

        <div class="mt-4 pt-4 border-t border-blue-200">
            <p class="px-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Quick Actions</p>
            <nav class="space-y-1">
                <button @click="showLoanRequestModal = true; sidebarOpen = false" class="w-full nav-item flex items-center space-x-2 px-2 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition text-xs text-left">
                    <i class="fas fa-plus-circle text-green-500 w-3"></i><span>Request Loan</span>
                </button>
                <button @click="showOpportunitiesModal = true; sidebarOpen = false" class="w-full nav-item flex items-center space-x-2 px-2 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition text-xs text-left">
                    <i class="fas fa-rocket text-purple-500 w-3"></i><span>New Investment</span>
                </button>
                <button @click="showChatModal = true; sidebarOpen = false" class="w-full nav-item flex items-center space-x-2 px-2 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition text-xs text-left">
                    <i class="fas fa-headset text-blue-500 w-3"></i><span>Get Support</span>
                </button>
            </nav>
        </div>
        <div class="pb-4"></div>
    </div>
</aside>

<button class="sidebar-toggle hidden lg:block" @click="sidebarCollapsed = !sidebarCollapsed">
    <i class="fas fa-chevron-right"></i>
</button>
