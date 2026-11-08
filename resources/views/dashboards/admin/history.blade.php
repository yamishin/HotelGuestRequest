@extends('layouts.dashboard')
@section('title', 'Global History')
@section('page-title', 'Global Request History')
@section('user-role', 'ADMIN')
@section('sidebar-menu')
    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-users-gear"></i> Manage Accounts</a></li>
    <li><a href="{{ route('admin.history') }}" class="{{ request()->routeIs('admin.history') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> Global History</a></li>
    <li><a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> Global Reports</a></li>
    <li><a href="{{ route('admin.supervisor.create') }}" class="{{ request()->routeIs('admin.supervisor.create') ? 'active' : '' }}"><i class="fa-solid fa-user-plus"></i> Add Supervisor</a></li>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Global Request History</h1>
            <p>Monitor all guest requests across all departments</p>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="card">
        <form method="GET" class="filters-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code, title, guest, or room...">
            
            <select name="status">
                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="routed" {{ request('status') === 'routed' ? 'selected' : '' }}>Routed</option>
                <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <select name="department">
                <option value="all" {{ request('department', 'all') === 'all' ? 'selected' : '' }}>All Departments</option>
                <option value="Housekeeping" {{ request('department') === 'Housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                <option value="Maintenance" {{ request('department') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="IT" {{ request('department') === 'IT' ? 'selected' : '' }}>IT</option>
                <option value="Front Desk" {{ request('department') === 'Front Desk' ? 'selected' : '' }}>Front Desk</option>
            </select>

            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('admin.history') }}" class="btn-secondary">Reset</a>
        </form>
    </div>

    {{-- REQUEST LIST --}}
    <div class="card">
        @if ($requests->count())
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Room</th>
                            <th>Guest</th>
                            <th>Request</th>
                            <th>Dept</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $req)
                            <tr>
                                <td><span class="req-code">{{ $req->request_code }}</span></td>
                                <td><strong>{{ $req->stay->room->room_number ?? '—' }}</strong></td>
                                <td>
                                    <div class="guest-cell">
                                        <div class="guest-avatar">{{ strtoupper(substr($req->stay->guest->first_name ?? 'G', 0, 1)) }}</div>
                                        <span>{{ $req->stay->guest->full_name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="req-title">{{ $req->title }}</div>
                                    <div class="req-time">{{ $req->created_at->diffForHumans() }}</div>
                                </td>
                                <td><span class="dept-badge dept-{{ Str::slug($req->department) }}">{{ $req->department }}</span></td>
                                <td><span class="badge badge-{{ $req->status_color }}">{{ $req->status_label }}</span></td>
                                <td>
                                    @if ($req->assignee)
                                        <div class="assignee-cell">
                                            <div class="assignee-avatar">{{ $req->assignee->initials }}</div>
                                            <span>{{ $req->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span style="color: #999; font-size: 0.85rem;">Unassigned</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn-secondary btn-sm view-btn"
                                        data-code="{{ $req->request_code }}"
                                        data-title="{{ $req->title }}"
                                        data-desc="{{ $req->description ?? '' }}"
                                        data-dept="{{ $req->department }}"
                                        data-status="{{ $req->status_label }}"
                                        data-color="{{ $req->status_color }}"
                                        data-date="{{ $req->updated_at->format('M d, Y · g:i A') }}"
                                        data-guest="{{ $req->stay->guest->full_name ?? 'Unknown' }}"
                                        data-room="{{ $req->stay->room->room_number ?? '—' }}"
                                        data-assignee="{{ $req->assignee->name ?? '—' }}"
                                        data-notes="{{ $req->staff_notes ?? '' }}"
                                        data-reason="{{ $req->rejection_reason ?? '' }}">
                                        <i class="fa-solid fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 20px;">{{ $requests->links() }}</div>
        @else
            <div class="empty-state">
                <div class="empty-icon" style="color: #ccc;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <h3>No history yet</h3>
                <p>No requests match your filters.</p>
            </div>
        @endif
    </div>

    {{-- VIEW DETAIL MODAL --}}
    <div class="modal-overlay" id="viewModal">
        <div class="modal">
            <div class="modal-header" style="background: linear-gradient(135deg, #58595B 0%, #333 100%); color: white;">
                <div>
                    <h3 id="m-title" style="color: white;">Request Title</h3>
                    <span id="m-code" style="font-size: 0.75rem; opacity: 0.8;">REQ-0000</span>
                </div>
                <button type="button" class="modal-close" style="color: white;" onclick="document.getElementById('viewModal').classList.remove('active')">×</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 15px;">
                    <span id="m-status" class="badge badge-grey">Status</span>
                    <span style="float: right; font-size: 0.8rem; color: #999;" id="m-date"></span>
                </div>

                <div class="detail-grid">
                    <div class="detail-item"><span>Guest</span><strong id="m-guest"></strong></div>
                    <div class="detail-item"><span>Room</span><strong id="m-room"></strong></div>
                    <div class="detail-item"><span>Department</span><strong id="m-dept"></strong></div>
                    <div class="detail-item"><span>Handled By</span><strong id="m-assignee"></strong></div>
                </div>

                <div class="detail-box">
                    <span>Description</span>
                    <p id="m-desc">—</p>
                </div>

                <div class="detail-box" id="box-notes" style="display: none; border-left-color: #4CAF50;">
                    <span>Staff Notes</span>
                    <p id="m-notes">—</p>
                </div>

                <div class="detail-box" id="box-reason" style="display: none; border-left-color: #E53935;">
                    <span>Reason</span>
                    <p id="m-reason">—</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="document.getElementById('viewModal').classList.remove('active')">Close</button>
            </div>
        </div>
    </div>

    <style>
        .filters-bar { display: grid; grid-template-columns: 2fr 1fr 1fr auto auto; gap: 10px; align-items: center; }
        .filters-bar input, .filters-bar select { padding: 10px 14px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat', sans-serif; font-size: 0.85rem; outline: none; background: white; }
        .filters-bar input:focus, .filters-bar select:focus { border-color: #E84E1B; }
        @media (max-width: 900px) { .filters-bar { grid-template-columns: 1fr 1fr; } }
        
        .table-wrapper { overflow-x: auto; }
        .req-code { font-size: 0.75rem; font-weight: 600; color: #E84E1B; background: #fff5f2; padding: 3px 8px; border-radius: 5px; white-space: nowrap; }
        .guest-cell { display: flex; align-items: center; gap: 8px; }
        .guest-avatar { width: 28px; height: 28px; border-radius: 50%; background: #f4f4f4; color: #58595B; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; }
        .req-title { font-weight: 600; color: #58595B; font-size: 0.85rem; }
        .req-time { font-size: 0.7rem; color: #bbb; margin-top: 4px; }
        .dept-badge { display: inline-block; padding: 4px 10px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; }
        .dept-housekeeping { background: #e8f5e9; color: #2e7d32; }
        .dept-maintenance { background: #fff3e0; color: #e65100; }
        .dept-it { background: #e3f2fd; color: #1565c0; }
        .dept-front-desk { background: #f3e5f5; color: #6a1b9a; }
        .assignee-cell { display: flex; align-items: center; gap: 8px; }
        .assignee-avatar { width: 28px; height: 28px; border-radius: 50%; background: #E84E1B; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; }
        .assignee-cell span { font-size: 0.85rem; color: #58595B; }
        .btn-sm { padding: 6px 12px; font-size: 0.75rem; }
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-icon { font-size: 3rem; margin-bottom: 15px; }
        .empty-state h3 { color: #58595B; margin-bottom: 6px; }
        .empty-state p { color: #999; font-size: 0.9rem; }

        /* Modal */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 2000; padding: 20px; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 12px; width: 100%; max-width: 500px; overflow: hidden; }
        .modal-header { padding: 20px 25px; display: flex; justify-content: space-between; align-items: start; }
        .modal-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; }
        .modal-body { padding: 25px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; background: #fafafa; padding: 15px; border-radius: 8px; }
        .detail-item { display: flex; flex-direction: column; }
        .detail-item span { font-size: 0.7rem; color: #888; text-transform: uppercase; }
        .detail-item strong { font-size: 0.9rem; color: #58595B; margin-top: 2px; }
        .detail-box { background: #fafafa; padding: 15px; border-radius: 8px; border-left: 3px solid #E84E1B; margin-bottom: 15px; }
        .detail-box span { font-size: 0.75rem; font-weight: 700; color: #888; text-transform: uppercase; }
        .detail-box p { font-size: 0.85rem; color: #58595B; margin-top: 5px; line-height: 1.5; }
        .modal-footer { padding: 15px 25px; background: #fafafa; display: flex; justify-content: flex-end; border-top: 1px solid #f4f4f4; }
    </style>

    <script>
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.getElementById('m-title').textContent = this.dataset.title;
                document.getElementById('m-code').textContent = this.dataset.code;
                document.getElementById('m-date').textContent = this.dataset.date;
                
                const badge = document.getElementById('m-status');
                badge.textContent = this.dataset.status;
                badge.className = 'badge badge-' + this.dataset.color;

                document.getElementById('m-guest').textContent = this.dataset.guest;
                document.getElementById('m-room').textContent = this.dataset.room;
                document.getElementById('m-dept').textContent = this.dataset.dept;
                document.getElementById('m-assignee').textContent = this.dataset.assignee;
                document.getElementById('m-desc').textContent = this.dataset.desc || 'No description provided.';

                const notesBox = document.getElementById('box-notes');
                if (this.dataset.notes) {
                    document.getElementById('m-notes').textContent = this.dataset.notes;
                    notesBox.style.display = 'block';
                } else {
                    notesBox.style.display = 'none';
                }

                const reasonBox = document.getElementById('box-reason');
                if (this.dataset.reason) {
                    document.getElementById('m-reason').textContent = this.dataset.reason;
                    reasonBox.style.display = 'block';
                } else {
                    reasonBox.style.display = 'none';
                }

                document.getElementById('viewModal').classList.add('active');
            });
        });
    </script>
@endsection