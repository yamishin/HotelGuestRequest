@extends('layouts.dashboard')
@section('title', 'Guests & Stays')
@section('page-title', 'Guests & Stays')
@section('user-role', 'FRONT DESK')
@section('sidebar-menu') @include('dashboards.frontdesk._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Guests & Stays</h1>
            <p>Unified view of all guests and their current/past stays</p>
        </div>
        <a href="{{ route('frontdesk.check_in.form') }}" class="btn-primary">➕ Check-in New Guest</a>
    </div>

    <div class="filter-tabs">
        <a href="{{ route('frontdesk.guests_stays') }}" class="filter-tab {{ !request('filter') || request('filter') === 'all' ? 'active' : '' }}">All Guests</a>
        <a href="{{ route('frontdesk.guests_stays', ['filter' => 'active']) }}" class="filter-tab {{ request('filter') === 'active' ? 'active' : '' }}">Currently Staying</a>
        <a href="{{ route('frontdesk.guests_stays', ['filter' => 'upcoming']) }}" class="filter-tab {{ request('filter') === 'upcoming' ? 'active' : '' }}">Upcoming</a>
        <a href="{{ route('frontdesk.guests_stays', ['filter' => 'completed']) }}" class="filter-tab {{ request('filter') === 'completed' ? 'active' : '' }}">Checked Out</a>
    </div>

    <div class="card">
        <form method="GET" class="search-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone, or guest code...">
            <button type="submit" class="btn-secondary">Search</button>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Guest</th>
                    <th>Contact</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Total Stays</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($guests as $guest)
                    @php
                        $activeStay = $guest->stays->where('status', 'active')->first();
                        $upcomingStay = $guest->stays->where('status', 'upcoming')->first();
                        $latestStay = $activeStay ?? $upcomingStay ?? $guest->stays->sortByDesc('check_out_date')->first();
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $guest->full_name }}</strong><br>
                            <span style="font-size: 0.75rem; color: #999;">ID: {{ $guest->guest_code }}</span>
                        </td>
                        <td>
                            {{ $guest->email ?? '—' }}<br>
                            <span style="font-size: 0.75rem; color: #999;">{{ $guest->phone ?? '—' }}</span>
                        </td>
                        <td>
                            @if ($latestStay)
                                <strong>{{ $latestStay->room->room_number ?? '—' }}</strong>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $latestStay?->check_in_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $latestStay?->check_out_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $guest->stays->count() }}</td>
                        <td>
                            @if ($activeStay)
                                <span class="badge badge-success">Active Stay</span>
                            @elseif ($upcomingStay)
                                <span class="badge badge-info">Upcoming</span>
                            @elseif ($guest->stays->count() > 0)
                                <span class="badge badge-grey">Checked Out</span>
                            @else
                                <span class="badge badge-grey">New Guest</span>
                            @endif
                        </td>
                                                <td>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <a href="{{ route('frontdesk.guest_detail', $guest) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 0.75rem; text-decoration: none;">View</a>
                                @if ($activeStay)
                                    <a href="{{ route('frontdesk.qr', $activeStay) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 0.75rem; text-decoration: none;">📱 QR</a>
                                    <form method="POST" action="{{ route('frontdesk.check_out', $activeStay) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 0.75rem;" onclick="return confirm('Check out {{ $guest->full_name }}?')">Check-out</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                            No guests found. <a href="{{ route('frontdesk.check_in.form') }}" style="color: #E84E1B;">Check in a new guest →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $guests->links() }}
        </div>
    </div>

    <style>
        .filter-tabs { display: flex; gap: 5px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-tab { background: white; border: 1px solid #eee; padding: 10px 20px; border-radius: 50px; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 0.8rem; color: #58595B; cursor: pointer; transition: all 0.3s; text-decoration: none; }
        .filter-tab:hover { border-color: #E84E1B; color: #E84E1B; }
        .filter-tab.active { background: #E84E1B; border-color: #E84E1B; color: white; }
    </style>
@endsection