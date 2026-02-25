@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-blue-600 to-cyan-600 p-2 md:p-4 rounded-xl shadow-xl">
                    <i class="fas fa-users text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent mb-1">Members Directory</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">View member information and transaction history</p>
            </div>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4">
        <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl text-blue-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Members data...</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-blue-100 text-[10px] font-medium mb-0.5">Total Members</p>
            <h3 class="text-xl font-bold">{{ $members->total() }}</h3>
        </div>
        <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-cyan-100 text-[10px] font-medium mb-0.5">Active Members</p>
            <h3 class="text-xl font-bold">{{ $members->where('status', 'active')->count() }}</h3>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-purple-100 text-[10px] font-medium mb-0.5">Total Balance</p>
            <h3 class="text-xl font-bold">{{ number_format($members->sum('balance')/1000000, 1) }}M</h3>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-green-100 text-[10px] font-medium mb-0.5">New This Month</p>
            <h3 class="text-xl font-bold">{{ $members->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
        </div>
    </div>

    <div class="bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-50 rounded-2xl shadow-lg border border-blue-100 overflow-hidden mb-4">
        <form method="GET" action="{{ route('cashier.members.index') }}" x-data="{ showAdvanced: false }">
            <div class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-blue-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-blue-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search members..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-circle absolute left-2.5 top-1/2 transform -translate-y-1/2 text-cyan-400 text-[8px]"></i>
                            <select name="status" class="w-full pl-8 pr-2 py-1.5 text-xs border border-cyan-200 rounded-lg focus:ring-2 focus:ring-cyan-500 appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                <option value="balance_high" {{ request('sort') == 'balance_high' ? 'selected' : '' }}>Balance (High-Low)</option>
                                <option value="balance_low" {{ request('sort') == 'balance_low' ? 'selected' : '' }}>Balance (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg hover:shadow-md transition flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg hover:shadow-lg transition font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('cashier.members.index') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>

                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-blue-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <div class="flex gap-1">
                                <input type="number" name="balance_min" value="{{ request('balance_min') }}" placeholder="Min Balance" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white" @change="$el.form.submit()">
                                <input type="number" name="balance_max" value="{{ request('balance_max') }}" placeholder="Max" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white" @change="$el.form.submit()">
                            </div>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y-0">
                <thead class="bg-gradient-to-r from-blue-600 via-cyan-600 to-blue-600">
                    <tr class="border-b-2 border-white/20">
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Member</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Balance</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($members as $member)
                    <tr class="transition-all duration-200 {{ $loop->iteration % 2 == 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 border-l-4 border-blue-500 cursor-pointer" onclick="window.location='{{ route('cashier.members.show', $member->id) }}'">
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    @if($member->profile_picture)
                                        <img src="{{ asset('storage/' . $member->profile_picture) }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-blue-500 ring-offset-2">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center ring-2 ring-blue-500 ring-offset-2">
                                            <span class="text-white font-bold text-lg">{{ substr($member->full_name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $member->full_name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $member->member_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <div class="text-sm text-gray-900">{{ $member->contact }}</div>
                            <div class="text-xs text-gray-500">{{ $member->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-wallet text-green-500"></i>
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($member->balance ?? 0) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-circle text-[8px] mr-1"></i>
                                {{ ucfirst($member->status ?? 'active') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('cashier.members.show', $member->id) }}" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition inline-block">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg font-medium">No members found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $members->links() }}</div>
    </div>
</div>

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
@keyframes slide-text { 0% { left: 0%; opacity: 1; } 95% { opacity: 1; } 100% { left: 100%; opacity: 0; } }
.animate-slide-text { animation: slide-text 5s ease-out forwards; }
tr { position: relative; }
tr::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to right, transparent, #3b82f6, #06b6d4, transparent);
    opacity: 0.3;
}
tr:hover::after { opacity: 0.6; }
</style>
@endsection
