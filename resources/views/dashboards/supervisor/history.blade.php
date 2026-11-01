@extends('layouts.dashboard')
@section('title', 'History')
@section('page-title', 'Request History')
@section('user-role', 'SUPERVISOR')
@section('sidebar-menu') @include('dashboards.supervisor._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Request History</h1>
            <p>All resolved, rejected, and unavailable requests</p>
        </div>
    </div>

    {{-- FILTER TABS --}}
    <div class="history-tabs">
        <a href="{{ route('supervisor.history', ['status' => 'resolved']) }}"
           class="history-tab {{ request('status', 'resolved') === 'resolved' ? 'active' : '' }}">
            <i class="fa-solid fa-circle-check"></i> Resolved ({{ $counts['resolved'] }})
        </a>
        <a href="{{ route('supervisor.history', ['status' => 'rejected']) }}"
           class="history-tab {{ request('status') === 'rejected' ? 'active' : '' }}">
            <i class="fa-solid fa-xmark"></i> Rejected ({{ $counts['rejected'] }})
        </a>
        <a href="{{ route('supervisor.history', ['status' => 'unavailable']) }}"
           class="history-tab {{ request('status') === 'unavailable' ? 'active' : '' }}">
            <i class="fa-solid fa-triangle-exclamation"></i> Unavailable ({{ $counts['unavailable'] }})
        </a>
        <a href="{{ route('supervisor.history', ['status' => 'all']) }}"
           class="history-tab {{ request('status') === 'all' ? 'active' : '' }}">
            <i class="fa-solid fa-list"></i> All History
        </a>
    </div>

    <div class="card">
        <form method="GET" class="search-bar">
            <input type="hidden" name="status" value="{{ request('status', 'resolved') }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by request, code, or guest name...">
            <button type="submit" class="btn-secondary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        </form>

        @if ($history->count())
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Room</th>
                            <th>Guest</th>
                            <th>Request</th>
                            <th>Handled By</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $req)
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
                                </td>
                                <td>
                                    @if ($req->assignee)
                                        <div class="assignee-cell">
                                            <div class="assignee-avatar">{{ $req->assignee->initials }}</div>
                                            <span>{{ $req->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span style="color: #999; font-size: 0.85rem;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $req->status_color }}">
                                        {{ $req->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 0.8rem; color: #888;">
                                        {{ $req->updated_at->format('M d, Y') }}
                                    </span>
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
            <div style="margin-top: 20px;">{{ $history->links() }}</div>
        @else
            <div class="empty-state">
                <div class="empty-icon" style="color: #ccc;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <h3>No history yet</h3>
                <p>Requests that are resolved, rejected, or unavailable will appear here.</p>
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
        .history-tabs { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
        .history-tab { background: white; border: 1px solid #eee; padding: 10px 18px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; color: #58595B; text-decoration: none; transition: all 0.2s; }
        .history-tab i { margin-right: 5px; }
        .history-tab:hover { border-color: #E84E1B; color: #E84E1B; }
        .history-tab.active { background: #E84E1B; border-color: #E84E1B; color: white; }
        
        .req-code { font-size: 0.75rem; font-weight: 600; color: #E84E1B; background: #fff5f2; padding: 3px 8px; border-radius: 5px; }
        .guest-cell { display: flex; align-items: center; gap: 8px; }
        .guest-avatar { width: 28px; height: 28px; border-radius: 50%; background: #f4f4f4; color: #58595B; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; }
        .assignee-cell { display: flex; align-items: center; gap: 8px; }
        .assignee-avatar { width: 28px; height: 28px; border-radius: 50%; background: #E84E1B; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; }
        .assignee-cell span { font-size: 0.85rem; color: #58595B; }
        .req-title { font-weight: 600; color: #58595B; font-size: 0.85rem; }
        
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