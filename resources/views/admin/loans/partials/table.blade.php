<table class="min-w-full divide-y-0">
    <thead class="bg-gradient-to-r from-green-600 via-teal-600 to-blue-600">
        <tr class="border-b-2 border-white/20">
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Loan ID</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Member</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Amount</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Interest</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Monthly Payment</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Status</th>
            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white">
        @forelse($loans as $loan)
        <tr class="transition-all duration-200 {{ $loop->iteration % 2 == 0 ? 'bg-green-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-teal-50 hover:to-blue-50 border-l-4 border-green-500">
            <td class="px-6 py-4 whitespace-nowrap relative border-r border-gray-200">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $loan->loan_id }}</p>
                        <p class="text-xs text-gray-500">{{ optional($loan->created_at)->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 border-r border-gray-200">
                <div class="flex items-center gap-2">
                    @if($loan->member && $loan->member->profile_picture_url)
                        <img src="{{ $loan->member->profile_picture_url }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-green-500 ring-offset-2" alt="">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center ring-2 ring-green-500 ring-offset-2">
                            <span class="text-white font-bold text-sm">{{ substr((string) ($loan->member->full_name ?? 'N'), 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $loan->member->full_name ?? 'Unknown Member' }}</p>
                        <p class="text-xs text-gray-500">{{ $loan->member->member_id ?? 'N/A' }}</p>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                <div class="flex items-center gap-2">
                    <i class="fas fa-money-bill-wave text-green-500"></i>
                    <span class="text-sm font-semibold text-gray-900">{{ number_format((float) ($loan->amount ?? 0), 2) }}</span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                <div class="flex items-center gap-2">
                    <i class="fas fa-percentage text-teal-500"></i>
                    <span class="text-sm font-semibold text-gray-900">{{ number_format((float) ($loan->interest ?? 0), 2) }}</span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-blue-500"></i>
                    <span class="text-sm font-semibold text-gray-900">{{ number_format((float) ($loan->monthly_payment ?? 0), 2) }}</span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                @php
                    $statusValue = strtolower((string) ($loan->status ?? 'pending'));
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'approved' => 'bg-green-100 text-green-800 border-green-200',
                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                        'disbursed' => 'bg-blue-100 text-blue-800 border-blue-200',
                    ];
                    $colorClass = $statusColors[$statusValue] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                @endphp
                <span class="px-3 py-1.5 text-xs font-semibold rounded-full border {{ $colorClass }}">
                    <i class="fas fa-circle text-[8px] mr-1"></i>
                    {{ ucfirst($statusValue) }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="flex items-center justify-center gap-2">
                    <a href="{{ route('admin.loans.show', $loan->id) }}" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all duration-200 group" title="View">
                        <i class="fas fa-eye group-hover:scale-110 transition-transform"></i>
                    </a>
                    <a href="{{ route('admin.loans.edit', $loan->id) }}" class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-all duration-200 group" title="Edit">
                        <i class="fas fa-edit group-hover:scale-110 transition-transform"></i>
                    </a>
                    <form action="{{ route('admin.loans.destroy', $loan->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all duration-200 group" onclick="return confirm('Are you sure you want to delete this loan?')" title="Delete">
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
                    <i class="fas fa-hand-holding-usd text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg font-medium">No loans found</p>
                    <p class="text-gray-400 text-sm mt-2">Try adjusting your search or filters</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="p-4">
    {{ $loans->links() }}
</div>

<style>
.border-gradient {
    border-image: linear-gradient(to right, #10b981, #14b8a6, #3b82f6) 1;
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
    background: linear-gradient(to right, transparent, #10b981, #14b8a6, #3b82f6, transparent);
    opacity: 0.3;
}
tr:hover::after {
    opacity: 0.6;
}
</style>

