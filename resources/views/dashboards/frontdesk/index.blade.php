@extends('layouts.dashboard')
@section('title', 'Front Desk Dashboard')
@section('page-title', 'Front Desk Operations')
@section('user-role', 'FRONT DESK')
@section('sidebar-menu') @include('dashboards.frontdesk._sidebar') @endsection

@section('content')

    <div class="welcome-banner">
        <div>
            <h2>Good day, Front Desk! 👋</h2>
            <p>Here's an overview of today's hotel operations.</p>
        </div>
        <div class="date-display">
            <span id="current-date"></span>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon orange">👤</div>
            <div class="stat-info">
                <h3>{{ $checkInsToday }}</h3>
                <p>Check-ins Today</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon grey">🚪</div>
            <div class="stat-info">
                <h3>{{ $checkOutsToday }}</h3>
                <p>Check-outs Today</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">🛎️</div>
            <div class="stat-info">
                <h3>{{ $pendingRequests }}</h3>
                <p>Pending Requests</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon grey">🏠</div>
            <div class="stat-info">
                <h3>{{ $activeStays }} / {{ $totalRooms }}</h3>
                <p>Active Stays</p>
            </div>
        </div>
    </div>

    <div class="main-grid">
        <div class="left-column">
            <div class="card">
                <h3 class="card-title">Quick Actions</h3>
                <div class="quick-actions">
                    <a href="{{ route('frontdesk.check_in.form') }}" class="action-btn">➕ Check-in Guest</a>
                    <a href="{{ route('frontdesk.notifications') }}" class="action-btn">🔔 View Notifications</a>
                    <a href="{{ route('frontdesk.rooms') }}" class="action-btn">🏠 View All Rooms</a>
                    <a href="{{ route('frontdesk.guests_stays') }}" class="action-btn">🔍 Guest Lookup</a>
                </div>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h3 class="card-title">
                    Room Status Overview
                    <a href="{{ route('frontdesk.rooms') }}" style="font-size: 0.75rem; color: #E84E1B; text-decoration: none;">View all →</a>
                </h3>
                <div class="room-legend">
                    <span><span class="dot available"></span> Available ({{ $roomCounts['available'] }})</span>
                    <span><span class="dot occupied"></span> Occupied ({{ $roomCounts['occupied'] }})</span>
                    <span><span class="dot cleaning"></span> Cleaning ({{ $roomCounts['cleaning'] }})</span>
                    <span><span class="dot maintenance"></span> Maintenance ({{ $roomCounts['maintenance'] }})</span>
                </div>
                <p style="font-size: 0.85rem; color: #888; margin-bottom: 10px;">
                    Showing Floor 5 ({{ $sampleRooms->count() }} rooms)
                </p>
                <div class="room-grid">
                    @foreach ($sampleRooms as $room)
                        <div class="room {{ $room->status }}" title="Room {{ $room->room_number }} — {{ $room->type }}">
                            {{ $room->room_number }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="right-column">
            <div class="card">
                <h3 class="card-title">Recent Activity</h3>
                <ul class="activity-feed">
                    @forelse ($recentActivity as $notif)
                        <li>
                            <div class="activity-icon {{ $notif->is_read ? 'grey' : 'orange' }}">
                                {{ match($notif->type) {
                                    'request'  => '🛎️',
                                    'check_in' => '👤',
                                    'check_out'=> '🚪',
                                    'review'   => '⭐',
                                    'payment'  => '💰',
                                    default    => '🔔',
                                } }}
                            </div>
                            <div class="activity-text">
                                <p>{{ $notif->message }}</p>
                                <span>{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                        </li>
                    @empty
                        <li style="color: #999;">No activity yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <style>
        .welcome-banner { background: linear-gradient(135deg, #E84E1B 0%, #ff6b3d 100%); color: white; padding: 25px 30px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .welcome-banner h2 { font-size: 1.5rem; margin-bottom: 5px; }
        .welcome-banner p { font-size: 0.9rem; opacity: 0.9; }
        .date-display { background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 50px; font-weight: 600; font-size: 0.9rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid #E84E1B; }
        .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .stat-icon.orange { background: #fff5f2; } .stat-icon.grey { background: #f4f4f4; }
        .stat-info h3 { font-size: 1.5rem; color: #58595B; }
        .stat-info p { font-size: 0.85rem; color: #888; }
        .main-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        @media (max-width: 900px) { .main-grid { grid-template-columns: 1fr; } }
        .quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .action-btn { background: #fafafa; border: 1px solid #eee; padding: 15px; border-radius: 8px; cursor: pointer; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 0.85rem; color: #58595B; display: flex; align-items: center; gap: 10px; transition: all 0.3s; text-decoration: none; }
        .action-btn:hover { background: #fff5f2; border-color: #E84E1B; color: #E84E1B; transform: translateY(-2px); }
        .room-legend { display: flex; gap: 15px; font-size: 0.75rem; color: #666; margin-bottom: 15px; flex-wrap: wrap; }
        .dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 5px; }
        .dot.available { background: #4CAF50; } .dot.occupied { background: #E84E1B; } .dot.cleaning { background: #FFC107; } .dot.maintenance { background: #58595B; }
        .room-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(50px, 1fr)); gap: 6px; }
        .room { padding: 10px 5px; text-align: center; border-radius: 6px; font-weight: 600; font-size: 0.75rem; color: white; }
        .room.available { background: #4CAF50; } .room.occupied { background: #E84E1B; }
        .room.cleaning { background: #FFC107; color: #58595B; } .room.maintenance { background: #58595B; }
        .activity-feed { list-style: none; }
        .activity-feed li { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f4f4f4; }
        .activity-feed li:last-child { border-bottom: none; }
        .activity-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .activity-icon.orange { background: #fff5f2; } .activity-icon.grey { background: #f4f4f4; }
        .activity-text p { font-size: 0.85rem; color: #58595B; }
        .activity-text span { font-size: 0.75rem; color: #999; }
    </style>

    <script>
        const dateEl = document.getElementById('current-date');
        const today = new Date();
        dateEl.textContent = today.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    </script>

@endsection