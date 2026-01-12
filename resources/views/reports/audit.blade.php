@extends('reports.pdf-template')

@section('content')
<div class="summary">
    <h3>Audit Summary</h3>
    <div class="stat">
        <div class="stat-label">Total Activities</div>
        <div class="stat-value">{{ count($data) }}</div>
    </div>
    <div class="stat">
        <div class="stat-label">Unique Users</div>
        <div class="stat-value">{{ $data->unique('user')->count() }}</div>
    </div>
</div>

<div class="info-box">
    <h3>Activities by Action</h3>
    <table>
        @php
            $actionGroups = $data->groupBy('action');
        @endphp
        @foreach($actionGroups as $action => $logs)
        <tr>
            <td><strong>{{ $action }}</strong></td>
            <td style="text-align: right;">{{ $logs->count() }} activities</td>
        </tr>
        @endforeach
    </table>
</div>

<div class="info-box">
    <h3>Activities by User</h3>
    <table>
        @php
            $userGroups = $data->groupBy('user');
        @endphp
        @foreach($userGroups as $user => $logs)
        <tr>
            <td><strong>{{ $user }}</strong></td>
            <td style="text-align: right;">{{ $logs->count() }} activities</td>
        </tr>
        @endforeach
    </table>
</div>

<h3 style="margin-top: 30px;">Detailed Activity Log</h3>
<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Action</th>
            <th>Details</th>
            <th>Timestamp</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $log)
        <tr>
            <td>{{ $log->user }}</td>
            <td><strong>{{ $log->action }}</strong></td>
            <td>{{ $log->details }}</td>
            <td>{{ date('Y-m-d H:i:s', strtotime($log->timestamp)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
