@extends('layouts.guest')
@section('title', 'Guest Portal')

@section('content')
    <div class="card" style="background: linear-gradient(135deg, #E84E1B, #ff6b3d); color: white; border: none;">
        <h1 style="font-size: 1.6rem; margin-bottom: 8px;">Welcome, {{ $stay->guest->first_name }}!</h1>
        <p style="opacity: 0.95; font-size: 0.9rem;">
            Room {{ $stay->room->room_number }} · {{ $stay->check_in_date->format('M d') }} → {{ $stay->check_out_date->format('M d, Y') }}
        </p>
        <p style="opacity: 0.85; font-size: 0.85rem; margin-top: 8px;">
            {{ $stay->nights }} {{ $stay->nights == 1 ? 'night' : 'nights' }} stay
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
        <a href="{{ route('guest.request.form') }}" style="text-decoration: none;">
            <div class="card" style="text-align: center; cursor: pointer; transition: transform 0.2s;">
                <div style="font-size: 2rem;">🛎️</div>
                <h3 style="color: #58595B; font-size: 0.95rem; margin-top: 10px;">Request Service</h3>
                <p style="font-size: 0.75rem; color: #999; margin-top: 5px;">Towels, room service, etc.</p>
            </div>
        </a>
        <a href="{{ route('guest.my_requests') }}" style="text-decoration: none;">
            <div class="card" style="text-align: center; cursor: pointer; transition: transform 0.2s;">
                <div style="font-size: 2rem;">📋</div>
                <h3 style="color: #58595B; font-size: 0.95rem; margin-top: 10px;">My Requests</h3>
                <p style="font-size: 0.75rem; color: #999; margin-top: 5px;">{{ $stay->serviceRequests->count() }} total</p>
            </div>
        </a>
        <a href="{{ route('guest.hotel_info') }}" style="text-decoration: none;">
            <div class="card" style="text-align: center; cursor: pointer; transition: transform 0.2s;">
                <div style="font-size: 2rem;">🏨</div>
                <h3 style="color: #58595B; font-size: 0.95rem; margin-top: 10px;">Hotel Info</h3>
                <p style="font-size: 0.75rem; color: #999; margin-top: 5px;">Amenities & hours</p>
            </div>
        </a>
        <a href="{{ route('guest.review.form') }}" style="text-decoration: none;">
            <div class="card" style="text-align: center; cursor: pointer; transition: transform 0.2s;">
                <div style="font-size: 2rem;">⭐</div>
                <h3 style="color: #58595B; font-size: 0.95rem; margin-top: 10px;">Leave Review</h3>
                <p style="font-size: 0.75rem; color: #999; margin-top: 5px;">Share your experience</p>
            </div>
        </a>
    </div>

    <div class="card">
        <h3 class="card-title">Recent Requests</h3>
        @forelse ($recentRequests as $req)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f4f4f4;">
                <div>
                    <p style="font-size: 0.9rem; color: #58595B; font-weight: 600;">{{ $req->title }}</p>
                    <span style="font-size: 0.75rem; color: #999;">{{ $req->department }} · {{ $req->created_at->diffForHumans() }}</span>
                </div>
                <span class="badge badge-{{ 
                    $req->status === 'completed' ? 'success' : 
                    ($req->status === 'pending' ? 'danger' : 'warning') 
                }}">
                    {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                </span>
            </div>
        @empty
            <p style="color: #999; font-size: 0.9rem;">You haven't made any requests yet.</p>
        @endforelse
    </div>

    <div class="card">
        <h3 class="card-title">Notifications</h3>
        @forelse ($notifications as $notif)
            <div style="display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f4f4f4;">
                <div style="font-size: 1.3rem;">🔔</div>
                <div>
                    <p style="font-size: 0.85rem; color: #58595B;">{{ $notif->message }}</p>
                    <span style="font-size: 0.7rem; color: #999;">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <p style="color: #999; font-size: 0.9rem;">No notifications.</p>
        @endforelse
    </div>
@endsection