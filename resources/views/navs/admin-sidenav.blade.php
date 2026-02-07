<aside class="sidebar w-36 bg-gradient-to-b from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 border-r border-blue-200 dark:border-gray-700 fixed left-0 top-12 h-[calc(100vh-3rem)] overflow-hidden transition-all duration-300 lg:translate-x-0 z-10 flex flex-col"
       :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'collapsed' : '']"
       id="sidebar">

    <!-- Fixed Profile Section -->
    <div class="p-2 profile-section">
        <div class="p-2 border-b border-blue-200">
            <div class="flex flex-col items-center py-3">
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-2 overflow-hidden cursor-pointer" @click="showProfileModal = true">
                    <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover">
                    <i x-show="!profilePicture" class="fas fa-shield-alt text-blue-600 text-2xl"></i>
                </div>
                <p class="text-xs font-semibold text-gray-800 text-center" x-text="adminProfile.name">Admin</p>
                <p class="text-[10px] text-gray-500" x-text="adminProfile.role">Administrator</p>
            </div>
        </div>
    </div>

    <!-- Scrollable Content -->
    <div class="flex-1 scrollable-content">
        <div class="p-2 h-full overflow-y-auto">
        <nav class="space-y-1 sidebar-nav">
            <a href="#stats" @click="sidebarOpen = false; activeLink = 'stats'" :class="activeLink === 'stats' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-tachometer-alt w-3 text-xs"></i><span>Dashboard</span>
            </a>
            <a href="#members" @click="sidebarOpen = false; activeLink = 'members'" :class="activeLink === 'members' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-users w-3 text-xs"></i><span>Members</span>
            </a>
            <a href="#loans" @click="sidebarOpen = false; activeLink = 'loans'" :class="activeLink === 'loans' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-money-bill-wave w-3 text-xs"></i><span>Loans</span>
            </a>
            <a href="#loan-requests" @click="sidebarOpen = false; activeLink = 'loan-requests'" :class="activeLink === 'loan-requests' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-file-invoice-dollar w-3 text-xs"></i><span>Loan Requests</span>
            </a>
            <a href="#fundraising" @click="sidebarOpen = false; activeLink = 'fundraising'" :class="activeLink === 'fundraising' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-hand-holding-heart w-3 text-xs"></i><span>Fundraising</span>
            </a>
            <a href="#transactions" @click="sidebarOpen = false; activeLink = 'transactions'" :class="activeLink === 'transactions' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-exchange-alt w-3 text-xs"></i><span>Transactions</span>
            </a>
            <a href="#financial" @click="sidebarOpen = false; activeLink = 'financial'" :class="activeLink === 'financial' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-dollar-sign w-3 text-xs"></i><span>Financial</span>
            </a>            
            <a href="#projects" @click="sidebarOpen = false; activeLink = 'projects'" :class="activeLink === 'projects' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-project-diagram w-3 text-xs"></i><span>Projects</span>
            </a>
            <a href="#users" @click="sidebarOpen = false; activeLink = 'users'" :class="activeLink === 'users' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-user-shield w-3 text-xs"></i><span>Users</span>
            </a>
            <a href="#settings" @click="sidebarOpen = false; activeLink = 'settings'" :class="activeLink === 'settings' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-cog w-3 text-xs"></i><span>Settings</span>
            </a>
            <a href="#reports" @click="sidebarOpen = false; activeLink = 'reports'" :class="activeLink === 'reports' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-chart-bar w-3 text-xs"></i><span>Reports</span>
            </a>
            <a href="#notifications" @click="sidebarOpen = false; activeLink = 'notifications'" :class="activeLink === 'notifications' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-bell w-3 text-xs"></i><span>Notifications</span>
            </a>
            <a href="#backup" @click="sidebarOpen = false; activeLink = 'backup'" :class="activeLink === 'backup' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-database w-3 text-xs"></i><span>Backup</span>
            </a>
            <a href="#bulk" @click="sidebarOpen = false; activeLink = 'bulk'" :class="activeLink === 'bulk' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-tasks w-3 text-xs"></i><span>Bulk Ops</span>
            </a>
            <a href="#health" @click="sidebarOpen = false; activeLink = 'health'" :class="activeLink === 'health' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-heartbeat w-3 text-xs"></i><span>System Health</span>
            </a>
            <a href="#permissions" @click="sidebarOpen = false; activeLink = 'permissions'" :class="activeLink === 'permissions' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-lock w-3 text-xs"></i><span>Permissions</span>
            </a>
            <a href="#audit" @click="sidebarOpen = false; activeLink = 'audit'" :class="activeLink === 'audit' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs">
                <i class="fas fa-history w-3 text-xs"></i><span>Audit Log</span>
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
