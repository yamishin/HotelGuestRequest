@extends('layouts.dashboard')
@section('title', 'Staff Dashboard')
@section('page-title', 'My Dashboard')
@section('user-role', 'STAFF')
@section('sidebar-menu') @include('dashboards.staff._sidebar') @endsection

@section('content')

    {{-- WELCOME BANNER --}}
    <div class="welcome-banner">
        <div>
            <h2>Hello, {{ $user->name }}! 👋</h2>
            <p>{{ $user->department }} · <span style="text-transform: capitalize;">{{ $user->shift }} shift</span></p>
        </div>
        <div class="date-display">
            <span id="current-date"></span>
        </div>
    </div>

    {{-- STATS --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon orange">📋</div>
            <div class="stat-info">
                <h3>{{ $stats['active'] }}</h3>
                <p>Active Tasks</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon grey">✅</div>
            <div class="stat-info">
                <h3>{{ $stats['completed_today'] }}</h3>
                <p>Completed Today</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">📅</div>
            <div class="stat-info">
                <h3>{{ $stats['completed_week'] }}</h3>
                <p>This Week</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon grey">🏆</div>
            <div class="stat-info">
                <h3>{{ $stats['completed_total'] }}</h3>
                <p>All Time</p>
            </div>
        </div>
    </div>

    {{-- MY TASKS --}}
    <div class="card">
        <h3 class="card-title">
            My Current Tasks
            <a href="{{ route('staff.tasks') }}" style="font-size: 0.75rem; color: #E84E1B; text-decoration: none;">View All →</a>
        </h3>

        @forelse ($myTasks as $task)
            <div class="task-row">
                <div class="task-info">
                    <div class="task-title-line">
                        <strong>{{ $task->title }}</strong>
                        <span class="task-code">{{ $task->request_code }}</span>
                    </div>
                    <div class="task-meta">
                        Room {{ $task->stay->room->room_number ?? '—' }} · {{ $task->stay->guest->full_name ?? 'Unknown' }}
                    </div>
                </div>
                <div class="task-right">
                    <span class="badge badge-{{ $task->status_color }}">{{ $task->status_label }}</span>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 40px 20px;">
                <div style="font-size: 2.5rem; margin-bottom: 10px;">🎉</div>
                <p style="color: #999;">No active tasks. You're all caught up!</p>
            </div>
        @endforelse
    </div>

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

        .task-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f4f4f4;
            gap: 15px;
        }
        .task-row:last-child { border-bottom: none; }
        .task-info { flex: 1; min-width: 0; }
        .task-title-line { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .task-title-line strong { color: #58595B; font-size: 0.9rem; }
        .task-code {
            font-size: 0.7rem;
            color: #E84E1B;
            background: #fff5f2;
            padding: 3px 8px;
            border-radius: 5px;
            font-weight: 600;
        }
        .task-meta { font-size: 0.75rem; color: #888; margin-top: 4px; }
        .task-right { flex-shrink: 0; }
    </style>

    <script>
        const dateEl = document.getElementById('current-date');
        const today = new Date();
        dateEl.textContent = today.toLocaleDateString('en-US', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    </script>
@endsection