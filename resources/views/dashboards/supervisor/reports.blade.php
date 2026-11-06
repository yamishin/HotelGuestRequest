@extends('layouts.dashboard')
@section('title', 'Department Reports')
@section('page-title', 'Reports: ' . auth()->user()->department)
@section('user-role', 'SUPERVISOR')
@section('sidebar-menu') @include('dashboards.supervisor._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ auth()->user()->department }} Analytics</h1>
            <p>Performance insights for your department</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fa-solid fa-ticket"></i></div>
            <div class="stat-info">
                <h3>{{ $totalRequests }}</h3>
                <p>Total Requests Routed</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon grey"><i class="fa-solid fa-check-double"></i></div>
            <div class="stat-info">
                <h3>{{ $resolvedRequests }}</h3>
                <p>Successfully Resolved</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fa-solid fa-stopwatch"></i></div>
            <div class="stat-info">
                <h3>{{ $avgResolutionTime ? round($avgResolutionTime) . ' mins' : '—' }}</h3>
                <p>Avg. Resolution Time</p>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Top 5 Most Requested Services</h3>
        <table class="data-table">
            <thead>
                <tr><th>Service Type</th><th>Total Requests</th></tr>
            </thead>
            <tbody>
                @forelse($topServices as $svc)
                    <tr>
                        <td style="font-weight: 600; color: #58595B;">{{ $svc->title }}</td>
                        <td>{{ $svc->count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" style="text-align: center; color: #999;">No data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid #E84E1B; }
        .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .stat-icon.orange { background: #fff5f2; color: #E84E1B; }
        .stat-icon.grey { background: #f4f4f4; color: #58595B; }
        .stat-info h3 { font-size: 1.5rem; color: #58595B; }
        .stat-info p { font-size: 0.85rem; color: #888; }
    </style>
@endsection