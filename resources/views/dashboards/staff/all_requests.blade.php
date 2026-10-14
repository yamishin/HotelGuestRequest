@extends('layouts.dashboard')
@section('title', 'Department Requests')
@section('page-title', 'Department Requests')
@section('user-role', 'STAFF')
@section('sidebar-menu') @include('dashboards.staff._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Department Requests</h1>
            <p>All open requests assigned to the {{ auth()->user()->department }} department</p>
        </div>
    </div>

    <div class="card">
        <form method="GET" class="filters-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, code, or room...">
            <select name="status">
                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Open</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('staff.all_requests') }}" class="btn-secondary">Reset</a>
        </form>
    </div>

    <div class="card">
        @if ($requests->count())
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Room</th>
                            <th>Request</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Age</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $req)
                            <tr>
                                <td><span class="req-code">{{ $req->request_code }}</span></td>
                                <td><strong>{{ $req->stay->room->room_number ?? '—' }}</strong></td>
                                <td>
                                    <div style="font-weight: 600; color: #58595B; font-size: 0.85rem;">{{ $req->title }}</div>
                                    <div style="font-size: 0.75rem; color: #999; margin-top: 2px;">
                                        {{ $req->stay->guest->full_name ?? 'Unknown' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $req->status_color }}">
                                        {{ $req->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if ($req->assignee)
                                        <div class="assignee-cell">
                                            <div class="assignee-avatar">{{ $req->assignee->initials }}</div>
                                            <span>{{ $req->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span style="color: #999; font-size: 0.85rem;">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size: 0.75rem; color: #999;">
                                        {{ $req->created_at->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 20px;">{{ $requests->links() }}</div>
        @else
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 3rem; margin-bottom: 10px;">📭</div>
                <p style="color: #999;">No open requests in your department.</p>
            </div>
        @endif
    </div>

    <style>
        .filters-bar {
            display: grid;
            grid-template-columns: 2fr 1fr auto auto;
            gap: 10px;
            align-items: center;
        }
        @media (max-width: 700px) { .filters-bar { grid-template-columns: 1fr 1fr; } }
        .filters-bar input,
        .filters-bar select {
            padding: 10px 14px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.85rem;
            outline: none;
            background: white;
        }
        .filters-bar input:focus,
        .filters-bar select:focus { border-color: #E84E1B; }

        .table-wrapper { overflow-x: auto; }
        .req-code {
            font-size: 0.75rem;
            font-weight: 600;
            color: #E84E1B;
            background: #fff5f2;
            padding: 3px 8px;
            border-radius: 5px;
            white-space: nowrap;
        }
        .assignee-cell { display: flex; align-items: center; gap: 8px; }
        .assignee-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #E84E1B, #ff6b3d);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .assignee-cell span { font-size: 0.85rem; color: #58595B; }
    </style>
@endsection