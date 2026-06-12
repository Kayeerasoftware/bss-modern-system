@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6">
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('cashier.notifications.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6 font-semibold transition-all hover:gap-3 gap-2">
            <i class="fas fa-arrow-left"></i> Back to Notifications
        </a>

        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-blue-100">
            <div class="bg-gradient-to-r from-{{ $notification->type == 'info' ? 'blue' : ($notification->type == 'success' ? 'green' : ($notification->type == 'warning' ? 'yellow' : 'red')) }}-500 via-{{ $notification->type == 'info' ? 'indigo' : ($notification->type == 'success' ? 'emerald' : ($notification->type == 'warning' ? 'orange' : 'rose')) }}-500 to-{{ $notification->type == 'info' ? 'purple' : ($notification->type == 'success' ? 'teal' : ($notification->type == 'warning' ? 'amber' : 'pink')) }}-500 p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-bell text-9xl"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-4">
                            <div class="bg-white/20 p-4 rounded-xl backdrop-blur-sm">
                                <i class="fas fa-{{ $notification->type == 'info' ? 'info-circle' : ($notification->type == 'success' ? 'check-circle' : ($notification->type == 'warning' ? 'exclamation-triangle' : 'times-circle')) }} text-3xl"></i>
                            </div>
                            <div>
                                <span class="px-3 py-1 bg-white/30 backdrop-blur-sm rounded-full text-sm font-bold">
                                    {{ ucfirst($notification->type) }}
                                </span>
                                <h1 class="text-3xl font-bold mt-2">{{ $notification->title }}</h1>
                            </div>
                        </div>
                        <form action="{{ route('cashier.notifications.destroy', $notification->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this notification?')" class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg hover:bg-white/30 transition-all font-semibold">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </form>
                    </div>
                    <div class="flex items-center gap-6 text-sm text-white/90">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock"></i>
                            <span>{{ $notification->created_at->format('F d, Y \a\t H:i') }}</span>
                        </div>
                        @if($notification->created_by)
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user"></i>
                            <span>Sent by: <strong>{{ $notification->created_by }}</strong></span>
                        </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <i class="fas fa-{{ $notification->is_read ? 'envelope-open' : 'envelope' }}"></i>
                            <span>{{ $notification->is_read ? 'Read' : 'Unread' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl p-6 border-l-4 border-{{ $notification->type == 'info' ? 'blue' : ($notification->type == 'success' ? 'green' : ($notification->type == 'warning' ? 'yellow' : 'red')) }}-500">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-comment-alt text-blue-600"></i> Message
                    </h3>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed text-base">{{ $notification->message }}</p>
                    </div>
                </div>

                @if($notification->roles)
                <div class="mt-6 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-users text-purple-600"></i> Recipients
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($notification->roles as $role)
                        <span class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full text-sm font-semibold shadow-md">
                            <i class="fas fa-user-tag mr-1"></i>{{ ucfirst($role) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="mt-6 grid md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200 text-center">
                        <i class="fas fa-calendar text-2xl text-blue-600 mb-2"></i>
                        <p class="text-xs text-gray-600 font-semibold">Created</p>
                        <p class="text-sm font-bold text-gray-800">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 border border-green-200 text-center">
                        <i class="fas fa-sync text-2xl text-green-600 mb-2"></i>
                        <p class="text-xs text-gray-600 font-semibold">Updated</p>
                        <p class="text-sm font-bold text-gray-800">{{ $notification->updated_at->diffForHumans() }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-200 text-center">
                        <i class="fas fa-hashtag text-2xl text-purple-600 mb-2"></i>
                        <p class="text-xs text-gray-600 font-semibold">ID</p>
                        <p class="text-sm font-bold text-gray-800">#{{ $notification->id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
