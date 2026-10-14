@extends('layouts.dashboard')
@section('title', 'History')
@section('page-title', 'Activity History')
@section('user-role', 'FRONT DESK')
@section('sidebar-menu') @include('dashboards.frontdesk._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>History</h1>
            <p>Past check-ins, check-outs, and completed stays</p>
        </div>
    </div>

    <div class="card">
        <form method="GET" class="search-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by guest or room number...">
            <button type="submit" class="btn-secondary">Search</button>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Check-out Date</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Nights</th>
                    <th>Checked In</th>
                    <th>Checked Out</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($history as $stay)
                    <tr>
                        <td>{{ $stay->actual_check_out?->format('M d, Y') ?? $stay->check_out_date->format('M d, Y') }}</td>
                        <td><strong>{{ $stay->guest->full_name }}</strong></td>
                        <td>{{ $stay->room->room_number }}</td>
                        <td>{{ $stay->nights }}</td>
                        <td>{{ $stay->actual_check_in?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $stay->actual_check_out?->format('M d, Y') ?? '—' }}</td>
                        <td><span class="badge badge-success">Completed</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align: center; padding: 40px; color: #999;">No history yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">{{ $history->links() }}</div>
    </div>
@endsection