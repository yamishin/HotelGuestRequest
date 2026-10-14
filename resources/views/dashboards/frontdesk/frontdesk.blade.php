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
            <h2>Good day, Front Desk! 👋</h2>
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
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div class="stat-info">
                <h3>12</h3>
                <p>Check-ins Today</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon grey">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            </div>
            <div class="stat-info">
                <h3>8</h3>
                <p>Check-outs Today</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div class="stat-info">
                <h3>5</h3>
                <p>Pending Requests</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon grey">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
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
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Check-in Guest
                    </button>
                    <button class="action-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        New Request
                    </button>
                    <button class="action-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                        View Rooms
                    </button>
                    <button class="action-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
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
                        <div class="activity-icon orange">🛎️</div>
                        <div class="activity-text">
                            <p><strong>Room 204</strong> requested extra towels</p>
                            <span>2 minutes ago</span>
                        </div>
                    </li>
                    <li>
                        <div class="activity-icon grey">✅</div>
                        <div class="activity-text">
                            <p><strong>Room 103</strong> request completed</p>
                            <span>10 minutes ago</span>
                        </div>
                    </li>
                    <li>
                        <div class="activity-icon orange">👤</div>
                        <div class="activity-text">
                            <p><strong>John Smith</strong> checked in to Room 205</p>
                            <span>25 minutes ago</span>
                        </div>
                    </li>
                    <li>
                        <div class="activity-icon grey">🍽️</div>
                        <div class="activity-text">
                            <p><strong>Room 102</strong> ordered room service</p>
                            <span>45 minutes ago</span>
                        </div>
                    </li>
                    <li>
                        <div class="activity-icon orange">⭐</div>
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
        .welcome-banner h2 { font-size: 1.5rem; margin-bottom: 5px; }
        .welcome-banner p { font-size: 0.9rem; opacity: 0.9; }
        .date-display {
            background: rgba(255,255,255,0.2);
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
        .stat-icon.orange { background: #fff5f2; color: #E84E1B; }
        .stat-icon.grey { background: #f4f4f4; color: #58595B; }
        .stat-info h3 { font-size: 1.5rem; color: #58595B; }
        .stat-info p { font-size: 0.85rem; color: #888; }

        /* MAIN GRID */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        @media (max-width: 900px) {
            .main-grid { grid-template-columns: 1fr; }
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
        .dot.available { background: #4CAF50; }
        .dot.occupied { background: #E84E1B; }
        .dot.cleaning { background: #FFC107; }

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
        .room:hover { transform: scale(1.05); }
        .room.available { background: #4CAF50; }
        .room.occupied { background: #E84E1B; }
        .room.cleaning { background: #FFC107; color: #58595B; }

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
        .activity-feed li:last-child { border-bottom: none; }
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
        .activity-icon.orange { background: #fff5f2; }
        .activity-icon.grey { background: #f4f4f4; }
        .activity-text p { font-size: 0.85rem; color: #58595B; }
        .activity-text span { font-size: 0.75rem; color: #999; }
    </style>

    <script>
        // Display current date in the banner
        const dateEl = document.getElementById('current-date');
        const today = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateEl.textContent = today.toLocaleDateString('en-US', options);
    </script>

@endsection