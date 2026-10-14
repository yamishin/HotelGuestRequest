@extends('layouts.dashboard')
@section('title', 'History')
@section('page-title', 'Request History')
@section('user-role', 'SUPERVISOR')
@section('sidebar-menu') @include('dashboards.supervisor._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Request History</h1>
            <p>All completed and cancelled requests</p>
        </div>
    </div>

    <div class="card">
        <form method="GET" class="search-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by request title or code...">
            <button type="submit" class="btn-secondary">Search</button>
        </form>

        @if ($history->count())
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Room</th>
                            <th>Request</th>
                            <th>Department</th>
                            <th>Handled By</th>
                            <th>Final Status</th>
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $req)
                            <tr>
                                <td><span class="req-code">{{ $req->request_code }}</span></td>
                                <td><strong>{{ $req->stay->room->room_number ?? '—' }}</strong></td>
                                <td>
                                    <div class="req-title">{{ $req->title }}</div>
                                    @if ($req->description)
                                        <div class="req-desc">{{ Str::limit($req->description, 60) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="dept-badge dept-{{ Str::slug($req->department) }}">
                                        {{ $req->department }}
                                    </span>
                                </td>
                                <td>
                                    @if ($req->assignee)
                                        <div class="assignee-cell">
                                            <div class="assignee-avatar">{{ $req->assignee->initials }}</div>
                                            <span>{{ $req->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span style="color: #999; font-size: 0.85rem;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $req->status_color }}">
                                        {{ $req->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 0.8rem; color: #888;">
                                        {{ $req->updated_at->format('M d, Y · g:i A') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">{{ $history->links() }}</div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📜</div>
                <h3>No history yet</h3>
                <p>Completed and cancelled requests will appear here.</p>
            </div>
        @endif
    </div>

    <style>
        .table-wrapper { overflow-x: auto; }
        .req-code {
            font-size: 0.75rem; font-weight: 600; color: #E84E1B;
            background: #fff5f2; padding: 3px 8px; border-radius: 5px; white-space: nowrap;
        }
        .req-title { font-weight: 600; color: #58595B; font-size: 0.85rem; }
        .req-desc { font-size: 0.75rem; color: #999; margin-top: 3px; }
        .assignee-cell { display: flex; align-items: center; gap: 8px; }
        .assignee-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg, #E84E1B, #ff6b3d);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; flex-shrink: 0;
        }
        .assignee-cell span { font-size: 0.85rem; color: #58595B; }
        .dept-badge {
            display: inline-block; padding: 4px 10px; border-radius: 50px;
            font-size: 0.7rem; font-weight: 600; white-space: nowrap;
        }
        .dept-housekeeping { background: #e8f5e9; color: #2e7d32; }
        .dept-maintenance { background: #fff3e0; color: #e65100; }
        .dept-it          { background: #e3f2fd; color: #1565c0; }
        .dept-front-desk  { background: #f3e5f5; color: #6a1b9a; }
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-icon { font-size: 3rem; margin-bottom: 15px; }
        .empty-state h3 { color: #58595B; margin-bottom: 6px; }
        .empty-state p { color: #999; font-size: 0.9rem; }
    </style>
@endsection