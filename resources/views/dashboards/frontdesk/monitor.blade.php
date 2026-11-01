@extends('layouts.dashboard')
@section('title', 'Monitor Requests')
@section('page-title', 'Monitor Requests')
@section('user-role', 'FRONT DESK')
@section('sidebar-menu') @include('dashboards.frontdesk._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Monitor Requests</h1>
            <p>Track all requests currently being handled by Supervisors and Staff</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon orange">📋</div><div class="stat-info"><h3>{{ $counts['routed'] }}</h3><p>Routed (In Queue)</p></div></div>
        <div class="stat-card"><div class="stat-icon grey">👷</div><div class="stat-info"><h3>{{ $counts['assigned'] }}</h3><p>Assigned to Staff</p></div></div>
        <div class="stat-card"><div class="stat-icon orange">🔧</div><div class="stat-info"><h3>{{ $counts['in_progress'] }}</h3><p>In Progress</p></div></div>
        <div class="stat-card"><div class="stat-icon grey">✅</div><div class="stat-info"><h3>{{ $counts['completed'] }}</h3><p>Awaiting Review</p></div></div>
    </div>

    <div class="card">
        <form method="GET" class="search-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search room or request...">
            <select name="status" style="padding: 0 15px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat';">
                <option value="all">All Statuses</option>
                <option value="routed" {{ request('status') == 'routed' ? 'selected' : '' }}>Routed</option>
                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Awaiting Review</option>
            </select>
            <button type="submit" class="btn-secondary">Filter</button>
        </form>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Room</th>
                        <th>Request</th>
                        <th>Dept</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $req)
                        <tr>
                            <td><span class="req-code">{{ $req->request_code }}</span></td>
                            <td><strong>{{ $req->stay->room->room_number }}</strong></td>
                            <td><strong>{{ $req->title }}</strong><br><span style="font-size:0.75rem; color:#999;">{{ $req->stay->guest->full_name }}</span></td>
                            <td>{{ $req->department }}</td>
                            <td>
                                @if($req->assignee)
                                    <span style="color:#E84E1B; font-weight:600;">{{ $req->assignee->name }}</span>
                                @else
                                    <span style="color:#999;">—</span>
                                @endif
                            </td>
                            <td><span class="badge badge-{{ $req->status_color }}">{{ $req->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align: center; padding: 40px; color: #999;">No requests being monitored.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">{{ $requests->links() }}</div>
    </div>
    
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid #E84E1B; }
        .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .stat-icon.orange { background: #fff5f2; }
        .stat-icon.grey { background: #f4f4f4; }
        .stat-info h3 { font-size: 1.5rem; color: #58595B; }
        .stat-info p { font-size: 0.85rem; color: #888; }
        .req-code { font-size: 0.75rem; font-weight: 700; color: #E84E1B; background: #fff5f2; padding: 3px 8px; border-radius: 5px; }
    </style>
@endsection