@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="max-w-sm w-full">
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header with Logo -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-5 text-center">
            <img src="{{ asset('assets/images/logo.png') }}" alt="BSS Logo" class="w-20 h-20 mx-auto mb-2 rounded-lg shadow-lg">
            <h1 class="text-xl font-bold text-white">BSS Investment Group</h1>
            <p class="text-blue-100 text-xs mt-1">Business Support System</p>
        </div>

        <!-- Reset Password Form -->
        <div class="p-5">
            <h2 class="text-lg font-bold text-gray-800 mb-1">Reset Password</h2>
            <p class="text-gray-500 text-xs mb-3">Enter your new password</p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label class="block text-gray-700 text-xs font-semibold mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                           class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:border-blue-500">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-3">
                    <label class="block text-gray-700 text-xs font-semibold mb-1">New Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:border-blue-500 pr-10">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-3">
                    <label class="block text-gray-700 text-xs font-semibold mb-1">Confirm Password</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:border-blue-500 pr-10">
                        <button type="button" onclick="togglePasswordConfirm()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="toggleIconConfirm"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2 rounded-lg font-semibold text-sm hover:from-blue-700 hover:to-purple-700 transition-all">
                    Reset Password
                </button>
            </form>

            <p class="text-center text-xs text-gray-600 mt-3">
                Remember your password? 
                <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:text-blue-800 hover:underline">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
