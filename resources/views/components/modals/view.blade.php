<div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-4xl w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                {{ $slot }}
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end">
                <button type="button" @click="open = false" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
