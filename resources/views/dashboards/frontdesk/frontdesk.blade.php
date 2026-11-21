@extends('layouts.dashboard')

@section('title', 'Front Desk Dashboard')
@section('page-title', 'Front Desk Operations')
@section('user-role', 'FRONT DESK')

@section('sidebar-menu')
<li><a href="/dashboard/frontdesk" class="active">Dashboard</a></li>
<li><a href="#">Guests</a></li>
<li><a href="#">Stays</a></li>
<li><a href="#">Rooms</a></li>
<li><a href="#">Requests</a></li>
<li><a href="#">Notifications</a></li>
<li><a href="#">History</a></li>
<li><a href="#">Reports</a></li>
<li><a href="#">Profile</a></li>
@endsection

@section('content')

<!-- WELCOME BANNER -->
<div class="welcome-banner">
    <div>
        <h2>Good day, Front Desk! <i class="fa-solid fa-hand-peace" style="font-size: 1.2rem;"></i></h2>
        <p>Here's an overview of today's hotel operations.</p>
    </div>
    <div class="date-display">
        <span id="current-date"></span>
    </div>
</div>

<!-- STATISTIC CARDS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fa-solid fa-user-plus" style="font-size: 22px;"></i>
        </div>
        <div class="stat-info">
            <h3>12</h3>
            <p>Check-ins Today</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon grey">
            <i class="fa-solid fa-right-from-bracket" style="font-size: 22px;"></i>
        </div>
        <div class="stat-info">
            <h3>8</h3>
            <p>Check-outs Today</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fa-solid fa-clock" style="font-size: 22px;"></i>
        </div>
        <div class="stat-info">
            <h3>5</h3>
            <p>Pending Requests</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon grey">
            <i class="fa-solid fa-bed" style="font-size: 22px;"></i>
        </div>
        <div class="stat-info">
            <h3>45 / 60</h3>
            <p>Active Stays</p>
        </div>
    </div>
</div>

<!-- MAIN GRID: QUICK ACTIONS + ACTIVITY FEED -->
<div class="main-grid">

    <!-- LEFT COLUMN -->
    <div class="left-column">

        <!-- QUICK ACTIONS -->
        <div class="card">
            <h3 class="card-title">Quick Actions</h3>
            <div class="quick-actions">
                <button class="action-btn">
                    <i class="fa-solid fa-user-plus" style="font-size: 18px;"></i>
                    Check-in Guest
                </button>
                <button class="action-btn">
                    <i class="fa-solid fa-file-lines" style="font-size: 18px;"></i>
                    New Request
                </button>
                <button class="action-btn">
                    <i class="fa-solid fa-door-open" style="font-size: 18px;"></i>
                    View Rooms
                </button>
                <button class="action-btn">
                    <i class="fa-solid fa-magnifying-glass" style="font-size: 18px;"></i>
                    Guest Lookup
                </button>
            </div>
        </div>

        <!-- ROOM STATUS OVERVIEW -->
        <div class="card" style="margin-top: 20px;">
            <h3 class="card-title">Room Status Overview</h3>
            <div class="room-legend">
                <span><span class="dot available"></span> Available (15)</span>
                <span><span class="dot occupied"></span> Occupied (45)</span>
                <span><span class="dot cleaning"></span> Cleaning (5)</span>
            </div>
            <div class="room-grid">
                <!-- These will eventually be generated dynamically from the database -->
                <div class="room available">501</div>
                <div class="room occupied">502</div>
                <div class="room occupied">503</div>
                <div class="room cleaning">504</div>
                <div class="room available">105</div>
                <div class="room occupied">201</div>
                <div class="room occupied">202</div>
                <div class="room available">203</div>
                <div class="room occupied">204</div>
                <div class="room occupied">205</div>
            </div>
        </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="right-column">

        <!-- RECENT ACTIVITY -->
        <div class="card">
            <h3 class="card-title">Recent Activity</h3>
            <ul class="activity-feed">
                <li>
                    <div class="activity-icon orange">
                        <i class="fa-solid fa-bell-concierge"></i>
                    </div>
                    <div class="activity-text">
                        <p><strong>Room 204</strong> requested extra towels</p>
                        <span>2 minutes ago</span>
                    </div>
                </li>
                <li>
                    <div class="activity-icon grey">
                        <i class="fa-solid fa-square-check"></i>
                    </div>
                    <div class="activity-text">
                        <p><strong>Room 103</strong> request completed</p>
                        <span>10 minutes ago</span>
                    </div>
                </li>
                <li>
                    <div class="activity-icon orange">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div class="activity-text">
                        <p><strong>John Smith</strong> checked in to Room 205</p>
                        <span>25 minutes ago</span>
                    </div>
                </li>
                <li>
                    <div class="activity-icon orange">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <div class="activity-text">
                        <p><strong>Room 102</strong> ordered room service</p>
                        <span>45 minutes ago</span>
                    </div>
                </li>
                <li>
                    <div class="activity-icon orange">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div class="activity-text">
                        <p><strong>Room 201</strong> left a 5-star review</p>
                        <span>1 hour ago</span>
                    </div>
                </li>
            </ul>
        </div>

    </div>

