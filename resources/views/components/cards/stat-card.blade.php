<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-600">{{ $title }}</p>
            <p class="text-2xl font-bold text-{{ $color }}-600">{{ $value }}</p>
        </div>
        <div class="text-3xl text-{{ $color }}-600">
            <i class="fas fa-{{ $icon }}"></i>
        </div>
    </div>
</div>
