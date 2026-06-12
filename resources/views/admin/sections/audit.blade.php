<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'audit'" id="audit">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Activity Log</h2>
    <div class="space-y-4">
        <template x-for="log in filteredAuditLogs" :key="log.id">
            <div class="bg-white border rounded-lg p-4">
                <h4 class="font-semibold" x-text="log.action"></h4>
                <p class="text-sm text-gray-600" x-text="log.details"></p>
            </div>
        </template>
    </div>
</div>
