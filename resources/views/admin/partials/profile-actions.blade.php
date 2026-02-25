<div class="flex flex-wrap gap-3">
    <button @click="localStorage.setItem('adminProfile', JSON.stringify(adminProfile)); alert('Profile updated successfully!')" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
        <i class="fas fa-save mr-2"></i>Save Changes
    </button>
    <button @click="activeLink = 'settings'; document.getElementById('settings').scrollIntoView({ behavior: 'smooth' })" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium">
        <i class="fas fa-cog mr-2"></i>System Settings
    </button>
    <button @click="activeLink = 'audit'; document.getElementById('audit').scrollIntoView({ behavior: 'smooth' })" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium">
        <i class="fas fa-history mr-2"></i>View Full Activity Log
    </button>
</div>
