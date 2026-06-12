@extends('reports.pdf-template')

@section('content')
<div class="summary">
    <h3>Member Overview</h3>
    <div class="stat">
        <div class="stat-label">Total Members</div>
        <div class="stat-value">{{ count($data) }}</div>
    </div>
    <div class="stat">
        <div class="stat-label">Total Savings</div>
        <div class="stat-value">UGX {{ number_format($data->sum('savings')) }}</div>
    </div>
    <div class="stat">
        <div class="stat-label">Avg Savings</div>
        <div class="stat-value">UGX {{ number_format($data->avg('savings')) }}</div>
    </div>
</div>

<div class="info-box">
    <h3>Members by Role</h3>
    <table>
        @php
            $roleGroups = $data->groupBy('role');
        @endphp
        @foreach($roleGroups as $role => $members)
        <tr>
            <td><strong>{{ ucfirst($role) }}</strong></td>
            <td style="text-align: right;">{{ $members->count() }} members</td>
            <td style="text-align: right;">UGX {{ number_format($members->sum('savings')) }}</td>
        </tr>
        @endforeach
    </table>
</div>

<h3 style="margin-top: 30px;">Detailed Member List</h3>
<table>
    <thead>
        <tr>
            <th>Member ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Role</th>
            <th>Savings (UGX)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $member)
        <tr>
            <td>{{ $member->member_id }}</td>
            <td>{{ $member->full_name }}</td>
            <td>{{ $member->email }}</td>
            <td>{{ $member->contact }}</td>
            <td>{{ ucfirst($member->role) }}</td>
            <td>{{ number_format($member->savings) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
