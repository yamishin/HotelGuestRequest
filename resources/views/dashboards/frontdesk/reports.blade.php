@extends('layouts.dashboard')
@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')
@section('user-role', 'FRONT DESK')
@section('sidebar-menu') @include('dashboards.frontdesk._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Reports</h1>
            <p>Hotel performance and operational insights</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon orange">📊</div>
            <div class="stat-info">
                <h3>{{ $occupancyRate }}%</h3>
                <p>Current Occupancy</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon grey">🛏️</div>
            <div class="stat-info">
                <h3>{{ $totalStays }}</h3>
                <p>Total Stays</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">🛎️</div>
            <div class="stat-info">
                <h3>{{ $totalRequests }}</h3>
                <p>Total Requests</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon grey">⭐</div>
            <div class="stat-info">
                <h3>{{ $avgRating }} / 5</h3>
                <p>Avg. Guest Rating</p>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Top Requested Services</h3>
        <table class="data-table">
            <thead><tr><th>Service</th><th>Count</th></tr></thead>
            <tbody>
                @forelse ($topServices as $svc)
                    <tr><td>{{ $svc->title }}</td><td>{{ $svc->count }}</td></tr>
                @empty
                    <tr><td colspan="2" style="text-align: center; color: #999;">No service data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3 class="card-title">Occupancy by Floor</h3>
        <table class="data-table">
            <thead><tr><th>Floor</th><th>Occupied</th><th>Occupancy Rate</th></tr></thead>
            <tbody>
                @foreach ($occupancyByFloor as $row)
                    <tr>
                        <td>Floor {{ $row->floor }}</td>
                        <td>{{ $row->occupied }} / {{ $row->total }}</td>
                        <td>{{ $row->total > 0 ? round(($row->occupied / $row->total) * 100) : 0 }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid #E84E1B; }
        .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .stat-icon.orange { background: #fff5f2; } .stat-icon.grey { background: #f4f4f4; }
        .stat-info h3 { font-size: 1.5rem; color: #58595B; }
        .stat-info p { font-size: 0.85rem; color: #888; }
    </style>
@endsection