@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6" x-data="{
    selectedNotifications: [],
    selectAll: false,
    toggleSelectAll() {
        this.selectAll = !this.selectAll;
        this.selectedNotifications = this.selectAll ? Array.from(document.querySelectorAll('[data-notification-id]')).map(el => el.dataset.notificationId) : [];
    },
    async markAllRead() {
        const response = await fetch('{{ route('cashier.notifications.mark-all-read') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
        if (response.ok) location.reload();
    },
    async deleteSelected() {
        if (!confirm('Delete selected notifications?')) return;
        for (const id of this.selectedNotifications) {
            await fetch(`/cashier/notifications/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
        }
        location.reload();
    }
}">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-blue-600 to-indigo-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                        <i class="fas fa-bell text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent mb-1 md:mb-2">Notifications</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">{{ $notifications->total() }} total notifications</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('cashier.notifications.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs md:text-sm">
                    <i class="fas fa-plus mr-1"></i>New
                </a>
                <button @click="markAllRead()" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs md:text-sm">
                    <i class="fas fa-check-double mr-1"></i>Mark All Read
                </button>
                <button @click="deleteSelected()" x-show="selectedNotifications.length > 0" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs md:text-sm">
                    <i class="fas fa-trash mr-1"></i>Delete (<span x-text="selectedNotifications.length"></span>)
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-3 md:mb-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $notifications->total() }}</h3>
                </div>
                <i class="fas fa-bell text-sm md:text-lg"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-[8px] md:text-[10px] font-medium mb-0.5">Read</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $notifications->where('is_read', true)->count() }}</h3>
                </div>
                <i class="fas fa-check-circle text-sm md:text-lg"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-[8px] md:text-[10px] font-medium mb-0.5">Unread</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $notifications->where('is_read', false)->count() }}</h3>
                </div>
                <i class="fas fa-envelope text-sm md:text-lg"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-[8px] md:text-[10px] font-medium mb-0.5">Today</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $notifications->where('created_at', '>=', today())->count() }}</h3>
                </div>
                <i class="fas fa-calendar text-sm md:text-lg"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-3 mb-4">
        <form method="GET" action="{{ route('cashier.notifications.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <div class="md:col-span-5">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search notifications..." class="w-full px-3 py-2 text-xs border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <select name="type" class="w-full px-3 py-2 text-xs border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Info</option>
                        <option value="success" {{ request('type') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="error" {{ request('type') == 'error' ? 'selected' : '' }}>Error</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <select name="status" class="w-full px-3 py-2 text-xs border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                    </select>
                </div>
                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-1"></i>Search
                    </button>
                    <a href="{{ route('cashier.notifications.index') }}" class="px-3 py-2 text-xs bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" @change="toggleSelectAll()" :checked="selectAll" class="rounded">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase">Message</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                    <tr class="border-b hover:bg-blue-50 {{ !$notification->is_read && $notification->member_id == auth()->id() ? 'bg-blue-50 font-semibold' : '' }}" :data-notification-id="{{ $notification->id }}">
                        <td class="px-4 py-3">
                            <input type="checkbox" :data-notification-id="{{ $notification->id }}" x-model="selectedNotifications" value="{{ $notification->id }}" class="rounded">
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $notification->type == 'info' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $notification->type == 'success' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $notification->type == 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $notification->type == 'error' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($notification->type) }}
                                </span>
                                @if($notification->created_by == auth()->user()->name)
                                <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-paper-plane text-[8px]"></i> Sent
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $notification->title }}</td>
                        <td class="px-4 py-3 text-sm">{{ Str::limit($notification->message, 50) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $notification->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('cashier.notifications.show', $notification->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('cashier.notifications.destroy', $notification->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this notification?')" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-bell text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">No notifications found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $notifications->links() }}</div>
    </div>
</div>
@endsection
