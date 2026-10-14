@extends('layouts.dashboard')
@section('title', 'Task History')
@section('page-title', 'Task History')
@section('user-role', 'STAFF')
@section('sidebar-menu') @include('dashboards.staff._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Task History</h1>
            <p>All tasks you've completed</p>
        </div>
    </div>

    <div class="card">
        <form method="GET" class="search-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, code, or room...">
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
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $req)
                            <tr>
                                <td><span class="req-code">{{ $req->request_code }}</span></td>
                                <td><strong>{{ $req->stay->room->room_number ?? '—' }}</strong></td>
                                <td>
                                    <div style="font-weight: 600; color: #58595B; font-size: 0.85rem;">{{ $req->title }}</div>
                                    <div style="font-size: 0.75rem; color: #999; margin-top: 2px;">
                                        {{ $req->stay->guest->full_name ?? 'Unknown' }}
                                    </div>
                                </td>
                                <td>{{ $req->department }}</td>
                                <td>
                                    <span style="font-size: 0.8rem; color: #888;">
                                        {{ $req->completed_at?->format('M d, Y · g:i A') ?? '—' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 20px;">{{ $history->links() }}</div>
        @else
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 3rem; margin-bottom: 10px;">📜</div>
                <p style="color: #999;">No completed tasks yet.</p>
            </div>
        @endif
    </div>

    <style>
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
    </style>
@endsection