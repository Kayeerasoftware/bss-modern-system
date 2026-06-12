@extends('layouts.member')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Transaction Details</h1>
            <p class="text-gray-600">View transaction information</p>
        </div>
        <a href="{{ route('member.transactions.history') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
        <div class="text-center mb-6 pb-6 border-b">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">UGX 500,000</h2>
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Completed</span>
        </div>

        <div class="space-y-4">
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-600">Transaction ID</span>
                <span class="font-medium">TXN-2024-001</span>
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-600">Type</span>
                <span class="font-medium">Deposit</span>
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-600">Date</span>
                <span class="font-medium">Jan 15, 2024 10:30 AM</span>
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-600">Payment Method</span>
                <span class="font-medium">Mobile Money</span>
            </div>
            <div class="flex justify-between py-3 border-b">
                <span class="text-gray-600">Reference</span>
                <span class="font-medium">MM-123456789</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-gray-600">Description</span>
                <span class="font-medium">Monthly savings deposit</span>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t flex gap-3">
            <button class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i>Download Receipt
            </button>
            <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <i class="fas fa-print"></i>
            </button>
        </div>
    </div>
</div>
@endsection

