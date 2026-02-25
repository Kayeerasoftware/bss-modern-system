<aside class="sidebar w-36 bg-gradient-to-b from-purple-50 to-pink-100 dark:from-gray-800 dark:to-gray-900 border-r border-purple-200 dark:border-gray-700 fixed left-0 top-12 h-[calc(100vh-3rem)] overflow-hidden transition-all duration-300 lg:translate-x-0 z-10 flex flex-col"
       :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'collapsed' : '']"
       id="sidebar">

    <!-- Fixed Profile Section -->
    <div class="pt-1 px-2 profile-section">
        <div class="p-2 border-b border-purple-200">
            <div class="flex flex-col items-center py-1">
                <div class="w-24 h-24 bg-purple-100 flex items-center justify-center mb-2 overflow-hidden cursor-pointer transition-all duration-300" 
                     :class="sidebarCollapsed ? 'rounded-full' : 'rounded-lg'" 
                     @click="showProfileModal = true; sidebarOpen = false">
                    <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover transition-all duration-300" 
                         :class="sidebarCollapsed ? 'rounded-full' : 'rounded-lg'">
                    <i x-show="!profilePicture" class="fas fa-chart-pie text-purple-600 text-2xl"></i>
                </div>
                <p class="text-xs font-semibold text-gray-800 text-center" x-text="adminProfile.name">{{ auth()->user()->name ?? 'Shareholder' }}</p>
                <p class="text-[10px] text-gray-500" x-text="adminProfile.role">{{ ucfirst(auth()->user()->role ?? 'shareholder') }}</p>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-2 relative" :class="sidebarCollapsed ? 'flex justify-center' : ''" x-data="{ search: '' }">
                <input x-show="!sidebarCollapsed" type="text" 
                       x-model="search" 
                       @input="document.querySelectorAll('#sidebarNav .nav-item').forEach(item => {
                           const text = item.getAttribute('data-search');
                           item.style.display = text && text.toLowerCase().includes(search.toLowerCase()) ? 'flex' : 'none';
                       }); if(search === '') document.querySelectorAll('#sidebarNav .nav-item').forEach(item => item.style.display = 'flex');"
                       placeholder="Search menu..."
                       class="w-full px-2 py-1.5 pl-7 text-xs border border-purple-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-white">
                <i x-show="!sidebarCollapsed" class="fas fa-search absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                <button x-show="sidebarCollapsed" @click="sidebarCollapsed = false" class="w-8 h-8 flex items-center justify-center bg-white border border-purple-200 rounded-lg hover:bg-purple-50 transition">
                    <i class="fas fa-search text-gray-600 text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Scrollable Content -->
    <div class="flex-1 scrollable-content">
        <div class="p-2 h-full overflow-y-auto">
        <nav class="space-y-1 sidebar-nav" id="sidebarNav">
            <a href="{{ route('shareholder.dashboard') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs {{ request()->routeIs('shareholder.dashboard') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600' }}" data-search="dashboard">
                <i class="fas fa-tachometer-alt w-3 text-xs"></i><span>Dashboard</span>
            </a>
            @if(\App\Models\Setting::get('shareholder_members_access', 1) == 1)
            <a href="{{ route('shareholder.members') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs {{ request()->routeIs('shareholder.members') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600' }}" data-search="members">
                <i class="fas fa-users w-3 text-xs"></i><span>Members</span>
            </a>
            @endif
            <a href="{{ route('shareholder.loans') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs {{ request()->routeIs('shareholder.loans') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600' }}" data-search="loans">
                <i class="fas fa-money-bill-wave w-3 text-xs"></i><span>Loans</span>
            </a>
            <a href="{{ route('shareholder.investments.index') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs {{ request()->routeIs('shareholder.investments.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600' }}" data-search="investments">
                <i class="fas fa-chart-line w-3 text-xs"></i><span>Investments</span>
            </a>
            <a href="{{ route('shareholder.dividends.index') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs {{ request()->routeIs('shareholder.dividends.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600' }}" data-search="dividends">
                <i class="fas fa-hand-holding-usd w-3 text-xs"></i><span>Dividends</span>
            </a>
            <a href="{{ route('shareholder.transactions') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs {{ request()->routeIs('shareholder.transactions') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600' }}" data-search="transactions">
                <i class="fas fa-exchange-alt w-3 text-xs"></i><span>Transactions</span>
            </a>
            <a href="{{ route('shareholder.financial') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs {{ request()->routeIs('shareholder.financial') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600' }}" data-search="financial">
                <i class="fas fa-dollar-sign w-3 text-xs"></i><span>Financial</span>
            </a>
            <a href="{{ route('shareholder.projects.index') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs {{ request()->routeIs('shareholder.projects.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600' }}" data-search="projects">
                <i class="fas fa-project-diagram w-3 text-xs"></i><span>Projects</span>
            </a>
            <a href="{{ route('shareholder.users') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs {{ request()->routeIs('shareholder.users') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600' }}" data-search="users">
                <i class="fas fa-user-shield w-3 text-xs"></i><span>Users</span>
            </a>
            <a href="{{ route('shareholder.settings') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs {{ request()->routeIs('shareholder.settings') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600' }}" data-search="settings">
                <i class="fas fa-cog w-3 text-xs"></i><span>Settings</span>
            </a>
            <a href="{{ route('shareholder.reports.index') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs {{ request()->routeIs('shareholder.reports.*') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600' }}" data-search="reports">
                <i class="fas fa-chart-bar w-3 text-xs"></i><span>Reports</span>
            </a>
            <a href="{{ route('shareholder.notifications') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs {{ request()->routeIs('shareholder.notifications') ? 'bg-purple-100 text-purple-700' : 'text-gray-700 hover:bg-pink-50 hover:text-pink-600' }}" data-search="notifications">
                <i class="fas fa-bell w-3 text-xs"></i><span>Notifications</span>
            </a>
        </nav>

        <!-- Quick Actions -->
        <div class="mt-4 pt-4 border-t border-purple-200">
            <p class="px-2 text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Quick Actions</p>
            <nav class="space-y-1">
                <button @click="showChatModal = true; sidebarOpen = false"
                        class="w-full flex items-center space-x-2 px-2 py-2 rounded-lg text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition text-xs text-left">
                    <i class="fas fa-headset text-purple-500 w-3"></i><span>Get Support</span>
                </button>
                <a href="{{ route('shareholder.profile') }}" class="nav-item flex items-center space-x-2 px-2 py-2 rounded-lg transition text-xs text-gray-700 hover:bg-pink-50 hover:text-pink-600">
                    <i class="fas fa-user w-3 text-xs"></i><span>Profile</span>
                </a>
            </nav>
        </div>
        <div class="pb-4"></div>
    </div>
    </div>

    <!-- Chat Button -->
    <div class="p-2 border-t border-purple-200">
        <button @click="showChatModal = true" class="w-full px-2 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-xs flex items-center justify-center space-x-1">
            <i class="fas fa-comments"></i><span>Chat</span>
        </button>
    </div>
