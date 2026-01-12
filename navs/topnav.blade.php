<nav class="topnav fixed top-0 left-0 right-0 h-12 bg-gradient-to-r from-violet-200 via-blue-200 to-cyan-200 dark:from-gray-800 dark:via-gray-700 dark:to-gray-800 backdrop-blur-xl border-b border-violet-300 dark:border-gray-600 z-[1000] shadow-2xl ring-2 ring-gradient-to-r ring-from-violet-400 ring-to-cyan-400 ring-opacity-80 shadow-violet-500/30 dark:shadow-gray-900/30 transition-colors duration-300">
    <div class="nav-container flex items-center justify-between h-full px-6 max-w-full mx-auto">
        <div class="nav-left flex items-center gap-2">
            <button @click="sidebarOpen = !sidebarOpen" class="menu-toggle lg:hidden bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 text-base text-blue-600 cursor-pointer px-2 py-1.5 rounded-lg transition-all duration-300 hover:from-blue-100 hover:to-purple-100 hover:-translate-y-0.5 hover:shadow-md">
                <i class="fas fa-bars"></i>
            </button>
            <div class="logo flex items-center gap-2 font-bold text-sm tracking-tight px-3 py-1.5 bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl border border-blue-200 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:border-blue-300 cursor-pointer" @click="showLogoModal = !showLogoModal" :class="showLogoModal ? 'ring-2 ring-blue-400' : ''">
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
                <div x-show="showProfileDropdown" @click.away="showProfileDropdown = false" x-transition class="dropdown-menu absolute top-full right-0 mt-3 w-48 bg-white border border-blue-200 rounded-lg shadow-xl z-[1000] overflow-hidden">
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

<div x-show="showLogoModal" x-transition @click.away="showLogoModal = false" class="fixed top-12 left-0 right-0 bg-white border-b border-gray-200 shadow-xl z-[1001] p-6">
    <div class="max-w-md mx-auto text-center">
        <img src="{{ asset('bunya logo.jpg') }}" alt="BSS Logo" class="h-16 w-auto object-contain mx-auto mb-3 drop-shadow-lg">
        <h2 class="text-lg font-bold bg-gradient-to-br from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">BSS OB/OG INVESTMENT GROUP SYSTEM</h2>
        <p class="text-sm text-gray-600 mb-1">Your trusted investment partner</p>
        <p class="text-xs text-gray-400">Building wealth together since 2020</p>
    </div>
</div>

<div x-show="showShareholderModal" x-transition @click.away="showShareholderModal = false" class="fixed top-12 left-0 right-0 bg-white border-b border-gray-200 shadow-xl z-[1001] p-6">
    <div class="max-w-md mx-auto text-center">
        <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center">
            <i class="fas fa-chart-line text-white text-2xl"></i>
        </div>
        <h2 class="text-lg font-bold bg-gradient-to-br from-green-600 to-emerald-600 bg-clip-text text-transparent mb-2">Shareholder Dashboard</h2>
        <p class="text-sm text-gray-600 mb-1">Investment portfolio management</p>
        <p class="text-xs text-gray-400">Track your shares, dividends & returns</p>
    </div>
</div>
