<div x-show="showChatModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;" @mousedown.self="showChatModal = false">
    <div class="bg-white rounded-lg w-full max-w-md h-[600px] flex flex-col">
        <div class="relative p-5 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-t-lg overflow-hidden">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-24 -mb-24"></div>
            <div class="relative flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-xl font-bold tracking-wide">Hi, <span x-text="adminProfile.name || 'User'" class="text-yellow-300"></span> 👋</h3>
                    </div>
                    <div class="h-8 w-px bg-white/30"></div>
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="w-11 h-11 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg border-2 border-white/30">
                                <i class="fas fa-headset text-white text-lg"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white shadow-lg animate-pulse"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Support Team</p>
                            <p class="text-xs text-blue-100 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                                Always here to help
                            </p>
                        </div>
                    </div>
                </div>
                <div class="relative flex items-center space-x-2">
                    <button @click.stop="showChatModal = false" class="group relative p-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full transition-all duration-300 border border-white/20 hover:border-white/40">
                        <i class="fas fa-times text-lg group-hover:rotate-90 transition-transform duration-300"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="flex-1 p-4 overflow-y-auto bg-gray-50" id="chatMessages">
            <div class="space-y-3">
                <template x-for="(msg, index) in chatMessages" :key="index">
                    <div :class="msg.sender === 'user' ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="msg.sender === 'user' ? 'bg-blue-600 text-white' : 'bg-white text-gray-800'"
                             class="p-3 rounded-lg shadow-sm max-w-xs">
                            <p class="text-sm" x-text="msg.text"></p>
                            <span class="text-xs opacity-75" x-text="msg.time"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <div class="p-4 border-t bg-white rounded-b-lg">
            <div class="flex flex-wrap gap-2 mb-3">
                <button @click="sendQuickMessage('I need help with system settings')" class="group px-3 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 border border-blue-200 rounded-xl text-xs font-medium text-blue-700 transition-all hover:shadow-md flex items-center gap-2">
                    <i class="fas fa-cog group-hover:rotate-180 transition-transform duration-500"></i>
                    <span>Settings Help</span>
                </button>
                <button @click="sendQuickMessage('User management issue')" class="group px-3 py-2 bg-gradient-to-r from-purple-50 to-pink-50 hover:from-purple-100 hover:to-pink-100 border border-purple-200 rounded-xl text-xs font-medium text-purple-700 transition-all hover:shadow-md flex items-center gap-2">
                    <i class="fas fa-user-circle group-hover:scale-110 transition-transform"></i>
                    <span>User Issue</span>
                </button>
                <button @click="sendQuickMessage('System error')" class="group px-3 py-2 bg-gradient-to-r from-red-50 to-orange-50 hover:from-red-100 hover:to-orange-100 border border-red-200 rounded-xl text-xs font-medium text-red-700 transition-all hover:shadow-md flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle group-hover:animate-bounce"></i>
                    <span>Error</span>
                </button>
                <button @click="showMemberChatModal = true; showChatModal = false" class="group px-3 py-2 bg-gradient-to-r from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 border border-green-200 rounded-xl text-xs font-medium text-green-700 transition-all hover:shadow-md flex items-center gap-2">
                    <i class="fas fa-users group-hover:scale-110 transition-transform"></i>
                    <span>Members</span>
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <input type="text" x-model="chatInput" @keyup.enter="sendMessage" placeholder="Type your message..."
                       class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-400 bg-gray-50 hover:bg-white">
                <button @click="sendMessage" class="group px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </div>
    </div>
</div>
