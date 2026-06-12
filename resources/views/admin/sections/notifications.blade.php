<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'notifications'" id="notifications">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Notifications Center</h2>
    <div class="bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Send New Notification</h3>
        <div class="space-y-4">
            <input type="text" x-model="notificationForm.title" placeholder="Title" class="w-full p-3 border rounded-lg">
            <textarea x-model="notificationForm.message" placeholder="Message" class="w-full p-3 border rounded-lg" rows="4"></textarea>
            <button @click="sendNotification()" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                <i class="fas fa-paper-plane mr-2"></i>Send
            </button>
        </div>
    </div>
</div>
