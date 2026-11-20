@extends('layouts.dashboard')
@section('title', 'Guests & Stays')
@section('page-title', 'Guests & Stays')
@section('user-role', 'SUPERVISOR')
@section('sidebar-menu') @include('dashboards.supervisor._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Guests & Stays</h1>
            <p>Unified view of all guests and their current/past stays</p>
        </div>
        <a href="{{ route('supervisor.check_in.form') }}" class="btn-primary"><i class="fa-solid fa-user-plus"></i> Check-in New Guest</a>
    </div>

    {{-- FILTER TABS — use ?status= and preserve current search --}}
    <div class="filter-tabs">
        <a href="{{ request()->fullUrlWithQuery(['status' => 'all', 'page' => null]) }}"
           class="filter-tab {{ request('status', 'all') === 'all' ? 'active' : '' }}">
            All Guests ({{ $counts['all'] }})
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'active', 'page' => null]) }}"
           class="filter-tab {{ request('status') === 'active' ? 'active' : '' }}">
            Currently Staying ({{ $counts['active'] }})
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'upcoming', 'page' => null]) }}"
           class="filter-tab {{ request('status') === 'upcoming' ? 'active' : '' }}">
            Upcoming ({{ $counts['upcoming'] }})
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'checked_out', 'page' => null]) }}"
           class="filter-tab {{ request('status') === 'checked_out' ? 'active' : '' }}">
            Checked Out ({{ $counts['checked_out'] }})
        </a>
    </div>

    <div class="card">
        {{-- SEARCH FORM — preserve current status filter via hidden input --}}
        <form method="GET" action="{{ url()->current() }}" class="search-bar">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone, or room number...">
            <button type="submit" class="btn-secondary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            @if (request('search'))
                <a href="{{ url()->current() . '?status=' . request('status', 'all') }}" class="btn-secondary" style="text-decoration: none;">
                    <i class="fa-solid fa-xmark"></i> Clear
                </a>
            @endif
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
                        $activeStay   = $guest->stays->where('status', 'active')->first();
                        $upcomingStay = $guest->stays->where('status', 'upcoming')->first();
                        $latestStay   = $activeStay ?? $upcomingStay ?? $guest->stays->sortByDesc('check_out_date')->first();
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
                        <td>{{ $latestStay && $latestStay->check_in_date ? \Carbon\Carbon::parse($latestStay->check_in_date)->format('M d, Y') : '—' }}</td>
                        <td>{{ $latestStay && $latestStay->check_out_date ? \Carbon\Carbon::parse($latestStay->check_out_date)->format('M d, Y') : '—' }}</td>
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
                            <div style="display: flex; gap: 6px;">
                                <a href="{{ route('supervisor.guest_detail', $guest) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 0.75rem; text-decoration: none;"><i class="fa-solid fa-eye"></i> View</a>
                                @if ($activeStay)
                                    <form method="POST" action="{{ route('supervisor.check_out', $activeStay) }}" style="display: inline;">
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
                            No guests found. <a href="{{ route('supervisor.check_in.form') }}" style="color: #E84E1B;">Check in a new guest →</a>
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
        .filter-tab { background: white; border: 1px solid #eee; padding: 10px 20px; border-radius: 50px; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 0.8rem; color: #58595B; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .filter-tab:hover { border-color: #E84E1B; color: #E84E1B; }
        .filter-tab.active { background: #E84E1B; border-color: #E84E1B; color: white; }
    </style>
@endsection