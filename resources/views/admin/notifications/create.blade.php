@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-3 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.notifications.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-blue-600"></i>
        </a>
        <div>
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">Send Notification</h2>
            <p class="text-gray-600 text-sm">Quick send to members</p>
        </div>
    </div>

    <form action="{{ route('admin.notifications.store') }}" method="POST" class="max-w-3xl mx-auto">
        @csrf
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <i class="fas fa-paper-plane text-white text-3xl"></i>
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold">Quick Send</h3>
                <p class="text-white/80 text-sm">Send instant notifications</p>
            </div>

            <div class="p-6 md:p-8 space-y-6">
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                        <i class="fas fa-heading text-blue-600"></i>
                        Title *
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm"
                           placeholder="Enter notification title">
                    @error('title')
                        <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                        <i class="fas fa-comment-alt text-indigo-600"></i>
                        Message *
                    </label>
                    <textarea name="message" rows="5" required
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                              placeholder="Type your message here...">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-users text-purple-600"></i>
                            Recipients *
                        </label>
                        <select name="recipients" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm appearance-none bg-white">
                            <option value="all">All Members</option>
                            <option value="active">Active Members Only</option>
                            <option value="clients">Clients</option>
                            <option value="shareholders">Shareholders</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-paper-plane text-pink-600"></i>
                            Type *
                        </label>
                        <select name="type" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm appearance-none bg-white">
                            <option value="sms">SMS</option>
                            <option value="email">Email</option>
                            <option value="both">SMS & Email</option>
                        </select>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-4 border-2 border-blue-100">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 text-lg mt-0.5"></i>
                        <div class="text-sm text-blue-900">
                            <p class="font-bold mb-1">Quick Send Tips:</p>
                            <ul class="space-y-1 text-xs">
                                <li>• Keep messages clear and concise</li>
                                <li>• SMS limited to 160 characters</li>
                                <li>• Notifications sent immediately</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('admin.notifications.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-paper-plane mr-2"></i>Send Now
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
