@extends('layouts.dashboard')
@section('title', 'Supervisor Dashboard')
@section('page-title', 'Supervisor Operations')
@section('user-role', 'SUPERVISOR')
@section('sidebar-menu') @include('dashboards.supervisor._sidebar') @endsection

@section('content')

    {{-- WELCOME BANNER --}}
    <div class="welcome-banner">
        <div>
            <h2>Welcome, {{ auth()->user()->name }}! 👋</h2>
            <p>Oversee operations, assign requests, and keep the team moving.</p>
        </div>
        <div class="date-display">
            <span id="current-date"></span>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon orange">⏳</div>
            <div class="stat-info">
                <h3>{{ $pendingCount }}</h3>
                <p>Pending Requests</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon grey">👤</div>
            <div class="stat-info">
                <h3>{{ $assignedCount }}</h3>
                <p>Assigned</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">🔧</div>
            <div class="stat-info">
                <h3>{{ $inProgressCount }}</h3>
                <p>In Progress</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon grey">✅</div>
            <div class="stat-info">
                <h3>{{ $completedToday }}</h3>
                <p>Completed Today</p>
            </div>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="main-grid">

        {{-- LEFT: RECENT REQUESTS --}}
        <div class="left-column">
            <div class="card">
                <h3 class="card-title">
                    Recent Requests
                    <a href="{{ route('supervisor.queue') }}" style="font-size: 0.75rem; color: #E84E1B; text-decoration: none;">View Queue →</a>
                </h3>

                @forelse ($recentRequests as $req)
                    <div class="req-row">
                        <div class="req-info">
                            <div class="req-title-line">
                                <strong>{{ $req->title }}</strong>
                                <span class="req-code">{{ $req->request_code }}</span>
                            </div>
                            <div class="req-meta">
                                Room {{ $req->stay->room->room_number }} · {{ $req->stay->guest->full_name }} · {{ $req->department }}
                            </div>
                        </div>
                        <div class="req-right">
                            <span class="badge badge-{{ $req->status_color }}">
                                {{ $req->status_label }}
                            </span>
                            <div class="req-assignee">
                                @if ($req->assignee)
                                    → {{ $req->assignee->name }}
                                @else
                                    <span style="color: #999;">Unassigned</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color: #999; text-align: center; padding: 20px;">No requests yet.</p>
                @endforelse
            </div>
        </div>

        {{-- RIGHT: STAFF WORKLOAD --}}
        <div class="right-column">
            <div class="card">
                <h3 class="card-title">
                    Staff Workload
                    <a href="{{ route('supervisor.staff') }}" style="font-size: 0.75rem; color: #E84E1B; text-decoration: none;">All Staff →</a>
                </h3>

                @forelse ($staffWorkload as $staff)
                    <div class="staff-row">
                        <div class="staff-avatar">
                            {{ strtoupper(substr($staff->name, 0, 2)) }}
                        </div>
                        <div class="staff-info">
                            <div class="staff-name">{{ $staff->name }}</div>
                            <div class="staff-dept">{{ $staff->department }}</div>
                        </div>
                        <div class="staff-count">
                            <span class="count-badge {{ $staff->active_requests_count > 3 ? 'high' : '' }}">
                                {{ $staff->active_requests_count }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p style="color: #999; text-align: center; padding: 20px;">No staff members yet.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- PAGE STYLES --}}
    <style>
        /* Welcome Banner */
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

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            font-size: 1.4rem;
        }
        .stat-icon.orange { background: #fff5f2; }
        .stat-icon.grey   { background: #f4f4f4; }
        .stat-info h3 { font-size: 1.5rem; color: #58595B; }
        .stat-info p  { font-size: 0.85rem; color: #888; }

        /* Main Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        @media (max-width: 900px) {
            .main-grid { grid-template-columns: 1fr; }
        }

        /* Request Row */
        .req-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid #f4f4f4;
            gap: 15px;
        }
        .req-row:last-child { border-bottom: none; }
        .req-info { flex: 1; min-width: 0; }
        .req-title-line { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .req-title-line strong { color: #58595B; font-size: 0.9rem; }
        .req-code { font-size: 0.7rem; color: #999; }
        .req-meta { font-size: 0.75rem; color: #888; margin-top: 4px; }
        .req-right { text-align: right; flex-shrink: 0; }
        .req-assignee { font-size: 0.75rem; color: #58595B; margin-top: 6px; font-weight: 600; }

        /* Staff Row */
        .staff-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f4f4f4;
        }
        .staff-row:last-child { border-bottom: none; }
        .staff-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #E84E1B, #ff6b3d);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        .staff-info { flex: 1; min-width: 0; }
        .staff-name { font-size: 0.85rem; color: #58595B; font-weight: 600; }
        .staff-dept { font-size: 0.7rem; color: #999; }
        .staff-count { flex-shrink: 0; }
        .count-badge {
            display: inline-block;
            min-width: 28px;
            padding: 4px 8px;
            border-radius: 50px;
            background: #f4f4f4;
            color: #58595B;
            font-weight: 700;
            font-size: 0.8rem;
            text-align: center;
        }
        .count-badge.high {
            background: #ffebee;
            color: #E53935;
        }
    </style>

    <script>
        const dateEl = document.getElementById('current-date');
        const today = new Date();
        dateEl.textContent = today.toLocaleDateString('en-US', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    </script>

@endsection