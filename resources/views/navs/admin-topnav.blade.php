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
            <div class="flex items-center gap-2 font-bold text-sm tracking-tight px-8 py-1.5 rounded-xl border transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md cursor-pointer hidden lg:flex" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-color: #3b82f6;" @click="showShareholderModal = !showShareholderModal" :class="showShareholderModal ? 'ring-2 ring-blue-400' : ''">
                <div class="bg-white/20 p-1 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-shield-alt text-sm drop-shadow-md" style="color: #2563eb;"></i>
                </div>
                <span class="text-sm font-bold whitespace-nowrap" style="background: linear-gradient(135deg, #2563eb, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Admin Dashboard</span>
            </div>
            <button class="nav-btn relative flex items-center justify-center rounded-xl border transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md px-3 py-1.5 lg:hidden cursor-pointer" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-color: #3b82f6;" title="Admin Dashboard" @click="showShareholderModal = !showShareholderModal" :class="showShareholderModal ? 'ring-2 ring-blue-400' : ''">
                <i class="fas fa-shield-alt drop-shadow-md" style="color: #2563eb;"></i>
            </button>
        </div>

        <div class="nav-right flex items-center gap-2">
            <div class="relative" x-data="{ 
                showCalendar: false, 
                currentTime: new Date(),
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                getDaysInMonth(month, year) {
                    return new Date(year, month + 1, 0).getDate();
                },
                getFirstDayOfMonth(month, year) {
                    return new Date(year, month, 1).getDay();
                },
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                isToday(day) {
                    const today = new Date();
                    return day === today.getDate() && this.currentMonth === today.getMonth() && this.currentYear === today.getFullYear();
                }
            }" x-init="setInterval(() => { currentTime = new Date() }, 1000)">
                <button @click="showCalendar = !showCalendar" class="nav-btn relative flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 px-3 py-1.5 rounded-xl cursor-pointer transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:border-blue-300" title="Calendar" :class="showCalendar ? 'ring-2 ring-blue-400' : ''">
                    <i class="fas fa-calendar-alt text-blue-600 drop-shadow-md"></i>
                    <span class="bg-gradient-to-br from-blue-600 to-purple-600 bg-clip-text text-transparent text-xs font-bold font-mono ml-2 hidden md:inline" x-text="currentTime.toLocaleDateString() + ' ' + currentTime.toLocaleTimeString()"></span>
                </button>
                <div x-show="showCalendar" @click.away="showCalendar = false" x-transition class="fixed sm:absolute top-16 sm:top-full left-1/2 -translate-x-1/2 sm:left-auto sm:right-0 sm:translate-x-0 mt-0 sm:mt-3 w-[320px] bg-white rounded-xl shadow-2xl z-[1000] overflow-hidden border-2 border-blue-100">
                    <div class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-between">
                        <button @click="currentMonth = currentMonth === 0 ? (currentYear--, 11) : currentMonth - 1" class="text-white hover:bg-white/20 rounded-lg px-2 py-1 transition"><i class="fas fa-chevron-left text-xs"></i></button>
                        <h3 class="text-sm font-bold text-white" x-text="monthNames[currentMonth] + ' ' + currentYear"></h3>
                        <button @click="currentMonth = currentMonth === 11 ? (currentYear++, 0) : currentMonth + 1" class="text-white hover:bg-white/20 rounded-lg px-2 py-1 transition"><i class="fas fa-chevron-right text-xs"></i></button>
                    </div>
                    <div class="p-3">
                        <div class="grid grid-cols-7 gap-1 mb-2">
                            <div class="text-center text-xs font-bold text-gray-600">Su</div>
                            <div class="text-center text-xs font-bold text-gray-600">Mo</div>
                            <div class="text-center text-xs font-bold text-gray-600">Tu</div>
                            <div class="text-center text-xs font-bold text-gray-600">We</div>
                            <div class="text-center text-xs font-bold text-gray-600">Th</div>
                            <div class="text-center text-xs font-bold text-gray-600">Fr</div>
                            <div class="text-center text-xs font-bold text-gray-600">Sa</div>
                        </div>
                        <div class="grid grid-cols-7 gap-1">
                            <template x-for="blank in getFirstDayOfMonth(currentMonth, currentYear)" :key="'blank-' + blank">
                                <div class="h-9"></div>
                            </template>
                            <template x-for="day in getDaysInMonth(currentMonth, currentYear)" :key="day">
                                <div class="h-9 flex items-center justify-center text-xs rounded-lg cursor-pointer transition" :class="isToday(day) ? 'bg-gradient-to-br from-blue-500 to-purple-500 text-white font-bold shadow-md' : 'hover:bg-blue-50 text-gray-700'" x-text="day"></div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <button @click="activeLink = 'notifications'; document.getElementById('notifications').scrollIntoView({ behavior: 'smooth' })" class="nav-btn relative flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 px-3 py-1.5 rounded-xl cursor-pointer transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:border-blue-300" title="Notifications">
                <i class="fas fa-bell text-blue-600 drop-shadow-md"></i>
                <span x-show="notificationStats.unread > 0" class="badge absolute -top-2 right-0 bg-gradient-to-br from-red-500 to-red-600 text-white text-[0.625rem] font-bold px-1.5 py-0.5 rounded-xl min-w-[1.125rem] text-center shadow-md border-2 border-white animate-pulse" x-text="notificationStats.unread"></span>
            </button>
            <button @click="showMemberChatModal = true" class="nav-btn relative flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 px-3 py-1.5 rounded-xl cursor-pointer transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:border-blue-300" title="Messages">
                <i class="fas fa-envelope text-blue-600 drop-shadow-md"></i>
                <span class="status-dot absolute -top-1 right-1.5 w-2.5 h-2.5 bg-gradient-to-br from-green-500 to-green-600 rounded-full border-2 border-white shadow-md animate-pulse"></span>
            </button>
            <div class="profile relative">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl cursor-pointer transition-all duration-300 bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 hover:-translate-y-0.5 hover:shadow-md hover:border-blue-300" @click="showProfileDropdown = !showProfileDropdown">
                    <span class="bg-gradient-to-br from-blue-600 to-purple-600 bg-clip-text text-transparent text-xs font-bold tracking-tight hidden lg:inline" x-text="'Welcome back, ' + adminProfile.name">Welcome back, Admin</span>
                    <i class="fas fa-chevron-down dropdown-arrow transition-all duration-300 text-blue-600 text-xs drop-shadow-md hidden lg:inline" :class="showProfileDropdown ? 'rotate-180' : ''"></i>
                    <div class="profile-avatar relative">
                        <img :src="profilePicture || 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face'" alt="Profile" class="w-6 h-6 rounded-full object-cover border-2 border-green-500 shadow-md transition-all duration-300">
                        <div class="status-indicator online absolute bottom-0 right-0 w-2 h-2 rounded-full border-2 border-white bg-gradient-to-br from-green-500 to-green-600 shadow-md animate-pulse"></div>
                    </div>
                </div>
                <div x-show="showProfileDropdown" @click.away="showProfileDropdown = false" x-transition class="dropdown-menu absolute top-full right-0 mt-3 w-48 bg-white border border-blue-200 rounded-lg shadow-xl z-[1000] overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 text-center">
                        <div class="w-12 h-12 rounded-full bg-gray-300 mx-auto mb-2 overflow-hidden">
                            <img :src="profilePicture || 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=48&h=48&fit=crop&crop=face'" alt="Profile" class="w-full h-full object-cover">
                        </div>
                        <p class="text-sm font-semibold text-gray-800" x-text="adminProfile.name">Admin</p>
                        <p class="text-xs text-gray-500" x-text="adminProfile.role">Administrator</p>
                    </div>
                    <a href="#profile" @click="showProfileDropdown = false; activeLink = 'profile'; document.getElementById('profile').scrollIntoView({ behavior: 'smooth' })" class="dropdown-item flex items-center gap-2 px-3 py-2 text-gray-800 no-underline transition-all duration-200 bg-transparent border-0 w-full text-left cursor-pointer text-xs font-medium relative hover:bg-gradient-to-br hover:from-blue-50 hover:to-purple-50 hover:text-blue-600">
                        <i class="fas fa-user text-blue-600 text-xs w-3 text-center"></i> Profile
                    </a>
                    <a href="#settings" @click="showProfileDropdown = false; activeLink = 'settings'; document.getElementById('settings').scrollIntoView({ behavior: 'smooth' })" class="dropdown-item flex items-center gap-2 px-3 py-2 text-gray-800 no-underline transition-all duration-200 bg-transparent border-0 w-full text-left cursor-pointer text-xs font-medium relative hover:bg-gradient-to-br hover:from-blue-50 hover:to-purple-50 hover:text-blue-600">
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
        <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center">
            <i class="fas fa-shield-alt text-white text-2xl"></i>
        </div>
        <h2 class="text-lg font-bold bg-gradient-to-br from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Admin Dashboard</h2>
        <p class="text-sm text-gray-600 mb-1">System administration & management</p>
        <p class="text-xs text-gray-400">Full control over the BSS Investment System</p>
    </div>
</div>
