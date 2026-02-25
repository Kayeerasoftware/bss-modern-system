@extends('layouts.member')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Contact Support</h1>
        <p class="text-gray-600">Get help from our support team</p>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg p-6 text-center">
            <i class="fas fa-phone text-4xl mb-3"></i>
            <h3 class="font-bold mb-2">Phone Support</h3>
            <p class="text-sm mb-2">+256 XXX XXX XXX</p>
            <p class="text-xs opacity-90">Mon-Fri: 8AM-6PM</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg p-6 text-center">
            <i class="fas fa-envelope text-4xl mb-3"></i>
            <h3 class="font-bold mb-2">Email Support</h3>
            <p class="text-sm mb-2">support@bss.com</p>
            <p class="text-xs opacity-90">24/7 Response</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg p-6 text-center">
            <i class="fas fa-comments text-4xl mb-3"></i>
            <h3 class="font-bold mb-2">Live Chat</h3>
            <button @click="showChatModal = true" class="mt-2 bg-white text-purple-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100">
                Start Chat
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-xl font-bold mb-4">Send us a Message</h2>
        <form action="#" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                    <input type="text" name="subject" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea name="message" rows="5" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-paper-plane mr-2"></i>Send Message
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

