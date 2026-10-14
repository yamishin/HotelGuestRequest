@extends('layouts.dashboard')
@section('title', 'Request Queue')
@section('page-title', 'Request Queue')
@section('user-role', 'SUPERVISOR')
@section('sidebar-menu') @include('dashboards.supervisor._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Request Queue</h1>
            <p>Assign pending requests to the right staff member</p>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="card">
        <form method="GET" class="filters-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, code, or room...">

            <select name="status">
                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Open</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>

            <select name="department">
                <option value="all" {{ request('department', 'all') === 'all' ? 'selected' : '' }}>All Departments</option>
                <option value="Housekeeping" {{ request('department') === 'Housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                <option value="Maintenance" {{ request('department') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="IT" {{ request('department') === 'IT' ? 'selected' : '' }}>IT</option>
                <option value="Front Desk" {{ request('department') === 'Front Desk' ? 'selected' : '' }}>Front Desk</option>
            </select>

            <button type="submit" class="btn-primary">Apply Filters</button>
            <a href="{{ route('supervisor.queue') }}" class="btn-secondary">Reset</a>
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
                            <th>Department</th>
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
                                    @if ($req->description)
                                        <div class="req-desc">{{ Str::limit($req->description, 60) }}</div>
                                    @endif
                                    <div class="req-time">{{ $req->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <span class="dept-badge dept-{{ Str::slug($req->department) }}">
                                        {{ $req->department }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $req->status_color }}">
                                        {{ $req->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if ($req->assignee)
                                        <div class="assignee-cell">
                                            <div class="assignee-avatar">{{ strtoupper(substr($req->assignee->name, 0, 2)) }}</div>
                                            <span>{{ $req->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span style="color: #999; font-size: 0.85rem;">Unassigned</span>
                                    @endif
                                </td>
                                                                <td style="text-align: right;">
                                    <div class="actions-cell">
                                        {{-- VIEW BUTTON --}}
                                        <button type="button"
                                            class="btn-secondary btn-sm view-btn"
                                            data-request-id="{{ $req->id }}"
                                            data-request-code="{{ $req->request_code }}"
                                            data-request-title="{{ $req->title }}"
                                            data-request-desc="{{ $req->description ?? '' }}"
                                            data-request-dept="{{ $req->department }}"
                                            data-request-status="{{ $req->status_label }}"
                                            data-request-status-color="{{ $req->status_color }}"
                                            data-request-created="{{ $req->created_at->format('M d, Y · g:i A') }}"
                                            data-request-age="{{ $req->created_at->diffForHumans() }}"
                                            data-guest-name="{{ $req->stay->guest->full_name ?? 'Unknown' }}"
                                            data-guest-code="{{ $req->stay->guest->guest_code ?? '—' }}"
                                            data-guest-email="{{ $req->stay->guest->email ?? '—' }}"
                                            data-guest-phone="{{ $req->stay->guest->phone ?? '—' }}"
                                            data-room-number="{{ $req->stay->room->room_number ?? '—' }}"
                                            data-room-floor="{{ $req->stay->room->floor ?? '—' }}"
                                            data-stay-checkin="{{ $req->stay->check_in_date->format('M d, Y') }}"
                                            data-stay-checkout="{{ $req->stay->check_out_date->format('M d, Y') }}"
                                            data-assignee="{{ $req->assignee->name ?? '' }}"
                                            data-assignee-dept="{{ $req->assignee->department ?? '' }}"
                                            data-completed="{{ $req->completed_at ? $req->completed_at->format('M d, Y · g:i A') : '' }}">
                                            View
                                        </button>

                                        {{-- ASSIGN / UNASSIGN BUTTON --}}
                                        @if (!$req->assignee)
                                            <button type="button"
                                                class="btn-primary btn-sm assign-btn"
                                                data-request-id="{{ $req->id }}"
                                                data-request-title="{{ $req->title }}"
                                                data-request-room="{{ $req->stay->room->room_number ?? '—' }}"
                                                data-request-dept="{{ $req->department }}">
                                                Assign
                                            </button>
                                        @else
                                            <form method="POST"
                                                action="{{ route('supervisor.unassign', $req) }}"
                                                style="display: inline;"
                                                onsubmit="return confirm('Unassign this request?')">
                                                @csrf
                                                <button type="submit" class="btn-secondary btn-sm">Unassign</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">{{ $requests->links() }}</div>
        @else
            <div class="empty-state">
                <div class="empty-icon">🎉</div>
                <h3>All caught up!</h3>
                <p>No requests match your filters. Great work!</p>
            </div>
        @endif
    </div>

    {{-- ASSIGN MODAL --}}
    <div class="modal-overlay" id="assignModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Assign Request</h3>
                <button type="button" class="modal-close" onclick="closeAssignModal()">×</button>
            </div>
            <form method="POST" id="assignForm">
                @csrf
                <div class="modal-body">
                    <div class="modal-info">
                        <div class="info-line"><span>Request:</span><strong id="modal-title">—</strong></div>
                        <div class="info-line"><span>Room:</span><strong id="modal-room">—</strong></div>
                        <div class="info-line"><span>Department:</span><strong id="modal-dept">—</strong></div>
                    </div>

                    <label class="modal-label">Assign to Staff Member</label>
                    <select name="assigned_to" required class="modal-select" id="staff-select">
                        <option value="">-- Choose a staff member --</option>
                        @foreach ($staff as $s)
                            <option value="{{ $s->id }}" data-dept="{{ $s->department }}">
                                {{ $s->name }} — {{ $s->department }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeAssignModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Assign Request</button>
                </div>
            </form>
        </div>
    </div>

        {{-- REQUEST DETAIL MODAL --}}
    <div class="modal-overlay" id="viewModal">
        <div class="modal modal-lg">
            {{-- HERO HEADER --}}
            <div class="modal-hero">
                <div class="modal-hero-content">
                    <div class="modal-hero-top">
                        <span class="hero-code" id="view-code">REQ-0000</span>
                        <span id="view-status-badge" class="hero-badge">Pending</span>
                    </div>
                    <h2 class="hero-title" id="view-title">Request Title</h2>
                    <div class="hero-meta">
                        <span class="hero-meta-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span id="view-age">Submitted just now</span>
                        </span>
                        <span class="hero-meta-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                            <span id="view-room">Room —</span>
                        </span>
                    </div>
                </div>
                <button type="button" class="modal-hero-close" onclick="closeViewModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            {{-- BODY --}}
            <div class="modal-body modal-body-scroll">

                {{-- DESCRIPTION (only shown if present) --}}
                <div class="detail-card" id="view-desc-card">
                    <div class="detail-card-header">
                        <div class="detail-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        </div>
                        <h4>Request Details</h4>
                    </div>
                    <p class="detail-desc" id="view-desc">—</p>
                </div>

                {{-- REQUEST META --}}
                <div class="detail-card">
                    <div class="detail-card-header">
                        <div class="detail-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        </div>
                        <h4>Request Information</h4>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-item-icon">🏷️</div>
                            <div class="info-item-content">
                                <span class="info-item-label">Department</span>
                                <strong class="info-item-value" id="view-dept">—</strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-item-icon">📅</div>
                            <div class="info-item-content">
                                <span class="info-item-label">Submitted</span>
                                <strong class="info-item-value" id="view-created">—</strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-item-icon">✅</div>
                            <div class="info-item-content">
                                <span class="info-item-label">Completed</span>
                                <strong class="info-item-value" id="view-completed">—</strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-item-icon">👷</div>
                            <div class="info-item-content">
                                <span class="info-item-label">Assigned To</span>
                                <strong class="info-item-value" id="view-assignee">Unassigned</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- GUEST --}}
                <div class="detail-card">
                    <div class="detail-card-header">
                        <div class="detail-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <h4>Guest Information</h4>
                    </div>
                    <div class="guest-card">
                        <div class="guest-avatar-lg" id="view-guest-avatar">G</div>
                        <div class="guest-main">
                            <div class="guest-name-lg" id="view-guest-name">Guest Name</div>
                            <div class="guest-code-lg" id="view-guest-code">—</div>
                        </div>
                    </div>
                    <div class="contact-rows">
                        <div class="contact-row">
                            <span class="contact-icon">✉️</span>
                            <span class="contact-value" id="view-guest-email">—</span>
                        </div>
                        <div class="contact-row">
                            <span class="contact-icon">📞</span>
                            <span class="contact-value" id="view-guest-phone">—</span>
                        </div>
                    </div>
                </div>

                {{-- STAY --}}
                <div class="detail-card">
                    <div class="detail-card-header">
                        <div class="detail-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        </div>
                        <h4>Stay Information</h4>
                    </div>
                    <div class="stay-timeline">
                        <div class="timeline-block">
                            <div class="timeline-icon">🛎️</div>
                            <div class="timeline-content">
                                <span class="timeline-label">Check-in</span>
                                <strong class="timeline-value" id="view-checkin">—</strong>
                            </div>
                        </div>
                        <div class="timeline-divider">
                            <div class="timeline-line"></div>
                            <div class="timeline-dot"></div>
                            <div class="timeline-line"></div>
                        </div>
                        <div class="timeline-block">
                            <div class="timeline-icon">🚪</div>
                            <div class="timeline-content">
                                <span class="timeline-label">Check-out</span>
                                <strong class="timeline-value" id="view-checkout">—</strong>
                            </div>
                        </div>
                    </div>
                    <div class="info-grid" style="margin-top: 18px;">
                        <div class="info-item">
                            <div class="info-item-icon">🏠</div>
                            <div class="info-item-content">
                                <span class="info-item-label">Room</span>
                                <strong class="info-item-value" id="view-room-number">—</strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-item-icon">🏢</div>
                            <div class="info-item-content">
                                <span class="info-item-label">Floor</span>
                                <strong class="info-item-value" id="view-floor">—</strong>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeViewModal()">Close</button>
            </div>
        </div>
    </div>

    {{-- PAGE STYLES --}}
    <style>
        /* Filters */
        .filters-bar {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto auto;
            gap: 10px;
            align-items: center;
        }
        .filters-bar input,
        .filters-bar select {
            padding: 10px 14px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.85rem;
            outline: none;
            background: white;
        }
        .filters-bar input:focus,
        .filters-bar select:focus { border-color: #E84E1B; }

        @media (max-width: 900px) {
            .filters-bar { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .filters-bar { grid-template-columns: 1fr; }
        }

        /* Table wrapper for horizontal scroll on small screens */
        .table-wrapper { overflow-x: auto; }

        /* Request code */
        .req-code {
            font-size: 0.75rem;
            font-weight: 600;
            color: #E84E1B;
            background: #fff5f2;
            padding: 3px 8px;
            border-radius: 5px;
            white-space: nowrap;
        }

        /* Guest cell */
        .guest-cell { display: flex; align-items: center; gap: 8px; }
        .guest-avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: #f4f4f4;
            color: #58595B;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* Request title block */
        .req-title { font-weight: 600; color: #58595B; font-size: 0.85rem; }
        .req-desc { font-size: 0.75rem; color: #999; margin-top: 3px; }
        .req-time { font-size: 0.7rem; color: #bbb; margin-top: 4px; }

        /* Department badges */
        .dept-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .dept-housekeeping { background: #e8f5e9; color: #2e7d32; }
        .dept-maintenance { background: #fff3e0; color: #e65100; }
        .dept-it          { background: #e3f2fd; color: #1565c0; }
        .dept-front-desk  { background: #f3e5f5; color: #6a1b9a; }

        /* Assignee cell */
        .assignee-cell { display: flex; align-items: center; gap: 8px; }
        .assignee-avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #E84E1B, #ff6b3d);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .assignee-cell span { font-size: 0.85rem; color: #58595B; }

        /* Actions */
        .actions-cell { display: flex; gap: 6px; justify-content: flex-end; }
        .btn-sm { padding: 6px 12px; font-size: 0.75rem; }

        /* Empty state */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-icon { font-size: 3rem; margin-bottom: 15px; }
        .empty-state h3 { color: #58595B; margin-bottom: 6px; }
        .empty-state p { color: #999; font-size: 0.9rem; }

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
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid #f4f4f4;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h3 { font-size: 1.1rem; color: #58595B; }
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #999;
            cursor: pointer;
            line-height: 1;
        }
        .modal-close:hover { color: #E84E1B; }
        .modal-body { padding: 25px; }
        .modal-info {
            background: #fafafa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-line {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            padding: 4px 0;
        }
        .info-line span { color: #888; }
        .info-line strong { color: #58595B; }
        .modal-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #58595B;
            margin-bottom: 8px;
        }
        .modal-select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            outline: none;
            background: white;
        }
        .modal-select:focus { border-color: #E84E1B; }
        .modal-footer {
            padding: 15px 25px;
            background: #fafafa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #f4f4f4;
        }


                /* ============================================================
           REQUEST DETAIL MODAL — REDESIGNED
           ============================================================ */
        .modal-lg { max-width: 620px; }

        /* HERO HEADER */
        .modal-hero {
            background: linear-gradient(135deg, #E84E1B 0%, #ff6b3d 100%);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            border-radius: 12px 12px 0 0;
        }
        .modal-hero-content { flex: 1; min-width: 0; }
        .modal-hero-top {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        .hero-code {
            font-size: 0.7rem;
            font-weight: 700;
            background: rgba(255,255,255,0.2);
            padding: 4px 10px;
            border-radius: 5px;
            letter-spacing: 0.5px;
        }
        .hero-badge {
            font-size: 0.7rem;
            font-weight: 700;
            background: rgba(255,255,255,0.9);
            color: #E84E1B;
            padding: 4px 10px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .hero-badge.badge-success  { background: #e8f5e9; color: #2e7d32; }
        .hero-badge.badge-warning  { background: #fff8e1; color: #e65100; }
        .hero-badge.badge-danger   { background: #ffebee; color: #c62828; }
        .hero-badge.badge-info     { background: #e3f2fd; color: #1565c0; }
        .hero-badge.badge-grey     { background: #f4f4f4; color: #58595B; }

        .hero-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        .hero-meta {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
            font-size: 0.8rem;
            opacity: 0.95;
        }
        .hero-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .modal-hero-close {
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background 0.2s;
        }
        .modal-hero-close:hover { background: rgba(255,255,255,0.3); }

        /* SCROLLABLE BODY */
        .modal-body-scroll {
            max-height: 60vh;
            overflow-y: auto;
            padding: 25px 30px;
        }
        .modal-body-scroll::-webkit-scrollbar { width: 6px; }
        .modal-body-scroll::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 3px;
        }

        /* DETAIL CARDS */
        .detail-card {
            background: #fafafa;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 15px;
            border: 1px solid #f0f0f0;
        }
        .detail-card:last-child { margin-bottom: 0; }

        .detail-card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        .detail-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #fff5f2;
            color: #E84E1B;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .detail-card-header h4 {
            font-size: 0.75rem;
            font-weight: 700;
            color: #58595B;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* DESCRIPTION */
        .detail-desc {
            font-size: 0.9rem;
            color: #58595B;
            line-height: 1.7;
            background: white;
            padding: 15px 18px;
            border-radius: 8px;
            border-left: 3px solid #E84E1B;
        }

        /* INFO GRID */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        @media (max-width: 500px) {
            .info-grid { grid-template-columns: 1fr; }
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            padding: 12px 15px;
            border-radius: 8px;
        }
        .info-item-icon {
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .info-item-content {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .info-item-label {
            font-size: 0.65rem;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .info-item-value {
            font-size: 0.85rem;
            color: #58595B;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* GUEST CARD */
        .guest-card {
            display: flex;
            align-items: center;
            gap: 15px;
            background: white;
            padding: 15px 18px;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .guest-avatar-lg {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #E84E1B, #ff6b3d);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .guest-main { min-width: 0; }
        .guest-name-lg {
            font-size: 1rem;
            font-weight: 700;
            color: #58595B;
            margin-bottom: 3px;
        }
        .guest-code-lg {
            font-size: 0.75rem;
            color: #999;
        }

        .contact-rows {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .contact-row {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #58595B;
        }
        .contact-icon {
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .contact-value {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* STAY TIMELINE */
        .stay-timeline {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 15px 18px;
            border-radius: 8px;
        }
        .timeline-block {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 0;
        }
        .timeline-icon {
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .timeline-content {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .timeline-label {
            font-size: 0.65rem;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .timeline-value {
            font-size: 0.85rem;
            color: #58595B;
            font-weight: 600;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .timeline-divider {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        .timeline-line {
            width: 15px;
            height: 2px;
            background: #eee;
        }
        .timeline-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #E84E1B;
        }
    </style>

    <script>
        function openAssignModal(requestId, title, room, dept) {
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-room').textContent  = room;
            document.getElementById('modal-dept').textContent  = dept;

            // Set the form action to the correct assign route
            document.getElementById('assignForm').action = '/dashboard/supervisor/assign/' + requestId;

            // Pre-select staff from same department (optional but helpful)
            const select = document.getElementById('staff-select');
            select.value = '';
            Array.from(select.options).forEach(opt => {
                if (opt.dataset.dept === dept) {
                    opt.style.fontWeight = 'bold';
                } else {
                    opt.style.fontWeight = 'normal';
                }
            });

            document.getElementById('assignModal').classList.add('active');
        }

        function closeAssignModal() {
            document.getElementById('assignModal').classList.remove('active');
        }

        // Attach click handlers to assign buttons
        document.querySelectorAll('.assign-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                openAssignModal(
                    this.dataset.requestId,
                    this.dataset.requestTitle,
                    this.dataset.requestRoom,
                    this.dataset.requestDept
                );
            });
        });

        // Close modal when clicking outside
        document.getElementById('assignModal').addEventListener('click', function (e) {
            if (e.target === this) closeAssignModal();
        });

        // Close on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeAssignModal();
        });

                /* ============================================================
         |  REQUEST DETAIL MODAL
         ============================================================ */
                function openViewModal(data) {
            // Hero
            document.getElementById('view-title').textContent = data.title;
            document.getElementById('view-code').textContent  = data.code;
            document.getElementById('view-age').textContent   = 'Submitted ' + data.age;
            document.getElementById('view-room').textContent  = 'Room ' + data.roomNumber;

            // Status badge
            const badge = document.getElementById('view-status-badge');
            badge.textContent = data.status;
            badge.className = 'hero-badge badge-' + data.statusColor;

            // Meta fields
            document.getElementById('view-dept').textContent       = data.dept;
            document.getElementById('view-created').textContent    = data.created;
            document.getElementById('view-completed').textContent  = data.completed || '—';
            document.getElementById('view-assignee').textContent   =
                data.assignee ? data.assignee + ' (' + data.assigneeDept + ')' : 'Unassigned';

            // Description — show card only if present
            const descCard = document.getElementById('view-desc-card');
            if (data.desc && data.desc.trim() !== '') {
                document.getElementById('view-desc').textContent = data.desc;
                descCard.style.display = 'block';
            } else {
                descCard.style.display = 'none';
            }

            // Guest
            const initials = data.guestName
                .split(' ')
                .map(n => n[0])
                .slice(0, 2)
                .join('')
                .toUpperCase();
            document.getElementById('view-guest-avatar').textContent = initials;
            document.getElementById('view-guest-name').textContent    = data.guestName;
            document.getElementById('view-guest-code').textContent    = data.guestCode;
            document.getElementById('view-guest-email').textContent   = data.guestEmail;
            document.getElementById('view-guest-phone').textContent   = data.guestPhone;

            // Stay
            document.getElementById('view-checkin').textContent      = data.stayCheckin;
            document.getElementById('view-checkout').textContent     = data.stayCheckout;
            document.getElementById('view-room-number').textContent  = data.roomNumber;
            document.getElementById('view-floor').textContent        = 'Floor ' + data.roomFloor;

            // Show
            document.getElementById('viewModal').classList.add('active');
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.remove('active');
        }

        // Attach handlers to all View buttons
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                openViewModal({
                    id:              this.dataset.requestId,
                    code:            this.dataset.requestCode,
                    title:           this.dataset.requestTitle,
                    desc:            this.dataset.requestDesc,
                    dept:            this.dataset.requestDept,
                    status:          this.dataset.requestStatus,
                    statusColor:     this.dataset.requestStatusColor,
                    created:         this.dataset.requestCreated,
                    age:             this.dataset.requestAge,
                    guestName:       this.dataset.guestName,
                    guestCode:       this.dataset.guestCode,
                    guestEmail:      this.dataset.guestEmail,
                    guestPhone:      this.dataset.guestPhone,
                    roomNumber:      this.dataset.roomNumber,
                    roomFloor:       this.dataset.roomFloor,
                    stayCheckin:     this.dataset.stayCheckin,
                    stayCheckout:    this.dataset.stayCheckout,
                    assignee:        this.dataset.assignee,
                    assigneeDept:    this.dataset.assigneeDept,
                    completed:       this.dataset.completed,
                });
            });
        });

        // Close the view modal when clicking outside
        document.getElementById('viewModal').addEventListener('click', function (e) {
            if (e.target === this) closeViewModal();
        });

        // Also close the view modal with Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeViewModal();
        });
    </script>
@endsection