@extends('reports.pdf-template')

@section('content')
<div class="summary">
    <h3>Loan Portfolio Summary</h3>
    <div class="stat">
        <div class="stat-label">Total Loans</div>
        <div class="stat-value">{{ count($data) }}</div>
    </div>
    <div class="stat">
        <div class="stat-label">Total Amount</div>
        <div class="stat-value">UGX {{ number_format($data->sum('amount')) }}</div>
    </div>
    <div class="stat">
        <div class="stat-label">Avg Loan</div>
        <div class="stat-value">UGX {{ number_format($data->avg('amount')) }}</div>
    </div>
</div>

<div class="info-box">
    <h3>Loans by Status</h3>
    <table>
        @php
            $statusGroups = $data->groupBy('status');
        @endphp
        @foreach($statusGroups as $status => $loans)
        <tr>
            <td><strong>{{ ucfirst($status) }}</strong></td>
            <td style="text-align: right;">{{ $loans->count() }} loans</td>
            <td style="text-align: right;">UGX {{ number_format($loans->sum('amount')) }}</td>
        </tr>
        @endforeach
    </table>
</div>

<h3 style="margin-top: 30px;">Detailed Loan List</h3>
<table>
    <thead>
        <tr>
            <th>Loan ID</th>
            <th>Member ID</th>
            <th>Amount (UGX)</th>
            <th>Status</th>
            <th>Purpose</th>
            <th>Updated By</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $loan)
        <tr>
            <td>{{ $loan->loan_id }}</td>
            <td>{{ $loan->member_id }}</td>
            <td>{{ number_format($loan->amount) }}</td>
            <td><strong>{{ ucfirst($loan->status) }}</strong></td>
            <td>{{ $loan->purpose }}</td>
            <td>{{ $loan->updated_by ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
