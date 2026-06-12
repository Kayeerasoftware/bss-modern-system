<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 p-3 md:p-6">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left text-indigo-600"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Contributions</h2>
                <p class="text-gray-600 text-sm">{{ $fundraising->title }} ({{ $fundraising->campaign_id }})</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.contributions.create', $fundraising->id) }}" class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>Add New Contribution
            </a>
            <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.show', $fundraising->id) }}" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                <i class="fas fa-eye mr-2"></i>View Campaign
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600">
            <h3 class="text-white text-lg font-bold flex items-center gap-2">
                <i class="fas fa-coins"></i>
                Contribution List
            </h3>
        </div>
        @if(session('success'))
        <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        </div>
        @endif
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Contributor</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Source</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Receipt</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Thank You</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Message</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Notes</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($fundraising->contributions as $contribution)
                    <tr class="hover:bg-indigo-50/40 transition-colors">
                        <td class="px-4 py-3 text-xs font-semibold text-gray-700">{{ $contribution->contribution_number ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-700">
                            <p class="font-semibold">{{ $contribution->member?->full_name ?? 'Guest' }}</p>
                            @if($contribution->member?->member_number)
                                <p class="text-[11px] text-gray-500">{{ $contribution->member?->member_number }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-700">
                            {{ $contribution->is_anonymous ? 'Anonymous' : ($contribution->contributor_name ?? 'Anonymous') }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-700">
                            <p>{{ $contribution->contributor_email ?? 'N/A' }}</p>
                            <p class="text-[11px] text-gray-500">{{ $contribution->contributor_phone ?? 'N/A' }}</p>
                        </td>
                        <td class="px-4 py-3 text-xs font-semibold text-green-700">UGX {{ number_format($contribution->amount, 0) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-700">{{ $contribution->paymentMethod?->display_name ?? $contribution->paymentMethod?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-700">
                            {{ in_array(optional($contribution->transaction?->transactionType)->name, ['transfer', 'withdrawal'], true) ? 'Savings Transfer' : 'Deposit' }}
                        </td>
                        <td class="px-4 py-3 text-xs">
                            @if($contribution->receipt_issued)
                                <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-[10px] font-bold">Issued</span>
                                <p class="text-[11px] text-gray-500 mt-1">{{ $contribution->receipt_number ?? 'No #'}} </p>
                            @else
                                <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold">Not Issued</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs">
                            @if($contribution->thank_you_sent)
                                <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold">Sent</span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ optional($contribution->contribution_date)->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-xs text-gray-700">{{ Str::limit($contribution->message ?? 'N/A', 40) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-700">{{ Str::limit($contribution->notes ?? 'N/A', 40) }}</td>
                        <td class="px-4 py-3 text-xs">
                            <div class="flex items-center gap-1">
                                <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.contributions.show', [$fundraising->id, $contribution->id]) }}" class="px-2.5 py-1 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-all">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.contributions.edit', [$fundraising->id, $contribution->id]) }}" class="px-2.5 py-1 text-xs font-bold text-white bg-yellow-600 hover:bg-yellow-700 rounded-lg transition-all">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.contributions.print', [$fundraising->id, $contribution->id]) }}" class="px-2.5 py-1 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-all">
                                    <i class="fas fa-print mr-1"></i>Print
                                </a>
                                <form action="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.fundraising.contributions.destroy', [$fundraising->id, $contribution->id]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this contribution?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-all">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="px-6 py-10 text-center text-sm text-gray-500">No contributions yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>