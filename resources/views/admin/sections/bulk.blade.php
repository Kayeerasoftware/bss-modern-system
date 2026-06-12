<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'bulk'" id="bulk">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Bulk Operations</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="border-2 border-blue-200 rounded-lg p-6 hover:shadow-lg transition">
            <i class="fas fa-file-import text-blue-600 text-3xl mb-3"></i>
            <h3 class="font-semibold text-lg">Import Members</h3>
            <button @click="showBulkImportModal = true" class="w-full mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg">
                <i class="fas fa-upload mr-2"></i>Import CSV
            </button>
        </div>
    </div>
</div>
