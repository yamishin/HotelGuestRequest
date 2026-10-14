@extends('layouts.dashboard')
@section('title', $user->name)
@section('page-title', 'Staff Profile')
@section('user-role', 'SUPERVISOR')
@section('sidebar-menu') @include('dashboards.supervisor._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $user->name }}</h1>
            <p>{{ $user->employee_code }} · {{ $user->department }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('supervisor.staff.edit', $user) }}" class="btn-secondary">✏️ Edit</a>
            <form method="POST" action="{{ route('supervisor.staff.toggleStatus', $user) }}" style="display: inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn-secondary" onclick="return confirm('Change status for {{ $user->name }}?')">
                    {{ $user->status === 'active' ? '💤 Deactivate' : '✅ Activate' }}
                </button>
            </form>
            <form method="POST" action="{{ route('supervisor.staff.resetPassword', $user) }}" style="display: inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn-primary" onclick="return confirm('Reset password for {{ $user->name }}?')">🔑 Reset Password</button>
            </form>
            <a href="{{ route('supervisor.staff') }}" class="btn-secondary">← Back</a>
        </div>
    </div>

    {{-- PROFILE HERO --}}
    <div class="profile-hero">
        <div class="profile-hero-left">
            <div class="profile-avatar-xl">{{ $user->initials }}</div>
            <div>
                <h2>{{ $user->name }}</h2>
                <p>{{ $user->department }} · <span style="text-transform: capitalize;">{{ $user->shift }} shift</span></p>
                <span class="badge badge-{{ $user->status_color }}">{{ ucfirst($user->status) }}</span>
            </div>
        </div>
        <div class="profile-hero-right">
            <div class="hero-stat">
                <div class="hero-stat-value">{{ $user->activeRequests->count() }}</div>
                <div class="hero-stat-label">Active</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-value">{{ $completedThisWeek }}</div>
                <div class="hero-stat-label">This Week</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-value">{{ $completedAllTime }}</div>
                <div class="hero-stat-label">All Time</div>
            </div>
        </div>
    </div>

    <div class="detail-grid">
        {{-- CONTACT INFO --}}
        <div class="card">
            <h3 class="card-title">Contact Information</h3>
            <div class="detail-row"><span>Employee Code</span><strong>{{ $user->employee_code }}</strong></div>
            <div class="detail-row"><span>Email</span><strong>{{ $user->email }}</strong></div>
            <div class="detail-row"><span>Phone</span><strong>{{ $user->phone ?? '—' }}</strong></div>
            <div class="detail-row"><span>Department</span><strong>{{ $user->department }}</strong></div>
            <div class="detail-row"><span>Shift</span><strong style="text-transform: capitalize;">{{ $user->shift }}</strong></div>
            <div class="detail-row"><span>Hire Date</span><strong>{{ $user->hire_date?->format('M d, Y') ?? '—' }}</strong></div>
        </div>

        {{-- WORKLOAD STATS --}}
        <div class="card">
            <h3 class="card-title">Workload Statistics</h3>
            <div class="detail-row"><span>Total Assigned</span><strong>{{ $totalAssigned }}</strong></div>
            <div class="detail-row"><span>Currently Active</span><strong>{{ $user->activeRequests->count() }}</strong></div>
            <div class="detail-row"><span>Completed This Week</span><strong>{{ $completedThisWeek }}</strong></div>
            <div class="detail-row"><span>Completed All Time</span><strong>{{ $completedAllTime }}</strong></div>
        </div>
    </div>

    {{-- CURRENT ASSIGNMENTS --}}
    <div class="card">
        <h3 class="card-title">Current Assignments</h3>

        @forelse ($activeRequests as $req)
            <div class="task-row">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                        <strong style="color: #58595B; font-size: 0.9rem;">{{ $req->title }}</strong>
                        <span class="req-code">{{ $req->request_code }}</span>
                    </div>
                    <div style="font-size: 0.75rem; color: #888;">
                        Room {{ $req->stay->room->room_number ?? '—' }} · {{ $req->stay->guest->full_name ?? 'Unknown' }}
                    </div>
                </div>
                <div style="text-align: right;">
                    <span class="badge badge-{{ $req->status_color }}">{{ $req->status_label }}</span>
                </div>
            </div>
        @empty
            <p style="color: #999; text-align: center; padding: 30px 0;">No active assignments. This staff member is available.</p>
        @endforelse
    </div>

    <style>
        .profile-hero {
            background: linear-gradient(135deg, #E84E1B 0%, #ff6b3d 100%);
            color: white;
            padding: 30px 35px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 20px;
        }
        .profile-hero-left { display: flex; align-items: center; gap: 20px; }
        .profile-avatar-xl {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            border: 3px solid rgba(255,255,255,0.3);
        }
        .profile-hero h2 { font-size: 1.5rem; margin-bottom: 4px; }
        .profile-hero p { font-size: 0.85rem; opacity: 0.9; margin-bottom: 10px; }
        .profile-hero-right {
            display: flex;
            gap: 30px;
        }
        .hero-stat { text-align: center; }
        .hero-stat-value { font-size: 1.8rem; font-weight: 700; line-height: 1; }
        .hero-stat-label { font-size: 0.7rem; opacity: 0.85; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        @media (max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f4f4f4;
            font-size: 0.9rem;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-row span { color: #888; }

        .task-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f4f4f4;
            gap: 15px;
        }
        .task-row:last-child { border-bottom: none; }

        .req-code {
            font-size: 0.7rem;
            font-weight: 600;
            color: #E84E1B;
            background: #fff5f2;
            padding: 3px 8px;
            border-radius: 5px;
        }
    </style>
@endsection