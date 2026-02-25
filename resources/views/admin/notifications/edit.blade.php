@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.notifications.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-blue-600"></i>
        </a>
        <div>
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">Edit Notification</h2>
            <p class="text-gray-600 text-sm">Update notification details</p>
        </div>
    </div>

    <form action="{{ route('admin.notifications.update', $notification->id) }}" method="POST" class="max-w-4xl mx-auto">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 rounded-full bg-white flex items-center justify-center shadow-2xl">
                        <i class="fas fa-bell text-blue-600 text-5xl"></i>
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold">Edit Notification</h3>
            </div>

            <!-- Form Content -->
            <div class="p-6 md:p-8 space-y-6">
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                        <i class="fas fa-heading text-blue-600"></i>
                        Title *
                    </label>
                    <input type="text" name="title" value="{{ old('title', $notification->title) }}" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                        <i class="fas fa-align-left text-indigo-600"></i>
                        Message *
                    </label>
                    <textarea name="message" rows="5" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">{{ old('message', $notification->message) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-tag text-purple-600"></i>
                            Type *
                        </label>
                        <select name="type" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm appearance-none bg-white">
                            <option value="sms" {{ old('type', $notification->type) == 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="email" {{ old('type', $notification->type) == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="push" {{ old('type', $notification->type) == 'push' ? 'selected' : '' }}>Push</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-circle text-green-600 text-[8px]"></i>
                            Status *
                        </label>
                        <select name="status" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm appearance-none bg-white">
                            <option value="pending" {{ old('status', $notification->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="sent" {{ old('status', $notification->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="failed" {{ old('status', $notification->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('admin.notifications.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Update Notification
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
