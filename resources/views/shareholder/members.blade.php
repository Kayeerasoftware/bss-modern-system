@extends('layouts.shareholder')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 p-2 md:p-4 rounded-xl shadow-xl">
                    <i class="fas fa-users text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-1">Members Directory</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">View organization members and their information</p>
            </div>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4">
        <div class="h-full bg-gradient-to-r from-purple-500 to-pink-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl text-purple-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Members data...</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-purple-100 text-[10px] font-medium mb-0.5">Total Members</p>
            <h3 class="text-xl font-bold">{{ number_format($stats['total']) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-pink-100 text-[10px] font-medium mb-0.5">Active Members</p>
            <h3 class="text-xl font-bold">{{ number_format($stats['active']) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-indigo-100 text-[10px] font-medium mb-0.5">Shareholders</p>
            <h3 class="text-xl font-bold">{{ number_format($stats['shareholders']) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-violet-100 text-[10px] font-medium mb-0.5">New This Month</p>
            <h3 class="text-xl font-bold">{{ number_format($stats['newThisMonth']) }}</h3>
        </div>
    </div>

    <!-- Advanced Search and Filters -->
    <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
        <form method="GET" action="{{ route('shareholder.members') }}" x-data="{ showAdvanced: false }">
            <div class="bg-white/60 backdrop-blur-sm p-3">
                <!-- Basic Search Section -->
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search members..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-user-tag absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="role" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="client" {{ request('role') == 'client' ? 'selected' : '' }}>Client</option>
                                <option value="shareholder" {{ request('role') == 'shareholder' ? 'selected' : '' }}>Shareholder</option>
                                <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                                <option value="td" {{ request('role') == 'td' ? 'selected' : '' }}>TD</option>
                                <option value="ceo" {{ request('role') == 'ceo' ? 'selected' : '' }}>CEO</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                @if(\App\Models\Setting::get('shareholder_hide_savings', 0) == 0)
                                <option value="savings_high" {{ request('sort') == 'savings_high' ? 'selected' : '' }}>Savings (High-Low)</option>
                                <option value="savings_low" {{ request('sort') == 'savings_low' ? 'selected' : '' }}>Savings (Low-High)</option>
                                @endif
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('shareholder.members') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Filters Section -->
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            @if(\App\Models\Setting::get('shareholder_hide_savings', 0) == 0)
                            <div class="flex gap-1">
                                <input type="number" name="savings_min" value="{{ request('savings_min') }}" placeholder="Min Savings" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                                <input type="number" name="savings_max" value="{{ request('savings_max') }}" placeholder="Max" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            </div>
                            @else
                            <div class="flex items-center justify-center bg-red-50 border border-red-200 rounded-lg px-2 py-1.5">
                                <span class="text-xs text-red-600 font-semibold">Savings filters hidden</span>
                            </div>
                            @endif
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
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
                <thead class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600">
                    <tr class="border-b-2 border-white/20">
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Member</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Savings</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Last Message</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($members as $member)
                    <tr class="transition-all duration-200 {{ $loop->iteration % 2 == 0 ? 'bg-purple-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 border-l-4 border-purple-500 cursor-pointer" onclick="window.location='{{ route('shareholder.members.show', $member->id) }}'">
                        <td class="px-6 py-4 whitespace-nowrap relative border-r border-gray-200">
                            <div class="flex items-center gap-4">
                                <div class="relative w-12 h-12 rounded-full overflow-hidden shrink-0">
                                    @if($member->profile_picture_url)
                                        <img src="{{ $member->profile_picture_url }}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';" class="w-12 h-12 rounded-full object-cover ring-2 ring-purple-500 ring-offset-2 shrink-0" alt="">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center ring-2 ring-purple-500 ring-offset-2 shrink-0">
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
                            @php
                                $roleColors = [
                                    'client' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'shareholder' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'cashier' => 'bg-green-100 text-green-800 border-green-200',
                                    'td' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'ceo' => 'bg-red-100 text-red-800 border-red-200',
                                ];
                                $colorClass = $roleColors[$member->role ?? 'client'] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-full border {{ $colorClass }}">
                                {{ ucfirst($member->role ?? 'Client') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-coins text-yellow-500"></i>
                                @if(\App\Models\Setting::get('shareholder_hide_savings', 0) == 1 && $member->user_id !== auth()->id())
                                    <span class="text-sm font-semibold text-red-600 bg-red-50 px-2 py-1 rounded">HIDDEN</span>
                                @else
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($member->savings ?? 0, 2) }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-circle text-[8px] mr-1"></i>
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200">
                            <div class="flex items-center justify-end gap-3">
                                @if(isset($member->last_message) && $member->last_message)
                                    <div class="text-right">
                                        <p class="text-xs text-gray-700 flex items-center justify-end gap-1">
                                            @if($member->last_message->sender_id === (auth()->user()->member->member_id ?? null))
                                                <i class="fas fa-check{{ $member->last_message->is_read ? '-double text-blue-500' : ' text-gray-400' }} text-[10px]"></i>
                                            @endif
                                            <i class="far fa-comment-dots text-purple-500"></i>
                                            <span class="font-medium">{{ Str::limit($member->last_message->message, 25) }}</span>
                                        </p>
                                        <p class="text-[10px] text-gray-500 mt-0.5">
                                            <i class="far fa-clock mr-1"></i>{{ $member->last_message->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if(isset($member->unread_count) && $member->unread_count > 0)
                                        <span class="bg-green-500 text-white text-[10px] font-bold rounded-full w-6 h-6 flex items-center justify-center ring-2 ring-green-200">{{ $member->unread_count > 9 ? '9+' : $member->unread_count }}</span>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 italic">No messages yet</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('shareholder.members.show', $member->id) }}" class="p-2 bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-200 transition-all duration-200 group inline-block" title="View Member">
                                <i class="fas fa-eye group-hover:scale-110 transition-transform"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg font-medium">No members found</p>
                                <p class="text-gray-400 text-sm mt-2">No members available</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $members->links() }}
        </div>
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
    background: linear-gradient(to right, transparent, #a855f7, #ec4899, transparent);
    opacity: 0.3;
}
tr:hover::after { opacity: 0.6; }
</style>
@endsection
