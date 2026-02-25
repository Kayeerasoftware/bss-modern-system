@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.fundraising.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-purple-600"></i>
        </a>
        <div>
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 via-pink-600 to-orange-600 bg-clip-text text-transparent">Edit Campaign</h2>
            <p class="text-gray-600 text-sm">Update campaign details - ID: {{ $fundraising->campaign_id }}</p>
        </div>
    </div>

    <form action="{{ route('admin.fundraising.update', $fundraising->id) }}" method="POST" class="max-w-5xl mx-auto">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-orange-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center shadow-2xl">
                        <i class="fas fa-hand-holding-heart text-purple-600 text-6xl"></i>
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold">{{ $fundraising->campaign_id }}</h3>
                <p class="text-white/80 text-sm">Update campaign information</p>
            </div>

            <!-- Form Content -->
            <div class="p-6 md:p-8 space-y-8">
                <!-- Campaign Information -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-purple-600 to-pink-600">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-bullhorn text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Campaign Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-heading text-purple-600"></i>
                                Campaign Title *
                            </label>
                            <input type="text" name="title" value="{{ old('title', $fundraising->title) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                            @error('title')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-align-left text-pink-600"></i>
                                Description *
                            </label>
                            <textarea name="description" rows="4" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm">{{ old('description', $fundraising->description) }}</textarea>
                            @error('description')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-bullseye text-orange-600"></i>
                                Target Amount *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="hidden" name="target_amount" id="target_amount" value="{{ old('target_amount', $fundraising->target_amount) }}">
                                <input type="text" id="target_display" value="{{ number_format(old('target_amount', $fundraising->target_amount)) }}" required oninput="formatAmount(this)"
                                       class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm">
                            </div>
                            @error('target_amount')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-circle text-green-600 text-[8px]"></i>
                                Status *
                            </label>
                            <select name="status" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm appearance-none bg-white">
                                <option value="active" {{ old('status', $fundraising->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status', $fundraising->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $fundraising->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calendar-check text-blue-600"></i>
                                Start Date *
                            </label>
                            <input type="date" name="start_date" value="{{ old('start_date', $fundraising->start_date->format('Y-m-d')) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                            @error('start_date')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calendar-times text-red-600"></i>
                                End Date *
                            </label>
                            <input type="date" name="end_date" value="{{ old('end_date', $fundraising->end_date->format('Y-m-d')) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm">
                            @error('end_date')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('admin.fundraising.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-600 via-pink-600 to-orange-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Update Campaign
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function formatAmount(input) {
    let value = input.value.replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
        document.getElementById('target_amount').value = value;
        let num = parseFloat(value);
        input.value = num.toLocaleString('en-US');
    } else {
        document.getElementById('target_amount').value = '';
    }
}
</script>
@endsection
