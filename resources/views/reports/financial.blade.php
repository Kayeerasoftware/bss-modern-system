@extends('reports.pdf-template')

@section('content')
<div class="summary">
    <h3>Financial Summary</h3>
    <div class="stat">
        <div class="stat-label">Total Savings</div>
        <div class="stat-value">UGX {{ number_format($data['total_savings']) }}</div>
    </div>
    <div class="stat">
        <div class="stat-label">Total Loans</div>
        <div class="stat-value">UGX {{ number_format($data['total_loans']) }}</div>
    </div>
    <div class="stat">
        <div class="stat-label">Net Balance</div>
        <div class="stat-value">UGX {{ number_format($data['net_balance']) }}</div>
    </div>
</div>

<div class="info-box">
    <h3>Income & Expenses</h3>
    <table>
        <tr>
            <td><strong>Total Deposits</strong></td>
            <td style="text-align: right; color: #10b981;">UGX {{ number_format($data['total_deposits']) }}</td>
        </tr>
        <tr>
            <td><strong>Total Withdrawals</strong></td>
            <td style="text-align: right; color: #ef4444;">UGX {{ number_format($data['total_withdrawals']) }}</td>
        </tr>
        <tr>
            <td><strong>Loan Interest Income</strong></td>
            <td style="text-align: right; color: #10b981;">UGX {{ number_format($data['loan_interest']) }}</td>
        </tr>
        <tr>
            <td><strong>Processing Fees</strong></td>
            <td style="text-align: right; color: #10b981;">UGX {{ number_format($data['processing_fees']) }}</td>
        </tr>
        <tr style="background: #eff6ff; font-weight: bold;">
            <td><strong>Net Cash Flow</strong></td>
            <td style="text-align: right;">UGX {{ number_format($data['net_cash_flow']) }}</td>
        </tr>
    </table>
</div>

<div class="info-box">
    <h3>Loan Portfolio Analysis</h3>
    <table>
        <tr>
            <td><strong>Total Loans Issued</strong></td>
            <td style="text-align: right;">{{ $data['loans_count'] }}</td>
        </tr>
        <tr>
            <td><strong>Approved Loans</strong></td>
            <td style="text-align: right;">{{ $data['approved_loans'] }}</td>
        </tr>
        <tr>
            <td><strong>Pending Loans</strong></td>
            <td style="text-align: right;">{{ $data['pending_loans'] }}</td>
        </tr>
        <tr>
            <td><strong>Rejected Loans</strong></td>
            <td style="text-align: right;">{{ $data['rejected_loans'] }}</td>
        </tr>
        <tr style="background: #eff6ff;">
            <td><strong>Average Loan Amount</strong></td>
            <td style="text-align: right;">UGX {{ number_format($data['avg_loan_amount']) }}</td>
        </tr>
    </table>
</div>

<div class="info-box">
    <h3>Member Statistics</h3>
    <table>
        <tr>
            <td><strong>Total Members</strong></td>
            <td style="text-align: right;">{{ $data['total_members'] }}</td>
        </tr>
        <tr>
            <td><strong>Active Members</strong></td>
            <td style="text-align: right;">{{ $data['active_members'] }}</td>
        </tr>
        <tr>
            <td><strong>Average Savings per Member</strong></td>
            <td style="text-align: right;">UGX {{ number_format($data['avg_savings']) }}</td>
        </tr>
    </table>
</div>

<div class="info-box">
    <h3>Transaction Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Transaction Type</th>
                <th style="text-align: right;">Count</th>
                <th style="text-align: right;">Total Amount (UGX)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['transaction_breakdown'] as $type => $info)
            <tr>
                <td>{{ ucfirst($type) }}</td>
                <td style="text-align: right;">{{ $info['count'] }}</td>
                <td style="text-align: right;">{{ number_format($info['amount']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
