@extends('layouts.member')

@section('content')
<div x-data="helpManager()" class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-teal-600 to-cyan-600 rounded-xl shadow-lg p-6 text-white">
        <h2 class="text-3xl font-bold mb-2">Help & Support</h2>
        <p class="text-teal-100">Get assistance and find answers to your questions</p>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <button @click="activeTab = 'faq'" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition text-left">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 p-4 rounded-lg">
                    <i class="fas fa-question-circle text-blue-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">FAQs</h3>
                    <p class="text-sm text-gray-600">Common questions</p>
                </div>
            </div>
        </button>

        <button @click="activeTab = 'contact'" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition text-left">
            <div class="flex items-center gap-4">
                <div class="bg-green-100 p-4 rounded-lg">
                    <i class="fas fa-headset text-green-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">Contact Us</h3>
                    <p class="text-sm text-gray-600">Get in touch</p>
                </div>
            </div>
        </button>

        <button @click="activeTab = 'guides'" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition text-left">
            <div class="flex items-center gap-4">
                <div class="bg-purple-100 p-4 rounded-lg">
                    <i class="fas fa-book text-purple-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">User Guides</h3>
                    <p class="text-sm text-gray-600">Learn how to use</p>
                </div>
            </div>
        </button>
    </div>

    <!-- FAQs Section -->
    <div x-show="activeTab === 'faq'" class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Frequently Asked Questions</h3>
        <div class="space-y-4">
            <div class="border rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50">
                    <span class="font-medium text-gray-800">How do I make a deposit?</span>
                    <i class="fas fa-chevron-down transition-transform" :class="openFaq === 1 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openFaq === 1" x-collapse class="px-6 py-4 bg-gray-50 border-t">
                    <p class="text-gray-600">To make a deposit, navigate to the Deposits section from your dashboard, click "Make Deposit", enter the amount, and follow the payment instructions.</p>
                </div>
            </div>

            <div class="border rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50">
                    <span class="font-medium text-gray-800">How do I apply for a loan?</span>
                    <i class="fas fa-chevron-down transition-transform" :class="openFaq === 2 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openFaq === 2" x-collapse class="px-6 py-4 bg-gray-50 border-t">
                    <p class="text-gray-600">Go to Loans section, click "Apply for Loan", fill in the required information including amount and duration, then submit your application for review.</p>
                </div>
            </div>

            <div class="border rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50">
                    <span class="font-medium text-gray-800">How can I check my transaction history?</span>
                    <i class="fas fa-chevron-down transition-transform" :class="openFaq === 3 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openFaq === 3" x-collapse class="px-6 py-4 bg-gray-50 border-t">
                    <p class="text-gray-600">Visit the Transactions section from your dashboard to view your complete transaction history with filters and export options.</p>
                </div>
            </div>

            <div class="border rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50">
                    <span class="font-medium text-gray-800">How do I update my profile information?</span>
                    <i class="fas fa-chevron-down transition-transform" :class="openFaq === 4 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openFaq === 4" x-collapse class="px-6 py-4 bg-gray-50 border-t">
                    <p class="text-gray-600">Go to Settings, select the Profile tab, update your information, and click "Save Changes".</p>
                </div>
            </div>

            <div class="border rounded-lg overflow-hidden">
                <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50">
                    <span class="font-medium text-gray-800">What are the loan interest rates?</span>
                    <i class="fas fa-chevron-down transition-transform" :class="openFaq === 5 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openFaq === 5" x-collapse class="px-6 py-4 bg-gray-50 border-t">
                    <p class="text-gray-600">Interest rates vary based on loan type and duration. Contact support or check the loan application page for current rates.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div x-show="activeTab === 'contact'" class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Contact Support</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="flex items-start gap-4 p-4 bg-blue-50 rounded-lg">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-phone text-blue-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-1">Phone</h4>
                    <p class="text-gray-600">+256 700 000 000</p>
                    <p class="text-sm text-gray-500">Mon-Fri, 8AM-5PM</p>
                </div>
            </div>

            <div class="flex items-start gap-4 p-4 bg-green-50 rounded-lg">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-envelope text-green-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-1">Email</h4>
                    <p class="text-gray-600">support@bss.com</p>
                    <p class="text-sm text-gray-500">24/7 support</p>
                </div>
            </div>

            <div class="flex items-start gap-4 p-4 bg-purple-50 rounded-lg">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-map-marker-alt text-purple-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-1">Office</h4>
                    <p class="text-gray-600">Kampala, Uganda</p>
                    <p class="text-sm text-gray-500">Visit us</p>
                </div>
            </div>

            <div class="flex items-start gap-4 p-4 bg-orange-50 rounded-lg">
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-comments text-orange-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-1">Live Chat</h4>
                    <p class="text-gray-600">Chat with us</p>
                    <p class="text-sm text-gray-500">Available now</p>
                </div>
            </div>
        </div>

        <form class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                <input type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                <textarea rows="5" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500"></textarea>
            </div>
            <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
                <i class="fas fa-paper-plane mr-2"></i>Send Message
            </button>
        </form>
    </div>

    <!-- Guides Section -->
    <div x-show="activeTab === 'guides'" class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">User Guides</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-start gap-4">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-book-open text-blue-600 text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 mb-2">Getting Started</h4>
                        <p class="text-sm text-gray-600 mb-3">Learn the basics of using the BSS system</p>
                        <a href="#" class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                            Read Guide <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-start gap-4">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 mb-2">Managing Transactions</h4>
                        <p class="text-sm text-gray-600 mb-3">How to deposit, withdraw, and transfer funds</p>
                        <a href="#" class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                            Read Guide <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-start gap-4">
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <i class="fas fa-hand-holding-usd text-orange-600 text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 mb-2">Loan Application Process</h4>
                        <p class="text-sm text-gray-600 mb-3">Step-by-step guide to applying for loans</p>
                        <a href="#" class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                            Read Guide <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-start gap-4">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-shield-alt text-purple-600 text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 mb-2">Security Best Practices</h4>
                        <p class="text-sm text-gray-600 mb-3">Keep your account safe and secure</p>
                        <a href="#" class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                            Read Guide <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function helpManager() {
    return {
        activeTab: 'faq',
        openFaq: null
    }
}
</script>
@endsection

