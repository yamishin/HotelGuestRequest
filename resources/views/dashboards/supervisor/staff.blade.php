@extends('layouts.dashboard')
@section('title', 'Staff')
@section('page-title', 'Staff Overview')
@section('user-role', 'SUPERVISOR')
@section('sidebar-menu') @include('dashboards.supervisor._sidebar') @endsection

@section('content')

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div>
            <h1>Staff Members</h1>
            <p>Manage staff accounts, monitor workloads, and review activity</p>
        </div>
        <a href="{{ route('supervisor.staff.create') }}" class="btn-primary">➕ Add New Staff</a>
    </div>

    {{-- STATS --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon orange">👥</div>
            <div class="stat-info">
                <h3>{{ $totalStaff }}</h3>
                <p>Total Staff</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon grey">✅</div>
            <div class="stat-info">
                <h3>{{ $activeStaff }}</h3>
                <p>Active</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">💤</div>
            <div class="stat-info">
                <h3>{{ $inactiveStaff }}</h3>
                <p>Inactive</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon grey">🔥</div>
            <div class="stat-info">
                <h3>{{ $busyStaff }}</h3>
                <p>High Workload</p>
            </div>
        </div>
    </div>

    {{-- STAFF BY DEPARTMENT --}}
    @forelse ($staffByDepartment as $department => $members)
        <div class="card">
            <h3 class="card-title">
                {{ $department }}
                <span class="dept-count">{{ $members->count() }} {{ Str::plural('member', $members->count()) }}</span>
            </h3>

            <div class="staff-grid">
                @foreach ($members as $member)
                    <a href="{{ route('supervisor.staff.show', $member) }}" class="staff-card {{ $member->status === 'inactive' ? 'inactive' : '' }}">
                        <div class="staff-card-header">
                            <div class="staff-avatar-lg">{{ $member->initials }}</div>
                            <div class="staff-card-info">
                                <div class="staff-card-name">{{ $member->name }}</div>
                                <div class="staff-card-email">{{ $member->employee_code ?? 'No code' }}</div>
                            </div>
                            <span class="badge badge-{{ $member->status_color }}">{{ ucfirst($member->status) }}</span>
                        </div>

                        <div class="staff-card-stats">
                            <div class="staff-stat">
                                <div class="staff-stat-value {{ $member->active_requests_count > 3 ? 'high' : '' }}">
                                    {{ $member->active_requests_count }}
                                </div>
                                <div class="staff-stat-label">Active</div>
                            </div>
                            <div class="staff-stat">
                                <div class="staff-stat-value">{{ $member->total_requests_count }}</div>
                                <div class="staff-stat-label">Total</div>
                            </div>
                            <div class="staff-stat">
                                <div class="staff-stat-value" style="text-transform: capitalize; font-size: 0.9rem;">{{ $member->shift }}</div>
                                <div class="staff-stat-label">Shift</div>
                            </div>
                        </div>

                        @if ($member->active_requests_count > 3)
                            <div class="workload-warning">⚠️ High workload</div>
                        @elseif ($member->active_requests_count === 0 && $member->status === 'active')
                            <div class="workload-available">✓ Available</div>
                        @elseif ($member->status === 'inactive')
                            <div class="workload-inactive">💤 Inactive</div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @empty
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <h3>No staff members yet</h3>
                <p>Add your first staff member to get started.</p>
                <a href="{{ route('supervisor.staff.create') }}" class="btn-primary" style="margin-top: 15px;">➕ Add Staff</a>
            </div>
        </div>
    @endforelse

    {{-- CREDENTIALS MODAL (shows after creating/resetting) --}}
    @if (session('new_staff_credentials'))
        @php $creds = session('new_staff_credentials'); @endphp
        <div class="modal-overlay active" id="credentialsModal">
            <div class="modal">
                <div class="modal-header" style="background: linear-gradient(135deg, #E84E1B, #ff6b3d); color: white;">
                    <h3 style="color: white;">🔑 New Staff Credentials</h3>
                    <button type="button" class="modal-close" style="color: white;" onclick="document.getElementById('credentialsModal').remove()">×</button>
                </div>
                <div class="modal-body">
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 20px;">
                        Please provide these credentials to <strong>{{ $creds['name'] }}</strong>. 
                        <strong style="color: #E84E1B;">They should change this default password after first login.</strong>
                    </p>

                    <div class="cred-box">
                        <div class="cred-row">
                            <span>Employee Code:</span>
                            <strong>{{ $creds['employee_code'] }}</strong>
                        </div>
                        <div class="cred-row">
                            <span>Department:</span>
                            <strong>{{ $creds['department'] }}</strong>
                        </div>
                        <div class="cred-row">
                            <span>Email:</span>
                            <strong>{{ $creds['email'] }}</strong>
                        </div>
                        <div class="cred-row" style="background: #fff9f6; border-left: 3px solid #E84E1B; padding-left: 12px;">
                            <span>Password:</span>
                            <strong style="color: #E84E1B; font-family: monospace; font-size: 1.1rem;">{{ $creds['password'] }}</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-primary" onclick="copyCredentials()">📋 Copy Credentials</button>
                    <button type="button" class="btn-secondary" onclick="document.getElementById('credentialsModal').remove()">Close</button>
                </div>
            </div>
        </div>

        <script>
            function copyCredentials() {
                const text = `Staff Credentials
Name: {{ $creds['name'] }}
Employee Code: {{ $creds['employee_code'] }}
Department: {{ $creds['department'] }}
Email: {{ $creds['email'] }}
Password: {{ $creds['password'] }}

Please log in at: {{ url('/login') }}`;
                navigator.clipboard.writeText(text);
                alert('Credentials copied to clipboard!');
            }
        </script>
    @endif

    {{-- PAGE STYLES --}}
    <style>
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

        .dept-count {
            font-size: 0.75rem;
            color: #999;
            font-weight: 500;
            margin-left: 10px;
        }

        .staff-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
        }

        .staff-card {
            background: #fafafa;
            border-radius: 10px;
            padding: 18px;
            transition: all 0.2s;
            border: 1px solid #f0f0f0;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .staff-card:hover {
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            border-color: #E84E1B;
            transform: translateY(-2px);
        }
        .staff-card.inactive {
            opacity: 0.6;
            background: #f7f7f7;
        }

        .staff-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        .staff-avatar-lg {
            width: 48px; height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #E84E1B, #ff6b3d);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .staff-card-info { flex: 1; min-width: 0; }
        .staff-card-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: #58595B;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .staff-card-email {
            font-size: 0.7rem;
            color: #999;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .staff-card-stats {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
            padding: 12px 0;
            border-top: 1px solid #eee;
        }
        .staff-stat { text-align: center; }
        .staff-stat-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #58595B;
        }
        .staff-stat-value.high { color: #E53935; }
        .staff-stat-label {
            font-size: 0.6rem;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .workload-warning, .workload-available, .workload-inactive {
            font-size: 0.75rem;
            padding: 6px 10px;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
            margin-top: 8px;
        }
        .workload-warning { color: #E53935; background: #ffebee; }
        .workload-available { color: #2e7d32; background: #e8f5e9; }
        .workload-inactive { color: #666; background: #eee; }

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            padding: 20px;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .modal-header {
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h3 { font-size: 1.1rem; }
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            line-height: 1;
        }
        .modal-body { padding: 25px; }
        .cred-box {
            background: #fafafa;
            border-radius: 10px;
            padding: 18px;
        }
        .cred-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 0.85rem;
            border-bottom: 1px solid #f0f0f0;
        }
        .cred-row:last-child { border-bottom: none; }
        .cred-row span { color: #888; }
        .cred-row strong { color: #58595B; }
        .modal-footer {
            padding: 15px 25px;
            background: #fafafa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #f4f4f4;
        }
    </style>
@endsection