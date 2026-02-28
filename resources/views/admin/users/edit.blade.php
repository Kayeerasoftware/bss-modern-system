@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 p-3 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-indigo-600"></i>
        </a>
        <div>
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">Edit User</h2>
            <p class="text-gray-600 text-sm">Update user account information</p>
        </div>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="max-w-4xl mx-auto">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="relative group">
                        <div class="w-24 h-24 rounded-full bg-white flex items-center justify-center shadow-2xl overflow-hidden">
                            @if($user->profile_picture_url)
                                <img id="preview" src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <img id="preview" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=96&background=random&bold=true" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <label for="profile_picture" class="absolute bottom-0 right-0 bg-white text-indigo-600 p-2 rounded-full cursor-pointer hover:bg-indigo-50 transition-all shadow-lg transform group-hover:scale-110">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden" onchange="previewImage(event)">
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold">{{ $user->name }}</h3>
                <p class="text-white/80 text-sm">{{ ucfirst($user->role) }}</p>
            </div>

            <div class="p-6 md:p-8 space-y-8">
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-user text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">User Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user-circle text-indigo-600"></i>
                                Full Name *
                            </label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                            @error('name')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-envelope text-purple-600"></i>
                                Email Address *
                            </label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                            @error('email')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-phone text-green-600"></i>
                                Phone Number
                            </label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm">
                            @error('phone')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-map-marker-alt text-red-600"></i>
                                Location
                            </label>
                            <input type="text" name="location" value="{{ old('location', $user->location) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm">
                            @error('location')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user-tag text-indigo-600"></i>
                                User Roles *
                            </label>
                            <div class="space-y-3 p-4 border-2 border-gray-200 rounded-xl bg-gray-50">
                                @php
                                    $userRoles = $user->roles_list ?? [];
                                    $availableRoles = [
                                        'admin' => 'Admin',
                                        'client' => 'Client',
                                        'shareholder' => 'Shareholder',
                                        'cashier' => 'Cashier',
                                        'td' => 'Technical Director',
                                        'ceo' => 'CEO'
                                    ];
                                @endphp
                                @foreach($availableRoles as $roleValue => $roleLabel)
                                    <label class="flex items-center gap-3 cursor-pointer hover:bg-white p-2 rounded-lg transition-all">
                                        <input type="checkbox" name="roles[]" value="{{ $roleValue }}" 
                                               {{ in_array($roleValue, $userRoles) ? 'checked' : '' }}
                                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">{{ $roleLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-star text-amber-500"></i>
                                    Default Role *
                                </label>
                                <select name="default_role" class="w-full px-3 py-2 text-sm border-2 border-amber-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white">
                                    @foreach($availableRoles as $roleValue => $roleLabel)
                                        <option value="{{ $roleValue }}" {{ old('default_role', $user->role ?? 'client') === $roleValue ? 'selected' : '' }}>{{ $roleLabel }}</option>
                                    @endforeach
                                </select>
                                @error('default_role')
                                    <p class="text-xs text-red-600 flex items-center gap-1 mt-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Select one or more roles for this user</p>
                            @error('roles')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-key text-pink-600"></i>
                                New Password
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password" placeholder="Leave blank to keep current"
                                       class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm">
                                <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-pink-600">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500">Leave blank to keep current password</p>
                            @error('password')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('admin.users.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Update User
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    const icon = document.getElementById(id + '-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection

