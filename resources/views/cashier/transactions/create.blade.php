@extends('layouts.cashier')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">New Transaction</h2>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('cashier.transactions.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Member</label>
                <select name="member_id" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Select Member</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}">{{ $member->name }} - {{ $member->member_id }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select name="type" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="deposit">Deposit</option>
                    <option value="withdrawal">Withdrawal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                <input type="number" name="amount" step="0.01" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reference</label>
                <input type="text" name="reference" class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Process Transaction</button>
        </div>
    </form>
</div>
@endsection
