<aside class="sidebar w-36 bg-gradient-to-b from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 border-r border-blue-200 dark:border-gray-700 fixed left-0 top-12 h-[calc(100vh-3rem)] overflow-hidden transition-all duration-300 lg:translate-x-0 z-10 flex flex-col"
       :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'collapsed' : '']"
       id="sidebar">

    <!-- Fixed Profile Section -->
    <div class="p-2 profile-section">
        <div class="p-2 border-b border-blue-200">
            <div class="flex flex-col items-center py-3">
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-2 overflow-hidden cursor-pointer" @click="showProfileModal = true">
                    <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover">
                    <i x-show="!profilePicture" class="fas fa-crown text-blue-600 text-2xl"></i>
                </div>
                <p class="text-xs font-semibold text-gray-800 text-center" x-text="adminProfile.name">CEO</p>
                <p class="text-[10px] text-gray-500" x-text="adminProfile.role">Chief Executive Officer</p>
            </div>
        </div>
    </div>

    <!-- Scrollable Content -->
    <div class="flex-1 scrollable-content">
        <div class="p-2 h-full overflow-y-auto">
        <nav class="space-y-1 sidebar-nav">
            <a href="#overview" @click="sidebarOpen = false; activeLink = 'overview'" :class="activeLink === 'overview' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-tachometer-alt w-3 text-xs"></i><span>Overview</span>
            </a>
            <a href="#strategic" @click="sidebarOpen = false; activeLink = 'strategic'" :class="activeLink === 'strategic' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-chart-line w-3 text-xs"></i><span>Strategic</span>
            </a>
            <a href="#kpi" @click="sidebarOpen = false; activeLink = 'kpi'" :class="activeLink === 'kpi' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-chart-bar w-3 text-xs"></i><span>KPIs</span>
            </a>
            <a href="#market" @click="sidebarOpen = false; activeLink = 'market'" :class="activeLink === 'market' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-chart-pie w-3 text-xs"></i><span>Market</span>
            </a>
            <a href="#financial" @click="sidebarOpen = false; activeLink = 'financial'" :class="activeLink === 'financial' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-coins w-3 text-xs"></i><span>Financial</span>
            </a>
            <!-- Members Dropdown -->
            <div class="relative">
                <button @click="dropdowns.members = !dropdowns.members"
                        :class="activeLink === 'member' || activeLink === 'member-management' || activeLink === 'member-accounts' || activeLink === 'member-activity' || activeLink === 'financial-operations' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                        class="nav-item flex items-center justify-between w-full space-x-2 px-2 py-2 rounded-lg transition text-xs">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-users w-3 text-xs"></i><span>Members</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] transition-transform" :class="dropdowns.members ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="dropdowns.members"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="ml-6 mt-1 bg-white border border-blue-200 rounded-lg shadow-lg z-20">
                    <nav class="py-2">
                        <a href="#member-management"
                           @click="sidebarOpen = false; activeLink = 'member-management'; dropdowns.members = false"
                           :class="activeLink === 'member-management' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                           class="flex items-center space-x-2 px-3 py-2 text-xs transition">
                            <i class="fas fa-user-cog w-3 text-[10px]"></i><span>Member Management</span>
                        </a>
                        <a href="#member-accounts"
                           @click="sidebarOpen = false; activeLink = 'member-accounts'; dropdowns.members = false"
                           :class="activeLink === 'member-accounts' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                           class="flex items-center space-x-2 px-3 py-2 text-xs transition">
                            <i class="fas fa-address-card w-3 text-[10px]"></i><span>Member Accounts</span>
                        </a>
                        <a href="#member-activity"
                           @click="sidebarOpen = false; activeLink = 'member-activity'; dropdowns.members = false"
                           :class="activeLink === 'member-activity' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                           class="flex items-center space-x-2 px-3 py-2 text-xs transition">
                            <i class="fas fa-chart-line w-3 text-[10px]"></i><span>Member Activity</span>
                        </a>
                        <a href="#member-segmentation"
                           @click="sidebarOpen = false; activeLink = 'member-segmentation'; dropdowns.members = false"
                           :class="activeLink === 'member-segmentation' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                           class="flex items-center space-x-2 px-3 py-2 text-xs transition">
                            <i class="fas fa-users-cog w-3 text-[10px]"></i><span>Member Segmentation</span>
                        </a>
                        <a href="#financial-operations"
                           @click="sidebarOpen = false; activeLink = 'financial-operations'; dropdowns.members = false"
                           :class="activeLink === 'financial-operations' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
                           class="flex items-center space-x-2 px-3 py-2 text-xs transition">
                            <i class="fas fa-calculator w-3 text-[10px]"></i><span>Financial Operations</span>
                        </a>
                    </nav>
                </div>
            </div>
            <a href="#loans" @click="sidebarOpen = false; activeLink = 'loans'" :class="activeLink === 'loans' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-hand-holding-usd w-3 text-xs"></i><span>Loans</span>
            </a>
            <a href="#reports" @click="sidebarOpen = false; activeLink = 'reports'" :class="activeLink === 'reports' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-file-alt w-3 text-xs"></i><span>Reports</span>
            </a>
            <a href="#settings" @click="sidebarOpen = false; activeLink = 'settings'" :class="activeLink === 'settings' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-cog w-3 text-xs"></i><span>Settings</span>
            </a>
        </nav>

        <!-- Quick Actions -->
        <div class="mt-4 pt-4 border-t border-blue-200">
            <p class="px-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Quick Actions</p>
            <nav class="space-y-1">
                <button @click="showChatModal = true; sidebarOpen = false"
                        class="w-full flex items-center space-x-2 px-2 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition text-xs text-left">
                    <i class="fas fa-headset text-blue-500 w-3"></i><span>Get Support</span>
                </button>
            <a href="#profile" @click="sidebarOpen = false; activeLink = 'profile'" :class="activeLink === 'profile' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-user w-3 text-xs"></i><span>Profile</span>
            </a>
            </nav>
        </div>
        <div class="pb-4"></div>
    </div>
    </div>

    <!-- Chat Button -->
    <div class="p-2 border-t border-blue-200">
        <button @click="showChatModal = true" class="w-full px-2 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-xs flex items-center justify-center space-x-1">
            <i class="fas fa-comments"></i><span>Chat</span>
        </button>
    </div>
</aside>

<button class="sidebar-toggle hidden lg:block" @click="sidebarCollapsed = !sidebarCollapsed">
    <i class="fas fa-chevron-right"></i>
</button>
