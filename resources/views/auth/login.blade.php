@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="max-w-sm w-full">
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header with Logo -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-5 text-center">
            <img src="{{ asset('assets/images/logo.png') }}" alt="BSS Logo" class="w-20 h-20 mx-auto mb-2 rounded-lg shadow-lg">
            <h1 class="text-xl font-bold text-white">BSS Investment Group</h1>
            <p class="text-blue-100 text-xs mt-1">Business Support System</p>
        </div>

        <!-- Login Form -->
        <div class="p-5">
            <!-- Success Message with Progress Bar -->
            @if (session('login_success'))
                <div id="successMessage" class="mb-3 relative overflow-hidden bg-green-50 border-2 border-green-500 rounded-lg p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                        <span class="text-green-800 font-bold text-sm">Logged in successfully as {{ session('login_role') }}</span>
                    </div>
                    <div class="h-1 bg-green-200 rounded-full overflow-hidden">
                        <div id="progressBar" class="h-full bg-green-600"></div>
                    </div>
                </div>
                <script>
                    const progressBar = document.getElementById('progressBar');
                    progressBar.style.width = '100%';
                    progressBar.style.transition = 'width 3s linear';
                    
                    setTimeout(() => {
                        progressBar.style.width = '0%';
                    }, 10);
                    
                    setTimeout(() => {
                        window.location.href = '{{ route('dashboard') }}';
                    }, 3000);
                </script>
            @endif

            @if (session('register_success'))
                <div class="mb-3 bg-blue-50 border-2 border-blue-500 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-user-check text-blue-600 text-lg mt-0.5"></i>
                        <div>
                            <p class="text-blue-900 font-bold text-sm">
                                Registration successful as {{ session('register_role') }}
                            </p>
                            <p class="text-blue-700 text-xs">
                                Please select your role and sign in.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <h2 class="text-lg font-bold text-gray-800 mb-1">Welcome Back</h2>
            <p class="text-gray-500 text-xs mb-3">Sign in to your account</p>

            @if (session('status'))
                <div class="mb-3 p-2 bg-green-50 text-green-700 rounded text-xs">{{ session('status') }}</div>
            @endif

            @error('email')
                @if($message == 'Invalid credentials')
                    <!-- Invalid Credentials Error -->
                    <div class="mb-4 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-indigo-500 opacity-10 animate-pulse"></div>
                        <div class="relative p-5 bg-gradient-to-br from-purple-50 to-indigo-50 border-2 border-purple-400 rounded-2xl shadow-2xl animate-shake">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-purple-500 rounded-full blur-md opacity-50 animate-ping"></div>
                                        <div class="relative bg-gradient-to-br from-purple-500 to-indigo-600 p-3 rounded-full shadow-lg">
                                            <i class="fas fa-lock text-white text-2xl"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-extrabold text-purple-900 mb-2 flex items-center gap-2">
                                        <i class="fas fa-shield-alt"></i>
                                        Authentication Failed
                                    </h3>
                                    <p class="text-sm font-semibold text-purple-800 mb-2">The email or password you entered is incorrect.</p>
                                    <div class="mt-3 p-3 bg-white/60 rounded-lg border border-purple-200">
                                        <p class="text-xs text-purple-700 leading-relaxed">
                                            <i class="fas fa-key mr-1"></i>
                                            <strong>Troubleshooting tips:</strong><br>
                                            • Double-check your email address for typos<br>
                                            • Ensure your password is entered correctly (check Caps Lock)<br>
                                            • Try using the "Forgot password?" link below if you can't remember your password
                                        </p>
                                    </div>
                                    <div class="mt-3 flex items-center gap-2 text-xs text-purple-600">
                                        <i class="fas fa-question-circle"></i>
                                        <span class="font-semibold">Still having trouble? <a href="{{ route('password.request') }}" class="text-purple-700 underline hover:text-purple-900 font-bold">Reset your password</a></span>
                                    </div>
                                </div>
                                <button type="button" onclick="this.closest('.mb-4').remove()" class="flex-shrink-0 text-purple-400 hover:text-purple-700 transition-colors p-1 hover:bg-purple-100 rounded-full">
                                    <i class="fas fa-times-circle text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @enderror

            @error('role')
                @if($message == 'The role field is required.')
                    <!-- Role Not Selected Error -->
                    <div class="mb-4 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-yellow-500 to-orange-500 opacity-10 animate-pulse"></div>
                        <div class="relative p-5 bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-400 rounded-2xl shadow-2xl animate-bounce-slow">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-yellow-500 rounded-full blur-md opacity-50 animate-ping"></div>
                                        <div class="relative bg-gradient-to-br from-yellow-500 to-orange-600 p-3 rounded-full shadow-lg">
                                            <i class="fas fa-hand-pointer text-white text-2xl"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-extrabold text-yellow-900 mb-2 flex items-center gap-2">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Role Selection Required
                                    </h3>
                                    <p class="text-sm font-semibold text-yellow-800 mb-2">Please select a role to continue with login.</p>
                                    <div class="mt-3 p-3 bg-white/60 rounded-lg border border-yellow-200">
                                        <p class="text-xs text-yellow-700 leading-relaxed">
                                            <i class="fas fa-lightbulb mr-1"></i>
                                            <strong>How to fix this:</strong><br>
                                            Click on the "Select only Active role" dropdown above and choose your appropriate role from the list. Make sure to select a role marked with <span class="text-green-600 font-bold">[Active role]</span> in green.
                                        </p>
                                    </div>
                                </div>
                                <button type="button" onclick="this.closest('.mb-4').remove()" class="flex-shrink-0 text-yellow-400 hover:text-yellow-700 transition-colors p-1 hover:bg-yellow-100 rounded-full">
                                    <i class="fas fa-times-circle text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @elseif(str_contains($message, 'does not match your registered role'))
                    <!-- Role Mismatch Error -->
                    <div class="mb-4 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-amber-500 to-yellow-500 opacity-10 animate-pulse"></div>
                        <div class="relative p-5 bg-gradient-to-br from-amber-50 to-yellow-50 border-2 border-amber-400 rounded-2xl shadow-2xl animate-shake">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-amber-500 rounded-full blur-md opacity-50 animate-ping"></div>
                                        <div class="relative bg-gradient-to-br from-amber-500 to-yellow-600 p-3 rounded-full shadow-lg">
                                            <i class="fas fa-user-times text-white text-2xl"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-extrabold text-amber-900 mb-2 flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Role Mismatch
                                    </h3>
                                    <p class="text-sm font-semibold text-amber-800 mb-2">{{ $message }}</p>
                                    <div class="mt-3 p-3 bg-white/60 rounded-lg border border-amber-200">
                                        <p class="text-xs text-amber-700 leading-relaxed">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>What this means:</strong><br>
                                            Your account is registered with a different role. You must select the role that matches your account registration to login successfully.
                                        </p>
                                    </div>
                                </div>
                                <button type="button" onclick="this.closest('.mb-4').remove()" class="flex-shrink-0 text-amber-400 hover:text-amber-700 transition-colors p-1 hover:bg-amber-100 rounded-full">
                                    <i class="fas fa-times-circle text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Role Inactive Error -->
                    <div class="mb-4 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-pink-500 opacity-10 animate-pulse"></div>
                        <div class="relative p-5 bg-gradient-to-br from-red-50 to-pink-50 border-2 border-red-400 rounded-2xl shadow-2xl animate-shake">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-red-500 rounded-full blur-md opacity-50 animate-ping"></div>
                                        <div class="relative bg-gradient-to-br from-red-500 to-red-600 p-3 rounded-full shadow-lg">
                                            <i class="fas fa-ban text-white text-2xl"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-extrabold text-red-900 mb-2 flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Access Denied - Role Inactive
                                    </h3>
                                    <p class="text-sm font-semibold text-red-800 mb-2">{{ $message }}</p>
                                    <div class="mt-3 p-3 bg-white/60 rounded-lg border border-red-200">
                                        <p class="text-xs text-red-700 leading-relaxed">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>Why is this happening?</strong><br>
                                            The system administrator has temporarily disabled this role for security or maintenance purposes. This role cannot be used for login until it is reactivated.
                                        </p>
                                    </div>
                                    <div class="mt-3 flex items-center gap-2 text-xs text-red-600">
                                        <i class="fas fa-phone-alt"></i>
                                        <span class="font-semibold">
                                            Need help? Contact your system administrator immediately
                                            @if(isset($adminPhone) && $adminPhone)
                                                at <a href="tel:{{ $adminPhone }}" class="text-red-700 underline hover:text-red-900 font-bold">{{ $adminPhone }}</a>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <button type="button" onclick="this.closest('.mb-4').remove()" class="flex-shrink-0 text-red-400 hover:text-red-700 transition-colors p-1 hover:bg-red-100 rounded-full">
                                    <i class="fas fa-times-circle text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @enderror

            <style>
                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    10%, 30%, 50%, 70%, 90% { transform: translateX(-8px); }
                    20%, 40%, 60%, 80% { transform: translateX(8px); }
                }
                .animate-shake {
                    animation: shake 0.6s ease-in-out;
                }
                @keyframes bounce-slow {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-10px); }
                }
                .animate-bounce-slow {
                    animation: bounce-slow 1s ease-in-out 2;
                }
                @keyframes pulse {
                    0%, 100% { opacity: 0.1; }
                    50% { opacity: 0.2; }
                }
                @keyframes ping {
                    75%, 100% {
                        transform: scale(1.5);
                        opacity: 0;
                    }
                }
            </style>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="block text-gray-700 text-xs font-semibold mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', session('registered_email')) }}" required autofocus
                           class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-3">
                    <label class="block text-green-600 text-xs font-semibold mb-1">Select only Active role</label>
                    <div class="relative">
                        <input type="hidden" name="role" id="roleInput" value="{{ old('role', session('registered_role')) }}" required>
                        <div class="w-full px-3 py-2 text-sm border rounded-lg cursor-pointer bg-white" id="roleDropdown">
                            <span id="selectedRole" class="text-gray-500">Select a role...</span>
                        </div>
                        <div id="roleOptions" class="hidden absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            @php
                                $roles = [
                                    'client' => 'Client/Member',
                                    'shareholder' => 'Shareholder',
                                    'cashier' => 'Cashier',
                                    'td' => 'Technical Director',
                                    'ceo' => 'CEO',
                                    'admin' => 'Administrator'
                                ];
                            @endphp
                            @foreach($roles as $roleKey => $roleLabel)
                                @php
                                    $isActive = ($roleStatuses[$roleKey] ?? 1) == 1;
                                @endphp
                                <div class="px-3 py-2 hover:bg-gray-100 cursor-pointer" data-value="{{ $roleKey }}">
                                    <span class="text-gray-800">{{ $roleLabel }}</span>
                                    <span class="font-semibold" style="color: {{ $isActive ? '#16a34a' : '#dc2626' }};">[{{ $isActive ? 'Active role' : 'Inactive role' }}]</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <script>
                    const dropdown = document.getElementById('roleDropdown');
                    const options = document.getElementById('roleOptions');
                    const input = document.getElementById('roleInput');
                    const selected = document.getElementById('selectedRole');

                    dropdown.addEventListener('click', () => {
                        options.classList.toggle('hidden');
                    });

                    document.querySelectorAll('#roleOptions > div').forEach(option => {
                        option.addEventListener('click', () => {
                            input.value = option.dataset.value;
                            selected.innerHTML = option.innerHTML;
                            selected.classList.remove('text-gray-500');
                            options.classList.add('hidden');
                        });
                    });

                    document.addEventListener('click', (e) => {
                        if (!dropdown.contains(e.target) && !options.contains(e.target)) {
                            options.classList.add('hidden');
                        }
                    });

                    const initialRole = input.value;
                    if (initialRole) {
                        const initialOption = Array.from(document.querySelectorAll('#roleOptions > div'))
                            .find(option => option.dataset.value === initialRole);
                        if (initialOption) {
                            selected.innerHTML = initialOption.innerHTML;
                            selected.classList.remove('text-gray-500');
                        }
                    }
                </script>

                <div class="mb-3">
                    <label class="block text-gray-700 text-xs font-semibold mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:border-blue-500 pr-10">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-between mb-3">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        <span class="ml-2 text-xs text-gray-700 font-medium group-hover:text-gray-900">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:text-blue-800 font-semibold hover:underline">Forgot password?</a>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2 rounded-lg font-semibold text-sm hover:from-blue-700 hover:to-purple-700 transition-all">
                    Sign In
                </button>
            </form>

            <p class="text-center text-xs text-gray-600 mt-3">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:text-blue-800 hover:underline">Register here</a>
            </p>
        </div>
    </div>
</div>
@endsection
