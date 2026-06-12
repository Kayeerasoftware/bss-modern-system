@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6">
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('cashier.notifications.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6 font-semibold transition-all hover:gap-3 gap-2">
            <i class="fas fa-arrow-left"></i> Back to Notifications
        </a>

        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-blue-100">
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 p-8 text-white">
                <div class="flex items-center gap-4 mb-4">
                    <div class="bg-white/20 p-4 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-paper-plane text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Create New Notification</h1>
                        <p class="text-blue-100 mt-1">Send notifications to users, roles, or everyone</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('cashier.notifications.store') }}" method="POST" class="p-8" x-data="{ sendTo: 'all' }">
                @csrf
                <div class="space-y-6">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                        <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-users text-blue-600"></i> Send To *
                        </label>
                        <select x-model="sendTo" name="send_to" required class="w-full px-4 py-3 border-2 border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white font-semibold">
                            <option value="all">📢 All Users</option>
                            <option value="role">👥 Specific Role</option>
                            <option value="member">👤 Specific Member</option>
                        </select>
                    </div>

                    <div x-show="sendTo === 'role'" x-cloak x-transition class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
                        <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-user-tag text-purple-600"></i> Select Role *
                        </label>
                        <select name="role" class="w-full px-4 py-3 border-2 border-purple-300 rounded-xl focus:ring-2 focus:ring-purple-500 bg-white font-semibold">
                            <option value="admin">🔐 Admin</option>
                            <option value="cashier">💰 Cashier</option>
                            <option value="ceo">👔 CEO</option>
                            <option value="td">🔧 Technical Director</option>
                            <option value="shareholder">📊 Shareholder</option>
                            <option value="client">👤 Client</option>
                        </select>
                    </div>

                    <div x-show="sendTo === 'member'" x-cloak x-transition class="bg-gradient-to-br from-green-50 to-teal-50 rounded-xl p-6 border border-green-200">
                        <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-user-circle text-green-600"></i> Select Member *
                        </label>
                        <select name="member_id" class="w-full px-4 py-3 border-2 border-green-300 rounded-xl focus:ring-2 focus:ring-green-500 bg-white font-semibold">
                            <option value="">Choose a member...</option>
                            @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-heading text-indigo-600"></i> Title *
                            </label>
                            <input type="text" name="title" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('title') }}" placeholder="Enter notification title">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-tag text-pink-600"></i> Type *
                            </label>
                            <select name="type" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 font-semibold">
                                <option value="info">ℹ️ Info</option>
                                <option value="success">✅ Success</option>
                                <option value="warning">⚠️ Warning</option>
                                <option value="error">❌ Error</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-comment-alt text-blue-600"></i> Message *
                        </label>
                        <textarea name="message" required rows="6" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" placeholder="Type your notification message here...">{{ old('message') }}</textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 px-8 py-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold text-lg transform hover:scale-105">
                            <i class="fas fa-paper-plane mr-2"></i>Send Notification
                        </button>
                        <a href="{{ route('cashier.notifications.index') }}" class="px-8 py-4 bg-gradient-to-r from-gray-200 to-gray-300 text-gray-700 rounded-xl hover:shadow-lg transition-all font-bold text-lg transform hover:scale-105">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
