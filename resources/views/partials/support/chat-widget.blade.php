<div id="support-chat-root" class="fixed bottom-4 right-4 z-50">
    <button id="support-chat-toggle" type="button" class="flex items-center gap-2 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl">
        <i class="fas fa-headset"></i>
        <span>Get Support</span>
    </button>

    <div id="support-chat-panel" class="mt-3 hidden w-[92vw] max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl">
        <div class="flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3 text-white">
            <div>
                <h3 class="text-sm font-bold">Support Assistant</h3>
                <p class="text-[11px] opacity-90">Simple AI help for the whole system</p>
            </div>
            <button id="support-chat-close" type="button" class="rounded p-1 hover:bg-white/20">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="border-b border-gray-100 px-4 py-3">
            <label for="support-chat-category" class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-gray-500">Category</label>
            <select id="support-chat-category" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="settings_help">Settings Help</option>
                <option value="user_issue">User Issue</option>
                <option value="error">Error</option>
                <option value="performance">Performance</option>
                <option value="account_access">Account Access</option>
                <option value="data_sync">Data Sync</option>
                <option value="billing">Billing/Payments</option>
                <option value="general">General</option>
            </select>
            <div class="mt-2 flex flex-wrap gap-1">
                <button type="button" class="support-chat-chip rounded-full bg-blue-50 px-2 py-1 text-[11px] font-semibold text-blue-700" data-category="settings_help">Settings Help</button>
                <button type="button" class="support-chat-chip rounded-full bg-purple-50 px-2 py-1 text-[11px] font-semibold text-purple-700" data-category="user_issue">User Issue</button>
                <button type="button" class="support-chat-chip rounded-full bg-red-50 px-2 py-1 text-[11px] font-semibold text-red-700" data-category="error">Error</button>
            </div>
        </div>

        <div id="support-chat-messages" class="h-72 overflow-y-auto bg-gray-50 px-3 py-3 text-sm">
            <div class="mb-2 max-w-[92%] rounded-xl border border-blue-100 bg-white px-3 py-2 text-gray-700 shadow-sm">
                <p class="font-semibold text-blue-700">Support Assistant</p>
                <p>Describe your issue and I will suggest the fastest fix steps.</p>
            </div>
        </div>

        <div class="border-t border-gray-100 p-3">
            <form id="support-chat-form" class="space-y-2">
                <textarea id="support-chat-input" rows="2" maxlength="2000" placeholder="Type your issue..." class="w-full resize-none rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                <div class="flex items-center justify-between">
                    <p class="text-[11px] text-gray-500">Try: loading error, user mismatch, settings not saving</p>
                    <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-bold text-white hover:bg-blue-700">
                        <i class="fas fa-paper-plane mr-1"></i>Send
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    if (window.__supportChatInitialized) return;
    window.__supportChatInitialized = true;

    const toggleBtn = document.getElementById('support-chat-toggle');
    const closeBtn = document.getElementById('support-chat-close');
    const panel = document.getElementById('support-chat-panel');
    const form = document.getElementById('support-chat-form');
    const input = document.getElementById('support-chat-input');
    const messages = document.getElementById('support-chat-messages');
    const categorySelect = document.getElementById('support-chat-category');
    const chips = document.querySelectorAll('.support-chat-chip');
    const endpoint = @json(route('support.chat.respond'));
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function appendMessage(role, text) {
        const wrapper = document.createElement('div');
        wrapper.className = role === 'user'
            ? 'mb-2 ml-auto max-w-[92%] rounded-xl bg-blue-600 px-3 py-2 text-white shadow-sm'
            : 'mb-2 max-w-[92%] rounded-xl border border-blue-100 bg-white px-3 py-2 text-gray-700 shadow-sm';

        if (role === 'assistant') {
            const label = document.createElement('p');
            label.className = 'font-semibold text-blue-700';
            label.textContent = 'Support Assistant';
            wrapper.appendChild(label);
        }

        const body = document.createElement('p');
        body.textContent = text;
        wrapper.appendChild(body);

        messages.appendChild(wrapper);
        messages.scrollTop = messages.scrollHeight;
    }

    async function sendMessage() {
        const message = (input.value || '').trim();
        const category = categorySelect.value || 'general';
        if (!message) return;

        appendMessage('user', message);
        input.value = '';

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    category: category,
                    message: message,
                    path: window.location.pathname
                })
            });

            const payload = await response.json();
            if (!response.ok || !payload.success) {
                appendMessage('assistant', 'I could not process that request right now. Please retry.');
                return;
            }

            if (payload?.data?.suggested_category) {
                categorySelect.value = payload.data.suggested_category;
            }

            appendMessage('assistant', payload?.data?.reply || 'Support response unavailable.');
        } catch (error) {
            appendMessage('assistant', 'Network issue while contacting support assistant. Please try again.');
        }
    }

    toggleBtn?.addEventListener('click', function () {
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) input.focus();
    });

    closeBtn?.addEventListener('click', function () {
        panel.classList.add('hidden');
    });

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            const value = chip.getAttribute('data-category');
            if (value) categorySelect.value = value;
        });
    });

    form?.addEventListener('submit', function (event) {
        event.preventDefault();
        sendMessage();
    });

    input?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage();
        }
    });
})();
</script>

