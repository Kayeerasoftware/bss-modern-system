<div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6 border border-gray-100">
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
        <div class="relative flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
            <div class="relative group">
                <div class="w-32 h-32 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center overflow-hidden border-4 border-white shadow-2xl transform transition-transform group-hover:scale-105">
                    <img x-show="profilePicture" :src="profilePicture" alt="Profile" class="w-full h-full object-cover">
                    <i x-show="!profilePicture" class="fas fa-user-shield text-white text-5xl"></i>
                </div>
                <button @click="showProfileModal = true" class="absolute -bottom-2 -right-2 w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl hover:from-blue-600 hover:to-purple-600 transition shadow-lg transform hover:scale-110">
                    <i class="fas fa-camera"></i>
                </button>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h3 class="text-3xl font-bold text-white mb-1" x-text="adminProfile.name">Admin</h3>
                <p class="text-white/90 font-semibold text-lg mb-2" x-text="adminProfile.role">Administrator</p>
                <p class="text-white/80 text-sm mb-4" x-text="adminProfile.email">admin@bss.com</p>
                <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                    <span class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg text-xs font-semibold border border-white/30">
                        <i class="fas fa-circle text-green-400 text-[8px] mr-1 animate-pulse"></i>Active
                    </span>
                    <span class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg text-xs font-semibold border border-white/30">
                        <i class="fas fa-shield-alt mr-1"></i>Full Access
                    </span>
                    <span class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg text-xs font-semibold border border-white/30">
                        <i class="fas fa-clock mr-1"></i>Last login: Today
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
