@extends('reports.pdf-template')

@section('content')
<div class="summary">
    <h3>Project Portfolio Summary</h3>
    <div class="stat">
        <div class="stat-label">Total Projects</div>
        <div class="stat-value">{{ count($data) }}</div>
    </div>
    <div class="stat">
        <div class="stat-label">Total Budget</div>
        <div class="stat-value">UGX {{ number_format($data->sum('budget')) }}</div>
    </div>
    <div class="stat">
        <div class="stat-label">Avg Progress</div>
        <div class="stat-value">{{ number_format($data->avg('progress'), 1) }}%</div>
    </div>
</div>

<div class="info-box">
    <h3>Project Status</h3>
    <table>
        @php
            $active = $data->where('progress', '<', 100);
            $completed = $data->where('progress', '=', 100);
        @endphp
        <tr>
            <td><strong>Active Projects</strong></td>
            <td style="text-align: right;">{{ $active->count() }} projects</td>
            <td style="text-align: right;">UGX {{ number_format($active->sum('budget')) }}</td>
        </tr>
        <tr>
            <td><strong>Completed Projects</strong></td>
            <td style="text-align: right;">{{ $completed->count() }} projects</td>
            <td style="text-align: right;">UGX {{ number_format($completed->sum('budget')) }}</td>
        </tr>
    </table>
</div>

<h3 style="margin-top: 30px;">Detailed Project List</h3>
<table>
    <thead>
        <tr>
            <th>Project ID</th>
            <th>Name</th>
            <th>Budget (UGX)</th>
            <th>Progress</th>
            <th>ROI</th>
            <th>Risk Score</th>
            <th>Timeline</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $project)
        <tr>
            <td>{{ $project->project_id }}</td>
            <td>{{ $project->name }}</td>
            <td>{{ number_format($project->budget) }}</td>
            <td><strong>{{ $project->progress }}%</strong></td>
            <td>{{ $project->roi ?? 'N/A' }}%</td>
            <td>{{ $project->risk_score ?? 'N/A' }}</td>
            <td>{{ $project->timeline ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
