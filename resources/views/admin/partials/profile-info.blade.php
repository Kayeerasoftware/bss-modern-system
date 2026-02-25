<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-shadow">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b-2 border-gradient-to-r from-blue-600 to-purple-600">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user text-white text-xl"></i>
            </div>
            <h3 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Personal Information</h3>
        </div>
        <div class="space-y-4">
            <div>
                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-id-badge text-blue-600"></i>Full Name
                </label>
                <input type="text" x-model="adminProfile.name" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-envelope text-purple-600"></i>Email Address
                </label>
                <input type="email" x-model="adminProfile.email" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-phone text-green-600"></i>Phone Number
                </label>
                <input type="tel" x-model="adminProfile.phone" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt text-red-600"></i>Location
                </label>
                <input type="text" x-model="adminProfile.location" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
            </div>
            <button class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg font-semibold">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-shadow">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b-2 border-gradient-to-r from-red-600 to-orange-600">
            <div class="w-12 h-12 bg-gradient-to-r from-red-600 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-lock text-white text-xl"></i>
            </div>
            <h3 class="text-xl font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">Security Settings</h3>
        </div>
        <div class="space-y-4">
            <div>
                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-key text-gray-600"></i>Current Password
                </label>
                <input type="password" placeholder="Enter current password" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-lock text-orange-600"></i>New Password
                </label>
                <input type="password" placeholder="Enter new password" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-check-circle text-green-600"></i>Confirm Password
                </label>
                <input type="password" placeholder="Confirm new password" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
            </div>
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                <p class="text-xs text-yellow-800 flex items-start gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i>
                    <span>Password must be at least 8 characters with uppercase, lowercase, and numbers.</span>
                </p>
            </div>
            <button @click="alert('Password updated successfully!')" class="w-full px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-xl hover:from-red-700 hover:to-orange-700 transition-all shadow-lg font-semibold">
                <i class="fas fa-shield-alt mr-2"></i>Change Password
            </button>
        </div>
    </div>
</div>