</div>

<!-- PAGE-LEVEL STYLES FOR DASHBOARD -->
<style>
    .welcome-banner {
        background: linear-gradient(135deg, #E84E1B 0%, #ff6b3d 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .welcome-banner h2 {
        font-size: 1.5rem;
        margin-bottom: 5px;
    }

    .welcome-banner p {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .date-display {
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* STATS GRID */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border-left: 4px solid #E84E1B;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon.orange {
        background: #fff5f2;
        color: #E84E1B;
    }

    .stat-icon.grey {
        background: #f4f4f4;
        color: #58595B;
    }

    .stat-info h3 {
        font-size: 1.5rem;
        color: #58595B;
    }

    .stat-info p {
        font-size: 0.85rem;
        color: #888;
    }

    /* MAIN GRID */
    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    @media (max-width: 900px) {
        .main-grid {
            grid-template-columns: 1fr;
        }
    }

    .card-title {
        font-size: 1.1rem;
        color: #58595B;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f4f4f4;
    }

    /* QUICK ACTIONS */
    .quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .action-btn {
        background: #fafafa;
        border: 1px solid #eee;
        padding: 15px;
        border-radius: 8px;
        cursor: pointer;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 0.85rem;
        color: #58595B;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }

    .action-btn:hover {
        background: #fff5f2;
        border-color: #E84E1B;
        color: #E84E1B;
        transform: translateY(-2px);
    }

    /* ROOM GRID */
    .room-legend {
        display: flex;
        gap: 20px;
        font-size: 0.8rem;
        color: #666;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 5px;
    }

    .dot.available {
        background: #4CAF50;
    }

    .dot.occupied {
        background: #E84E1B;
    }

    .dot.cleaning {
        background: #FFC107;
    }

    .room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
        gap: 8px;
    }

    .room {
        padding: 12px 5px;
        text-align: center;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        color: white;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .room:hover {
        transform: scale(1.05);
    }

    .room.available {
        background: #4CAF50;
    }

    .room.occupied {
        background: #E84E1B;
    }

    .room.cleaning {
        background: #FFC107;
        color: #58595B;
    }

    /* ACTIVITY FEED */
    .activity-feed {
        list-style: none;
    }

    .activity-feed li {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f4f4f4;
    }

    .activity-feed li:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .activity-icon.orange {
        background: #fff5f2;
        color: #E84E1B;
    }

    .activity-icon.grey {
        background: #f4f4f4;
        color: #58595B;
    }

    .activity-text p {
        font-size: 0.85rem;
        color: #58595B;
    }

    .activity-text span {
        font-size: 0.75rem;
        color: #999;
    }
</style>

<script>
    // Display current date in the banner
    const dateEl = document.getElementById('current-date');
    const today = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    dateEl.textContent = today.toLocaleDateString('en-US', options);
</script>

@endsection