</aside>

<!-- Profile Modal -->
<div x-show="showProfileModal" 
     x-cloak
     @click.self="showProfileModal = false"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     style="display: none;">
    <div @click.away="showProfileModal = false" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="max-w-3xl w-full max-h-[90vh] overflow-y-auto relative">
        
        <!-- Close Button -->
        <button @click="showProfileModal = false" 
                class="absolute top-4 right-4 w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-all shadow-lg z-10">
            <i class="fas fa-times"></i>
        </button>

        <!-- Large Profile Picture -->
        <div class="flex justify-center">
            <div class="w-80 h-80 rounded-3xl bg-gradient-to-br from-purple-100 to-pink-100 shadow-2xl overflow-hidden">
                <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover">
                <div x-show="!profilePicture" class="w-full h-full bg-gradient-to-br from-purple-200 to-pink-200 flex items-center justify-center">
                    <i class="fas fa-chart-pie text-purple-600 text-9xl"></i>
                </div>
            </div>
        </div>

        <!-- Name and Role -->
        <div class="text-center px-6 py-3">
            <h2 class="text-xl font-bold text-white drop-shadow-lg mb-1" x-text="adminProfile.name">Shareholder Name</h2>
            <p class="text-sm text-white/90 drop-shadow-lg font-semibold" x-text="adminProfile.role">Shareholder</p>
        </div>

        <!-- Member Details -->
        <div class="flex justify-center">
            <div class="w-80 space-y-2">
                <div class="grid grid-cols-2 gap-2">
                    <!-- Email -->
                    <div class="flex items-center gap-1 p-2 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-lg">
                        <div class="w-6 h-6 bg-purple-600 rounded flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-white text-[10px]"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[8px] font-semibold text-gray-500 uppercase leading-tight">Email</p>
                            <p class="text-[10px] font-bold text-gray-800 truncate leading-tight" x-text="adminProfile.email">shareholder@example.com</p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="flex items-center gap-1 p-2 bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg shadow-lg">
                        <div class="w-6 h-6 bg-pink-600 rounded flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-white text-[10px]"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[8px] font-semibold text-gray-500 uppercase leading-tight">Phone</p>
                            <p class="text-[10px] font-bold text-gray-800 truncate leading-tight" x-text="adminProfile.phone">+256 XXX</p>
                        </div>
                    </div>

                    <!-- Member ID -->
                    <div class="flex items-center gap-1 p-2 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg shadow-lg">
                        <div class="w-6 h-6 bg-indigo-600 rounded flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-id-card text-white text-[10px]"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[8px] font-semibold text-gray-500 uppercase leading-tight">ID</p>
                            <p class="text-[10px] font-bold text-gray-800 truncate leading-tight" x-text="adminProfile.member_id">MEM-XXXX</p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center gap-1 p-2 bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-lg">
                        <div class="w-6 h-6 bg-green-600 rounded flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-circle-check text-white text-[10px]"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[8px] font-semibold text-gray-500 uppercase leading-tight">Status</p>
                            <p class="text-[10px] font-bold text-green-600 leading-tight">Active</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-1">
                    <a href="{{ route('shareholder.profile') }}" @click="showProfileModal = false" 
                            class="flex-1 px-3 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg font-semibold text-xs text-center">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <button @click="showProfileModal = false" 
                            class="px-3 py-2 bg-white/90 text-gray-700 rounded-lg hover:bg-white transition-all font-semibold text-xs shadow-lg">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<button class="sidebar-toggle" 
        :class="{'hidden-mobile': !sidebarOpen}"
        @click="sidebarCollapsed = !sidebarCollapsed"
        :style="{
            left: sidebarCollapsed ? 'calc(80px - 10px)' : 'calc(144px - 10px)'
        }">
    <i class="fas fa-chevron-left" 
       :style="{
           transform: sidebarCollapsed ? 'rotate(180deg)' : 'rotate(0deg)'
       }"></i>
</button>
