<table class="min-w-full divide-y-0">
    <thead class="bg-gradient-to-r from-yellow-600 via-orange-600 to-red-600">
        <tr class="border-b-2 border-white/20">
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Application ID</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Member</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Amount</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Duration</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Purpose</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Status</th>
            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white">
        @forelse($applications as $app)
        <tr class="transition-all duration-200 {{ $loop->iteration % 2 == 0 ? 'bg-yellow-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 border-l-4 border-yellow-500">
            <td class="px-6 py-4 whitespace-nowrap relative border-r border-gray-200">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-500 to-orange-600 flex items-center justify-center">
                        <i class="fas fa-file-invoice text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $app->application_id }}</p>
                        <p class="text-xs text-gray-500">{{ $app->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 border-r border-gray-200">
                <div class="flex items-center gap-2">
                    @if($app->member && $app->member->profile_picture)
                        <img src="{{ asset('storage/' . $app->member->profile_picture) }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-yellow-500 ring-offset-2" alt="">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-500 to-orange-600 flex items-center justify-center ring-2 ring-yellow-500 ring-offset-2">
                            <span class="text-white font-bold text-sm">{{ substr($app->member->full_name ?? 'N', 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $app->member->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $app->member->member_id ?? '' }}</p>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                <div class="flex items-center gap-2">
                    <i class="fas fa-money-bill-wave text-yellow-500"></i>
                    <span class="text-sm font-semibold text-gray-900">{{ number_format($app->amount, 2) }}</span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-orange-500"></i>
                    <span class="text-sm font-semibold text-gray-900">{{ $app->repayment_months }} months</span>
                </div>
            </td>
            <td class="px-6 py-4 border-r border-gray-200">
                <p class="text-sm text-gray-700 truncate max-w-xs" title="{{ $app->purpose }}">{{ Str::limit($app->purpose, 40) }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'approved' => 'bg-green-100 text-green-800 border-green-200',
                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                        'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
                    ];
                    $colorClass = $statusColors[$app->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                @endphp
                <span class="px-3 py-1.5 text-xs font-semibold rounded-full border {{ $colorClass }}">
                    <i class="fas fa-circle text-[8px] mr-1"></i>
                    {{ ucfirst($app->status) }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="flex items-center justify-center gap-2">
                    <a href="{{ route('admin.loan-applications.show', $app->id) }}" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all duration-200 group" title="View">
                        <i class="fas fa-eye group-hover:scale-110 transition-transform"></i>
                    </a>
                    <a href="{{ route('admin.loan-applications.edit', $app->id) }}" class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-all duration-200 group" title="Edit">
                        <i class="fas fa-edit group-hover:scale-110 transition-transform"></i>
                    </a>
                    <form action="{{ route('admin.loan-applications.destroy', $app->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all duration-200 group" onclick="return confirm('Are you sure you want to delete this application?')" title="Delete">
                            <i class="fas fa-trash group-hover:scale-110 transition-transform"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center">
                    <i class="fas fa-file-invoice text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-medium">No loan applications found</p>
                    <p class="text-gray-400 text-sm mt-2">Try adjusting your search or filters</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="p-4">
    {{ $applications->links() }}
</div>

<style>
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
    background: linear-gradient(to right, transparent, #f59e0b, #ea580c, #dc2626, transparent);
    opacity: 0.3;
}
tr:hover::after {
    opacity: 0.6;
}
</style>
