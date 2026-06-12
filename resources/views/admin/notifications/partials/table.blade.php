<table class="w-full text-xs">
    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b-2 border-blue-200">
        <tr>
            <th class="px-2 md:px-3 py-2 text-left font-bold text-blue-900">
                <div class="flex items-center gap-1">
                    <i class="fas fa-bell text-blue-600 text-[10px]"></i>
                    <span>Notification</span>
                </div>
            </th>
            <th class="px-2 md:px-3 py-2 text-left font-bold text-blue-900 hidden md:table-cell">
                <div class="flex items-center gap-1">
                    <i class="fas fa-users text-indigo-600 text-[10px]"></i>
                    <span>Recipients</span>
                </div>
            </th>
            <th class="px-2 md:px-3 py-2 text-left font-bold text-blue-900 hidden sm:table-cell">
                <div class="flex items-center gap-1">
                    <i class="fas fa-paper-plane text-purple-600 text-[10px]"></i>
                    <span>Type</span>
                </div>
            </th>
            <th class="px-2 md:px-3 py-2 text-left font-bold text-blue-900 hidden lg:table-cell">
                <div class="flex items-center gap-1">
                    <i class="fas fa-clock text-pink-600 text-[10px]"></i>
                    <span>Sent</span>
                </div>
            </th>
            <th class="px-2 md:px-3 py-2 text-left font-bold text-blue-900">Status</th>
            <th class="px-2 md:px-3 py-2 text-center font-bold text-blue-900">Actions</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
        @forelse($notifications as $notification)
        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200 group">
            <td class="px-2 md:px-3 py-3">
                <div class="flex items-start gap-2">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl flex items-center justify-center shadow-sm
                            @if($notification->type == 'sms') bg-gradient-to-br from-purple-500 to-purple-600
                            @elseif($notification->type == 'email') bg-gradient-to-br from-orange-500 to-orange-600
                            @else bg-gradient-to-br from-blue-500 to-blue-600
                            @endif">
                            <i class="fas 
                                @if($notification->type == 'sms') fa-sms
                                @elseif($notification->type == 'email') fa-envelope
                                @else fa-bell
                                @endif text-white text-xs md:text-sm"></i>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-gray-900 truncate text-sm">{{ $notification->title }}</p>
                        <p class="text-gray-600 text-[10px] line-clamp-1">{{ Str::limit($notification->message ?? 'No message', 50) }}</p>
                        <div class="flex items-center gap-2 mt-1 md:hidden">
                            <span class="text-[9px] text-gray-500">
                                <i class="fas fa-users text-indigo-600"></i> {{ $notification->recipients_count ?? 0 }}
                            </span>
                            <span class="text-[9px] text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-2 md:px-3 py-3 hidden md:table-cell">
                <div class="flex items-center gap-1">
                    <div class="w-6 h-6 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600 text-[10px]"></i>
                    </div>
                    <span class="font-bold text-gray-900">{{ $notification->recipients_count ?? 0 }}</span>
                </div>
            </td>
            <td class="px-2 md:px-3 py-3 hidden sm:table-cell">
                <span class="px-2 py-1 rounded-lg text-[10px] font-bold
                    @if($notification->type == 'sms') bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800
                    @elseif($notification->type == 'email') bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800
                    @else bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800
                    @endif">
                    {{ strtoupper($notification->type ?? 'system') }}
                </span>
            </td>
            <td class="px-2 md:px-3 py-3 text-gray-600 text-[10px] hidden lg:table-cell">
                <div class="flex items-center gap-1">
                    <i class="fas fa-clock text-pink-600"></i>
                    {{ $notification->created_at->format('M d, Y H:i') }}
                </div>
            </td>
            <td class="px-2 md:px-3 py-3">
                <span class="px-2 py-1 rounded-lg text-[10px] font-bold
                    @if($notification->status == 'sent') bg-gradient-to-r from-green-100 to-emerald-100 text-green-800
                    @elseif($notification->status == 'pending') bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800
                    @else bg-gradient-to-r from-red-100 to-pink-100 text-red-800
                    @endif">
                    {{ ucfirst($notification->status ?? 'sent') }}
                </span>
            </td>
            <td class="px-2 md:px-3 py-3">
                <div class="flex items-center justify-center gap-1">
                    <a href="{{ route('admin.notifications.show', $notification->id) }}" class="p-1 md:p-1.5 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all transform hover:scale-110" title="View">
                        <i class="fas fa-eye text-[10px]"></i>
                    </a>
                    <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="p-1 md:p-1.5 bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-200 transition-all transform hover:scale-110" title="Edit">
                        <i class="fas fa-edit text-[10px]"></i>
                    </a>
                    <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this notification?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1 md:p-1.5 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all transform hover:scale-110" title="Delete">
                            <i class="fas fa-trash text-[10px]"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-3 py-12 text-center">
                <div class="flex flex-col items-center justify-center">
                    <i class="fas fa-bell-slash text-5xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 font-medium text-sm">No notifications found</p>
                    <p class="text-gray-400 text-xs mt-1">Send your first notification to get started</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="px-3 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200">
    {{ $notifications->links() }}
</div>
