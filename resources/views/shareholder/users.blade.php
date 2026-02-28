@extends('layouts.shareholder')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 p-2 md:p-4 rounded-xl shadow-xl">
                    <i class="fas fa-user-shield text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-1">Shareholders Directory</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">View all shareholders and their financial information</p>
            </div>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4">
        <div class="h-full bg-gradient-to-r from-purple-500 to-pink-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl text-purple-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading shareholders data...</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-purple-100 text-[10px] font-medium mb-0.5">Total Shareholders</p>
            <h3 class="text-xl font-bold">{{ number_format($stats['total']) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-green-100 text-[10px] font-medium mb-0.5">Active</p>
            <h3 class="text-xl font-bold">{{ number_format($stats['active']) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-blue-100 text-[10px] font-medium mb-0.5">Total Investments</p>
            <h3 class="text-xl font-bold">UGX 0</h3>
        </div>
        <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-pink-100 text-[10px] font-medium mb-0.5">Total Dividends</p>
            <h3 class="text-xl font-bold">UGX 0</h3>
        </div>
    </div>

    <!-- Advanced Search and Filters -->
    <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
        <form method="GET" action="{{ route('shareholder.users') }}" x-data="{ showAdvanced: false }">
            <div class="bg-white/60 backdrop-blur-sm p-3">
                <!-- Basic Search Section -->
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search shareholders..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
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
                            <a href="{{ route('shareholder.users') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Filters Section -->
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
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
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Shareholder</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Contact Info</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Joined</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Financial Records</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($users as $user)
                    <tr class="transition-all duration-200 {{ $loop->iteration % 2 == 0 ? 'bg-purple-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 border-l-4 border-purple-500">
                        <td class="px-6 py-4 whitespace-nowrap relative border-r border-gray-200">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center ring-2 ring-purple-500 ring-offset-2">
                                    <span class="text-white font-bold text-lg">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">ID: #{{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200">
                            <div class="space-y-1">
                                <p class="text-sm text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                    {{ $user->email }}
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
                                    'admin' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                ];
                                $colorClass = $roleColors[$user->role ?? 'client'] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-full border {{ $colorClass }}">
                                {{ ucfirst($user->role ?? 'Client') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            @if($user->is_active)
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-circle text-[8px] mr-1"></i>
                                Active
                            </span>
                            @else
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                <i class="fas fa-circle text-[8px] mr-1"></i>
                                Inactive
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $user->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="#" class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-all duration-200 group" title="Investments">
                                    <i class="fas fa-chart-line group-hover:scale-110 transition-transform"></i>
                                </a>
                                <a href="#" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all duration-200 group" title="Dividends">
                                    <i class="fas fa-coins group-hover:scale-110 transition-transform"></i>
                                </a>
                                <a href="#" class="p-2 bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-200 transition-all duration-200 group" title="Transactions">
                                    <i class="fas fa-exchange-alt group-hover:scale-110 transition-transform"></i>
                                </a>
                                <a href="#" class="p-2 bg-pink-100 text-pink-600 rounded-lg hover:bg-pink-200 transition-all duration-200 group" title="Portfolio">
                                    <i class="fas fa-briefcase group-hover:scale-110 transition-transform"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-user-shield text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg font-medium">No shareholders found</p>
                                <p class="text-gray-400 text-sm mt-2">No shareholders available in the system</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $users->links() }}
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
