@extends('reports.pdf-template')

@section('content')
<div class="summary">
    <h3>Transaction Summary</h3>
    <div class="stat">
        <div class="stat-label">Total Transactions</div>
        <div class="stat-value">{{ count($data) }}</div>
    </div>
    <div class="stat">
        <div class="stat-label">Total Volume</div>
        <div class="stat-value">UGX {{ number_format($data->sum('amount')) }}</div>
    </div>
</div>

<div class="info-box">
    <h3>Transactions by Type</h3>
    <table>
        @php
            $typeGroups = $data->groupBy('type');
        @endphp
        @foreach($typeGroups as $type => $transactions)
        <tr>
            <td><strong>{{ ucfirst($type) }}</strong></td>
            <td style="text-align: right;">{{ $transactions->count() }} transactions</td>
            <td style="text-align: right;">UGX {{ number_format($transactions->sum('amount')) }}</td>
        </tr>
        @endforeach
    </table>
</div>

<h3 style="margin-top: 30px;">Detailed Transaction List</h3>
<table>
    <thead>
        <tr>
            <th>Transaction ID</th>
            <th>Member ID</th>
            <th>Type</th>
            <th>Amount (UGX)</th>
            <th>Description</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $transaction)
        <tr>
            <td>{{ $transaction->transaction_id }}</td>
            <td>{{ $transaction->member_id }}</td>
            <td><strong>{{ ucfirst($transaction->type) }}</strong></td>
            <td>{{ number_format($transaction->amount) }}</td>
            <td>{{ $transaction->description ?? '-' }}</td>
            <td>{{ date('Y-m-d H:i', strtotime($transaction->created_at)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
