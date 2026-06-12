@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-3 md:p-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.notifications.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left text-blue-600"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">Notification Details</h2>
                <p class="text-gray-600 text-sm">View notification information</p>
            </div>
        </div>
        <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 p-8">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="relative">
                        <div class="w-24 h-24 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center border-4 border-white shadow-2xl">
                            <i class="fas 
                                @if($notification->type == 'sms') fa-sms
                                @elseif($notification->type == 'email') fa-envelope
                                @else fa-bell
                                @endif text-white text-4xl"></i>
                        </div>
                    </div>
                    <div class="text-center md:text-left">
                        <h3 class="text-3xl font-bold text-white mb-2">{{ $notification->title }}</h3>
                        <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                            <span class="px-4 py-1.5 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold">
                                <i class="fas fa-tag mr-1"></i>{{ strtoupper($notification->type ?? 'system') }}
                            </span>
                            <span class="px-4 py-1.5 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold">
                                <i class="fas fa-circle text-xs mr-1 
                                    @if($notification->status == 'sent') text-green-400
                                    @elseif($notification->status == 'pending') text-yellow-400
                                    @else text-red-400
                                    @endif"></i>{{ ucfirst($notification->status ?? 'sent') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border-2 border-blue-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-3 rounded-xl">
                                <i class="fas fa-info-circle text-white"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800">Information</h4>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-users text-blue-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Recipients</p>
                                    <p class="font-bold text-gray-900">{{ $notification->recipients_count ?? 0 }} members</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-calendar-plus text-indigo-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Sent Date</p>
                                    <p class="font-bold text-gray-900">{{ $notification->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-clock text-purple-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Time Ago</p>
                                    <p class="font-bold text-gray-900">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border-2 border-purple-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-3 rounded-xl">
                                <i class="fas fa-chart-bar text-white"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800">Statistics</h4>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-green-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Delivery Status</p>
                                    <p class="font-bold text-gray-900">{{ ucfirst($notification->status ?? 'sent') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-paper-plane text-blue-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Type</p>
                                    <p class="font-bold text-gray-900">{{ strtoupper($notification->type ?? 'system') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-user text-indigo-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Created By</p>
                                    <p class="font-bold text-gray-900">{{ $notification->created_by ?? 'System' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border-2 border-gray-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-gradient-to-r from-gray-600 to-gray-700 p-3 rounded-xl">
                            <i class="fas fa-comment-alt text-white"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800">Message Content</h4>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-gray-200">
                        <p class="text-gray-700 leading-relaxed">{{ $notification->message ?? 'No message content available' }}</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 mt-6 border-t-2 border-gray-100">
                    <a href="{{ route('admin.notifications.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                    <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="px-8 py-3 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-edit mr-2"></i>Edit Notification
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
