<table class="min-w-full divide-y-0">
    <thead class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600">
        <tr class="border-b-2 border-white/20">
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Member</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Contact Info</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Default Role</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Other Roles</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Savings</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Status</th>
            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white">
        @forelse($members as $member)
        <tr class="transition-all duration-200 {{ $loop->iteration % 2 == 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 border-l-4 {{ $member->trashed() ? 'border-orange-500 opacity-80' : 'border-green-500' }}">
            <td class="px-6 py-4 whitespace-nowrap relative border-r border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="relative w-12 h-12 rounded-full overflow-hidden shrink-0">
                        @php
                            $hasProfilePicture = $member->profile_picture || ($member->user && $member->user->profile_picture);
                        @endphp
                        @if($hasProfilePicture)
                            <img src="{{ $member->profile_picture_url }}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';" class="w-12 h-12 rounded-full object-cover ring-2 ring-blue-500 ring-offset-2 shrink-0" alt="{{ $member->full_name }}">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center ring-2 ring-gray-400 ring-offset-2 shrink-0">
                                <span class="text-gray-600 font-bold text-lg">{{ substr($member->full_name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $member->full_name }}</p>
                        <p class="text-xs text-gray-500">ID: {{ $member->member_id }}</p>
                        @if($hasProfilePicture)
                            <p class="text-xs text-green-600 flex items-center gap-1 mt-1">
                                <i class="fas fa-check-circle"></i>
                                Has picture
                            </p>
                        @else
                            <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                <i class="fas fa-user-circle"></i>
                                No picture
                            </p>
                        @endif
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 border-r border-gray-200">
                <div class="space-y-1">
                    <p class="text-sm text-gray-900 flex items-center gap-2">
                        <i class="fas fa-envelope text-gray-400 text-xs"></i>
                        {{ $member->email }}
                    </p>
                    <p class="text-sm text-gray-600 flex items-center gap-2">
                        <i class="fas fa-phone text-gray-400 text-xs"></i>
                        {{ $member->contact }}
                    </p>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                @php
                    $roleColors = [
                        'client' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'shareholder' => 'bg-purple-100 text-purple-800 border-purple-200',
                        'cashier' => 'bg-green-100 text-green-800 border-green-200',
                        'td' => 'bg-orange-100 text-orange-800 border-orange-200',
                        'ceo' => 'bg-red-100 text-red-800 border-red-200',
                    ];
                    $colorClass = $roleColors[$member->role] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                @endphp
                <span class="px-3 py-1.5 text-xs font-semibold rounded-full border {{ $colorClass }}">
                    {{ ucfirst($member->role) }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                @php
                    $allRoles = $member->roles_list ?? [];
                    $defaultRole = strtolower((string) ($member->role ?? 'client'));
                    $otherRoles = array_values(array_filter($allRoles, fn ($r) => strtolower((string) $r) !== $defaultRole));
                @endphp
                @if(!empty($otherRoles))
                    <div class="flex flex-wrap gap-1">
                        @foreach($otherRoles as $otherRole)
                            <span class="px-2 py-1 text-[10px] font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                                {{ ucfirst($otherRole) }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <span class="text-xs text-gray-500">None</span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                <div class="flex items-center gap-2">
                    <i class="fas fa-coins text-yellow-500"></i>
                    <span class="text-sm font-semibold text-gray-900">{{ number_format($member->savings, 2) }}</span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                <span class="px-3 py-1.5 text-xs font-semibold rounded-full {{ $member->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    <i class="fas fa-circle text-[8px] mr-1"></i>
                    {{ $member->trashed() ? 'Deleted' : ucfirst($member->status ?? 'active') }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="flex items-center justify-center gap-2">
                    @if($member->trashed())
                        <form action="{{ route('admin.members.restore', $member->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-all duration-200 group" onclick="return confirm('Restore this member?')" title="Restore">
                                <i class="fas fa-trash-restore group-hover:scale-110 transition-transform"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('admin.bio-data.view', $member->id) }}" class="p-2 bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-200 transition-all duration-200 group" title="View Bio Data">
                            <i class="fas fa-id-card group-hover:scale-110 transition-transform"></i>
                        </a>
                        <a href="{{ route('admin.members.show', $member->id) }}" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all duration-200 group" title="View">
                            <i class="fas fa-eye group-hover:scale-110 transition-transform"></i>
                        </a>
                        <a href="{{ route('admin.members.edit', $member->id) }}" class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-all duration-200 group" title="Edit">
                            <i class="fas fa-edit group-hover:scale-110 transition-transform"></i>
                        </a>
                        <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all duration-200 group" onclick="return confirm('Are you sure you want to move this member to trash?')" title="Move to Trash">
                                <i class="fas fa-trash group-hover:scale-110 transition-transform"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center">
                    <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-medium">No members found</p>
                    <p class="text-gray-400 text-sm mt-2">Try adjusting your search or filters</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="p-4">
    {{ $members->links() }}
</div>

<style>
.border-gradient {
    border-image: linear-gradient(to right, #3b82f6, #a855f7, #ec4899) 1;
}
tr {
    position: relative;
}
tr::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to right, transparent, #3b82f6, #a855f7, #ec4899, transparent);
    opacity: 0.3;
}
tr:hover::after {
    opacity: 0.6;
}
</style>

