@extends('layouts.guest')
@section('title', 'My Stay')

@section('content')
    {{-- HERO: ROOM + DATES --}}
    <div class="card stay-hero">
        <div class="hero-left">
            <p class="hero-label">YOUR ROOM</p>
            <h1 class="hero-room">{{ $stay->room->room_number }}</h1>
            <p class="hero-floor">Floor {{ $stay->room->floor }} · {{ $stay->room->type }}</p>
        </div>
        <div class="hero-right">
            <div class="hero-date">
                <span>Check-in</span>
                <strong>{{ $stay->check_in_date->format('M d, Y') }}</strong>
                <small>{{ $stay->check_in_date->format('l') }}</small>
            </div>
            <div class="hero-arrow">→</div>
            <div class="hero-date">
                <span>Check-out</span>
                <strong>{{ $stay->check_out_date->format('M d, Y') }}</strong>
                <small>{{ $stay->check_out_date->format('l') }}</small>
            </div>
        </div>
    </div>

    {{-- STATS --}}
    <div class="stats-row">
        <div class="mini-stat">
            <div class="mini-stat-value">{{ $stay->nights }}</div>
            <div class="mini-stat-label">{{ $stay->nights == 1 ? 'Night' : 'Nights' }}</div>
        </div>
        <div class="mini-stat">
            <div class="mini-stat-value">{{ $daysRemaining }}</div>
            <div class="mini-stat-label">{{ $daysRemaining == 1 ? 'Day Left' : 'Days Left' }}</div>
        </div>
        <div class="mini-stat">
            <div class="mini-stat-value">{{ $stay->num_guests }}</div>
            <div class="mini-stat-label">{{ $stay->num_guests == 1 ? 'Guest' : 'Guests' }}</div>
        </div>
        <div class="mini-stat">
            <div class="mini-stat-value">{{ $completedRequests }}/{{ $totalRequests }}</div>
            <div class="mini-stat-label">Requests Done</div>
        </div>
    </div>

    {{-- GUEST DETAILS --}}
    <div class="card">
        <h3 class="card-title">Guest Details</h3>
        <div class="detail-row"><span>Name</span><strong>{{ $stay->guest->full_name }}</strong></div>
        <div class="detail-row"><span>Guest ID</span><strong>{{ $stay->guest->guest_code }}</strong></div>
        <div class="detail-row"><span>Email</span><strong>{{ $stay->guest->email ?? '—' }}</strong></div>
        <div class="detail-row"><span>Phone</span><strong>{{ $stay->guest->phone ?? '—' }}</strong></div>
    </div>

    {{-- SPECIAL REQUESTS --}}
    @if ($stay->special_requests)
        <div class="card">
            <h3 class="card-title">Special Requests</h3>
            <p style="font-size: 0.9rem; color: #58595B; line-height: 1.7;">
                {{ $stay->special_requests }}
            </p>
        </div>
    @endif

    {{-- ROOM AMENITIES --}}
    <div class="card">
        <h3 class="card-title">Room Amenities</h3>
        <div class="amenities-grid">
            <div class="amenity">📶 Free Wi-Fi</div>
            <div class="amenity">❄️ Air Conditioning</div>
            <div class="amenity">📺 Smart TV</div>
            <div class="amenity">🚿 Hot Shower</div>
            <div class="amenity">☕ Coffee & Tea</div>
            <div class="amenity">🧴 Toiletries</div>
            <div class="amenity">🧺 Laundry Service</div>
            <div class="amenity">🛎️ 24/7 Room Service</div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="card">
        <h3 class="card-title">Quick Actions</h3>
        <div class="actions-row">
            <a href="{{ route('guest.request.form') }}" class="btn-primary">🛎️ Request Service</a>
            <a href="{{ route('guest.my_requests') }}" class="btn-secondary">📋 My Requests</a>
            <a href="{{ route('guest.hotel_info') }}" class="btn-secondary">ℹ️ Hotel Info</a>
        </div>
    </div>

    <style>
        .stay-hero {
            background: linear-gradient(135deg, #E84E1B 0%, #ff6b3d 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 35px;
            flex-wrap: wrap;
            gap: 25px;
        }
        .hero-label { font-size: 0.75rem; opacity: 0.85; letter-spacing: 2px; font-weight: 600; }
        .hero-room { font-size: 3rem; font-weight: 700; line-height: 1; margin: 8px 0; }
        .hero-floor { font-size: 0.9rem; opacity: 0.9; }
        .hero-right { display: flex; align-items: center; gap: 20px; }
        .hero-date { text-align: center; }
        .hero-date span { display: block; font-size: 0.7rem; opacity: 0.85; letter-spacing: 1px; text-transform: uppercase; }
        .hero-date strong { display: block; font-size: 1.05rem; margin: 4px 0 2px; }
        .hero-date small { font-size: 0.7rem; opacity: 0.85; }
        .hero-arrow { font-size: 1.5rem; opacity: 0.7; }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        @media (max-width: 600px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
        .mini-stat {
            background: white;
            padding: 18px 10px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-top: 3px solid #E84E1B;
        }
        .mini-stat-value { font-size: 1.5rem; font-weight: 700; color: #58595B; }
        .mini-stat-label { font-size: 0.7rem; color: #999; letter-spacing: 0.5px; text-transform: uppercase; margin-top: 4px; }

        .detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f4f4f4; font-size: 0.9rem; }
        .detail-row:last-child { border-bottom: none; }
        .detail-row span { color: #888; }

        .amenities-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        @media (max-width: 600px) { .amenities-grid { grid-template-columns: 1fr; } }
        .amenity {
            background: #fafafa; padding: 14px 16px; border-radius: 8px;
            font-size: 0.85rem; color: #58595B; font-weight: 500;
        }

        .actions-row { display: flex; gap: 10px; flex-wrap: wrap; }
    </style>
@endsection