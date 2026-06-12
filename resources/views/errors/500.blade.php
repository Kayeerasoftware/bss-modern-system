@extends('layouts.guest')

@section('title', '500 - Server Error')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-800">500</h1>
        <p class="text-2xl font-semibold text-gray-600 mt-4">Server Error</p>
        <p class="text-gray-500 mt-2">Something went wrong on our end.</p>
        <a href="{{ route('welcome') }}" class="mt-6 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
            Go Home
        </a>
    </div>
</div>
@endsection
