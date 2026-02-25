<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'backup'" id="backup">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Database Backup & Restore</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <button @click="createBackup()" class="p-4 border-2 border-green-500 rounded-lg hover:bg-green-50">
            <i class="fas fa-download text-green-600 text-3xl mb-2"></i>
            <p class="font-semibold text-green-600">Create Backup</p>
        </button>
        <button @click="restoreBackup()" class="p-4 border-2 border-blue-500 rounded-lg hover:bg-blue-50">
            <i class="fas fa-upload text-blue-600 text-3xl mb-2"></i>
            <p class="font-semibold text-blue-600">Restore Backup</p>
        </button>
    </div>
</div>
