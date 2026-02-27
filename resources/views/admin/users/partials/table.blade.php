<table class="w-full text-xs">
    <thead class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b-2 border-indigo-200">
        <tr>
            <th class="px-2 md:px-3 py-2 text-left font-bold text-indigo-900">
                <div class="flex items-center gap-1">
                    <i class="fas fa-user text-indigo-600 text-[10px]"></i>
                    <span>Name</span>
                </div>
            </th>
            <th class="px-2 md:px-3 py-2 text-left font-bold text-indigo-900 hidden md:table-cell">
                <div class="flex items-center gap-1">
                    <i class="fas fa-envelope text-purple-600 text-[10px]"></i>
                    <span>Email</span>
                </div>
            </th>
            <th class="px-2 md:px-3 py-2 text-left font-bold text-indigo-900">
                <div class="flex items-center gap-1">
                    <i class="fas fa-user-tag text-pink-600 text-[10px]"></i>
                    <span>Role</span>
                </div>
            </th>
            <th class="px-2 md:px-3 py-2 text-left font-bold text-indigo-900 hidden sm:table-cell">
                <div class="flex items-center gap-1">
                    <i class="fas fa-circle text-green-600 text-[8px]"></i>
                    <span>Status</span>
                </div>
            </th>
            <th class="px-2 md:px-3 py-2 text-left font-bold text-indigo-900 hidden lg:table-cell">
                <div class="flex items-center gap-1">
                    <i class="fas fa-calendar text-blue-600 text-[10px]"></i>
                    <span>Joined</span>
                </div>
            </th>
            <th class="px-2 md:px-3 py-2 text-center font-bold text-indigo-900">Actions</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
        @forelse($users as $user)
        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-all duration-200 group">
            <td class="px-2 md:px-3 py-2">
                <div class="flex items-center gap-1.5 md:gap-2">
                    <div class="relative flex-shrink-0">
                        @if($user->profile_picture_url)
                            <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" class="w-6 h-6 md:w-8 md:h-8 rounded-lg shadow-sm object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=32&background=random&bold=true" alt="{{ $user->name }}" class="w-6 h-6 md:w-8 md:h-8 rounded-lg shadow-sm">
                        @endif
                        <div class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-green-500 rounded-full border border-white"></div>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 truncate">{{ $user->name }}</p>
                        <p class="text-gray-500 text-[10px] md:hidden truncate">{{ $user->email }}</p>
                    </div>
                </div>
            </td>
            <td class="px-2 md:px-3 py-2 hidden md:table-cell">
                <span class="text-gray-700">{{ $user->email }}</span>
            </td>
            <td class="px-2 md:px-3 py-2">
                <span class="px-1.5 md:px-2 py-0.5 md:py-1 rounded-lg text-[10px] font-bold
                    @if($user->role == 'admin') bg-gradient-to-r from-red-100 to-pink-100 text-red-800
                    @elseif($user->role == 'cashier') bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800
                    @elseif($user->role == 'td') bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-800
                    @elseif($user->role == 'ceo') bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-800
                    @else bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800
                    @endif">
                    {{ ucfirst($user->role) }}
                </span>
            </td>
            <td class="px-2 md:px-3 py-2 hidden sm:table-cell">
                <span class="px-1.5 md:px-2 py-0.5 md:py-1 rounded-lg text-[10px] font-bold
                    @if($user->is_active) bg-gradient-to-r from-green-100 to-emerald-100 text-green-800
                    @else bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800
                    @endif">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </td>
            <td class="px-2 md:px-3 py-2 text-gray-600 hidden lg:table-cell">
                {{ $user->created_at->format('M d, Y') }}
            </td>
            <td class="px-2 md:px-3 py-2">
                <div class="flex items-center justify-center gap-1">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="p-1 md:p-1.5 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all transform hover:scale-110" title="View">
                        <i class="fas fa-eye text-[10px]"></i>
                    </a>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="p-1 md:p-1.5 bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-200 transition-all transform hover:scale-110" title="Edit">
                        <i class="fas fa-edit text-[10px]"></i>
                    </a>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
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
            <td colspan="6" class="px-3 py-8 text-center">
                <div class="flex flex-col items-center justify-center">
                    <i class="fas fa-users text-4xl md:text-5xl text-gray-300 mb-2 md:mb-3"></i>
                    <p class="text-gray-500 font-medium text-xs md:text-sm">No users found</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="px-3 py-2 md:py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200">
    {{ $users->links() }}
</div>

