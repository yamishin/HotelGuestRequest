@extends('layouts.dashboard')
@section('title', 'History')
@section('page-title', 'Request History')
@section('user-role', 'SUPERVISOR')
@section('sidebar-menu') @include('dashboards.supervisor._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Request History</h1>
            <p>All verified, awaiting confirmation, and cancelled requests</p>
        </div>
    </div>

    {{-- FILTER TABS --}}
    <div class="history-tabs">
        <a href="{{ route('supervisor.history', ['status' => 'verified']) }}"
           class="history-tab {{ request('status', 'verified') === 'verified' ? 'active' : '' }}">
            ✅ Verified ({{ $counts['verified'] }})
        </a>
        <a href="{{ route('supervisor.history', ['status' => 'awaiting']) }}"
           class="history-tab {{ request('status') === 'awaiting' ? 'active' : '' }}">
            ⏳ Awaiting Confirmation ({{ $counts['awaiting'] }})
        </a>
        <a href="{{ route('supervisor.history', ['status' => 'cancelled']) }}"
           class="history-tab {{ request('status') === 'cancelled' ? 'active' : '' }}">
            ❌ Cancelled ({{ $counts['cancelled'] }})
        </a>
        <a href="{{ route('supervisor.history', ['status' => 'all']) }}"
           class="history-tab {{ request('status') === 'all' ? 'active' : '' }}">
            📋 All
        </a>
    </div>

    <div class="card">
        <form method="GET" class="search-bar">
            <input type="hidden" name="status" value="{{ request('status', 'verified') }}">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by request, code, room, or guest name...">
            <button type="submit" class="btn-secondary">Search</button>
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
                            <th>Department</th>
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
                                        <div class="guest-avatar">
                                            {{ strtoupper(substr($req->stay->guest->first_name ?? 'G', 0, 1)) }}
                                        </div>
                                        <span>{{ $req->stay->guest->full_name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="req-title">{{ $req->title }}</div>
                                    @if ($req->description)
                                        <div class="req-desc">{{ Str::limit($req->description, 50) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="dept-badge dept-{{ Str::slug($req->department) }}">
                                        {{ $req->department }}
                                    </span>
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
                                        data-request-completed="{{ $req->completed_at?->format('M d, Y · g:i A') ?? '' }}"
                                        data-request-confirmed="{{ $req->guest_confirmed_at?->format('M d, Y · g:i A') ?? '' }}"
                                        data-request-notes="{{ $req->staff_notes ?? '' }}"
                                        data-request-age="{{ $req->created_at->diffForHumans() }}"
                                        data-guest-name="{{ $req->stay->guest->full_name ?? 'Unknown' }}"
                                        data-guest-code="{{ $req->stay->guest->guest_code ?? '—' }}"
                                        data-guest-email="{{ $req->stay->guest->email ?? '—' }}"
                                        data-guest-phone="{{ $req->stay->guest->phone ?? '—' }}"
                                        data-guest-notes="{{ $req->stay->guest->notes ?? '' }}"
                                        data-room-number="{{ $req->stay->room->room_number ?? '—' }}"
                                        data-room-floor="{{ $req->stay->room->floor ?? '—' }}"
                                        data-room-type="{{ $req->stay->room->type ?? '—' }}"
                                        data-stay-checkin="{{ $req->stay->check_in_date->format('M d, Y') }}"
                                        data-stay-checkout="{{ $req->stay->check_out_date->format('M d, Y') }}"
                                        data-stay-nights="{{ $req->stay->nights }}"
                                        data-stay-guests="{{ $req->stay->num_guests }}"
                                        data-stay-requests="{{ $req->stay->special_requests ?? '' }}"
                                        data-assignee="{{ $req->assignee->name ?? '' }}"
                                        data-assignee-dept="{{ $req->assignee->department ?? '' }}">
                                        View
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
                <div class="empty-icon">📜</div>
                <h3>No history yet</h3>
                <p>Verified and cancelled requests will appear here.</p>
            </div>
        @endif
    </div>

    {{-- ============================================================
         VIEW DETAIL MODAL
         ============================================================ --}}
    <div class="modal-overlay" id="viewModal">
        <div class="modal modal-lg">
            {{-- HERO HEADER --}}
            <div class="modal-hero">
                <div class="modal-hero-content">
                    <div class="modal-hero-top">
                        <span class="hero-code" id="view-code">REQ-0000</span>
                        <span id="view-status-badge" class="hero-badge">Verified</span>
                    </div>
                    <h2 class="hero-title" id="view-title">Request Title</h2>
                    <div class="hero-meta">
                        <span class="hero-meta-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span id="view-age">Submitted —</span>
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

                {{-- DESCRIPTION --}}
                <div class="detail-card" id="view-desc-card">
                    <div class="detail-card-header">
                        <div class="detail-icon">📄</div>
                        <h4>Request Details</h4>
                    </div>
                    <p class="detail-desc" id="view-desc">—</p>
                </div>

                {{-- REQUEST INFO --}}
                <div class="detail-card">
                    <div class="detail-card-header">
                        <div class="detail-icon">ℹ️</div>
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
                            <div class="info-item-icon">🎉</div>
                            <div class="info-item-content">
                                <span class="info-item-label">Guest Confirmed</span>
                                <strong class="info-item-value" id="view-confirmed">—</strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-item-icon">👷</div>
                            <div class="info-item-content">
                                <span class="info-item-label">Handled By</span>
                                <strong class="info-item-value" id="view-assignee">—</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STAFF NOTES --}}
                <div class="detail-card" id="view-notes-card">
                    <div class="detail-card-header">
                        <div class="detail-icon">📝</div>
                        <h4>Staff Notes</h4>
                    </div>
                    <p class="detail-desc" id="view-notes">—</p>
                </div>

                {{-- GUEST --}}
                <div class="detail-card">
                    <div class="detail-card-header">
                        <div class="detail-icon">👤</div>
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
                        <div class="detail-icon">🏨</div>
                        <h4>Booking Information</h4>
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
                        <div class="info-item">
                            <div class="info-item-icon">🛏️</div>
                            <div class="info-item-content">
                                <span class="info-item-label">Room Type</span>
                                <strong class="info-item-value" id="view-room-type">—</strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-item-icon">🌙</div>
                            <div class="info-item-content">
                                <span class="info-item-label">Nights</span>
                                <strong class="info-item-value" id="view-nights">—</strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-item-icon">👥</div>
                            <div class="info-item-content">
                                <span class="info-item-label">Guests</span>
                                <strong class="info-item-value" id="view-guests">—</strong>
                            </div>
                        </div>
                    </div>
                    <div class="view-block" id="view-special-block" style="margin-top: 15px;">
                        <span class="view-label">Special Requests</span>
                        <p class="view-desc" id="view-special">—</p>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeViewModal()">Close</button>
            </div>
        </div>
    </div>

    <style>
        /* HISTORY TABS */
        .history-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .history-tab {
            background: white;
            border: 1px solid #eee;
            padding: 10px 18px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #58595B;
            text-decoration: none;
            transition: all 0.2s;
        }
        .history-tab:hover {
            border-color: var(--primary-orange);
            color: var(--primary-orange);
        }
        .history-tab.active {
            background: var(--primary-orange);
            border-color: var(--primary-orange);
            color: white;
        }

        /* TABLE */
        .table-wrapper { overflow-x: auto; }
        .req-code {
            font-size: 0.75rem; font-weight: 600; color: var(--primary-orange);
            background: #fff5f2; padding: 3px 8px; border-radius: 5px; white-space: nowrap;
        }
        .guest-cell { display: flex; align-items: center; gap: 8px; }
        .guest-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: #f4f4f4; color: #58595B;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; flex-shrink: 0;
        }
        .req-title { font-weight: 600; color: #58595B; font-size: 0.85rem; }
        .req-desc { font-size: 0.75rem; color: #999; margin-top: 3px; }
        .assignee-cell { display: flex; align-items: center; gap: 8px; }
        .assignee-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-orange), #ff6b3d);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; flex-shrink: 0;
        }
        .assignee-cell span { font-size: 0.85rem; color: #58595B; }
        .dept-badge {
            display: inline-block; padding: 4px 10px; border-radius: 50px;
            font-size: 0.7rem; font-weight: 600; white-space: nowrap;
        }
        .dept-housekeeping { background: #e8f5e9; color: #2e7d32; }
        .dept-maintenance { background: #fff3e0; color: #e65100; }
        .dept-it          { background: #e3f2fd; color: #1565c0; }
        .dept-front-desk  { background: #f3e5f5; color: #6a1b9a; }
        .btn-sm { padding: 6px 12px; font-size: 0.75rem; }
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-icon { font-size: 3rem; margin-bottom: 15px; }
        .empty-state h3 { color: #58595B; margin-bottom: 6px; }
        .empty-state p { color: #999; font-size: 0.9rem; }

        /* MODAL — reuse from request_queue */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            display: none; align-items: center; justify-content: center;
            z-index: 2000; padding: 20px;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: white; border-radius: 12px; width: 100%; max-width: 480px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2); overflow: hidden;
        }
        .modal-lg { max-width: 620px; }

        .modal-hero {
            background: linear-gradient(135deg, var(--primary-orange) 0%, #ff6b3d 100%);
            color: white; padding: 25px 30px;
            display: flex; justify-content: space-between; align-items: flex-start;
            position: relative; border-radius: 12px 12px 0 0;
        }
        .modal-hero-content { flex: 1; min-width: 0; }
        .modal-hero-top { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .hero-code {
            font-size: 0.7rem; font-weight: 700; background: rgba(255,255,255,0.2);
            padding: 4px 10px; border-radius: 5px; letter-spacing: 0.5px;
        }
        .hero-badge {
            font-size: 0.7rem; font-weight: 700; background: rgba(255,255,255,0.9);
            color: var(--primary-orange); padding: 4px 10px; border-radius: 50px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .hero-badge.badge-success { background: #e8f5e9; color: #2e7d32; }
        .hero-badge.badge-warning { background: #fff8e1; color: #e65100; }
        .hero-badge.badge-danger  { background: #ffebee; color: #c62828; }
        .hero-badge.badge-info    { background: #e3f2fd; color: #1565c0; }
        .hero-badge.badge-grey    { background: #f4f4f4; color: #58595B; }
        .hero-badge.badge-orange  { background: #fff5f2; color: var(--primary-orange); }

        .hero-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 10px; line-height: 1.3; }
        .hero-meta { display: flex; gap: 18px; flex-wrap: wrap; font-size: 0.8rem; opacity: 0.95; }
        .hero-meta-item { display: flex; align-items: center; gap: 6px; }

        .modal-hero-close {
            background: rgba(255,255,255,0.15); border: none; color: white;
            width: 34px; height: 34px; border-radius: 50%; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: background 0.2s;
        }
        .modal-hero-close:hover { background: rgba(255,255,255,0.3); }

        .modal-body-scroll { max-height: 60vh; overflow-y: auto; padding: 25px 30px; }
        .modal-body-scroll::-webkit-scrollbar { width: 6px; }
        .modal-body-scroll::-webkit-scrollbar-thumb { background: #ddd; border-radius: 3px; }

        .detail-card {
            background: #fafafa; border-radius: 10px; padding: 18px 20px;
            margin-bottom: 15px; border: 1px solid #f0f0f0;
        }
        .detail-card:last-child { margin-bottom: 0; }
        .detail-card-header {
            display: flex; align-items: center; gap: 10px; margin-bottom: 15px;
        }
        .detail-icon {
            width: 30px; height: 30px; border-radius: 8px;
            background: #fff5f2; color: var(--primary-orange);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 0.9rem;
        }
        .detail-card-header h4 {
            font-size: 0.75rem; font-weight: 700; color: #58595B;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .detail-desc {
            font-size: 0.9rem; color: #58595B; line-height: 1.7;
            background: white; padding: 15px 18px; border-radius: 8px;
            border-left: 3px solid var(--primary-orange);
        }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        @media (max-width: 500px) { .info-grid { grid-template-columns: 1fr; } }
        .info-item {
            display: flex; align-items: center; gap: 12px;
            background: white; padding: 12px 15px; border-radius: 8px;
        }
        .info-item-icon { font-size: 1.1rem; flex-shrink: 0; }
        .info-item-content { display: flex; flex-direction: column; min-width: 0; }
        .info-item-label {
            font-size: 0.65rem; color: #999; text-transform: uppercase;
            letter-spacing: 0.5px; font-weight: 600; margin-bottom: 2px;
        }
        .info-item-value {
            font-size: 0.85rem; color: #58595B; font-weight: 600;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .guest-card {
            display: flex; align-items: center; gap: 15px;
            background: white; padding: 15px 18px; border-radius: 8px;
            margin-bottom: 12px;
        }
        .guest-avatar-lg {
            width: 50px; height: 50px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-orange), #ff6b3d);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 700; flex-shrink: 0;
        }
        .guest-main { min-width: 0; }
        .guest-name-lg { font-size: 1rem; font-weight: 700; color: #58595B; margin-bottom: 3px; }
        .guest-code-lg { font-size: 0.75rem; color: #999; }

        .contact-rows { display: flex; flex-direction: column; gap: 8px; }
        .contact-row {
            display: flex; align-items: center; gap: 10px;
            background: white; padding: 10px 15px; border-radius: 8px;
            font-size: 0.85rem; color: #58595B;
        }
        .contact-icon { font-size: 0.95rem; flex-shrink: 0; }
        .contact-value { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .stay-timeline {
            display: flex; align-items: center; gap: 10px;
            background: white; padding: 15px 18px; border-radius: 8px;
        }
        .timeline-block { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; }
        .timeline-icon { font-size: 1.2rem; flex-shrink: 0; }
        .timeline-content { display: flex; flex-direction: column; min-width: 0; }
        .timeline-label {
            font-size: 0.65rem; color: #999; text-transform: uppercase;
            letter-spacing: 0.5px; font-weight: 600;
        }
        .timeline-value {
            font-size: 0.85rem; color: #58595B; font-weight: 600;
            margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .timeline-divider { display: flex; align-items: center; flex-shrink: 0; }
        .timeline-line { width: 15px; height: 2px; background: #eee; }
        .timeline-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--primary-orange); }

        .view-block { padding-top: 15px; }
        .view-label {
            font-size: 0.7rem; color: #999; text-transform: uppercase;
            letter-spacing: 0.5px; font-weight: 600;
        }
        .view-desc {
            margin-top: 8px; font-size: 0.85rem; color: #58595B; line-height: 1.6;
            background: white; padding: 12px 15px; border-radius: 8px;
        }

        .modal-footer {
            padding: 15px 25px; background: #fafafa;
            display: flex; justify-content: flex-end; gap: 10px;
            border-top: 1px solid #f4f4f4;
        }
    </style>

    <script>
        function openViewModal(data) {
            document.getElementById('view-title').textContent = data.title;
            document.getElementById('view-code').textContent = data.code;
            document.getElementById('view-age').textContent = 'Submitted ' + data.age;
            document.getElementById('view-room').textContent = 'Room ' + data.roomNumber;

            const badge = document.getElementById('view-status-badge');
            badge.textContent = data.status;
            badge.className = 'hero-badge badge-' + data.statusColor;

            document.getElementById('view-dept').textContent = data.dept;
            document.getElementById('view-created').textContent = data.created;
            document.getElementById('view-completed').textContent = data.completed || '—';
            document.getElementById('view-confirmed').textContent = data.confirmed || '—';
            document.getElementById('view-assignee').textContent =
                data.assignee ? data.assignee + ' (' + data.assigneeDept + ')' : 'Unassigned';

            // Description
            const descCard = document.getElementById('view-desc-card');
            if (data.desc && data.desc.trim() !== '') {
                document.getElementById('view-desc').textContent = data.desc;
                descCard.style.display = 'block';
            } else {
                descCard.style.display = 'none';
            }

            // Staff notes
            const notesCard = document.getElementById('view-notes-card');
            if (data.notes && data.notes.trim() !== '') {
                document.getElementById('view-notes').textContent = data.notes;
                notesCard.style.display = 'block';
            } else {
                notesCard.style.display = 'none';
            }

            // Guest
            const initials = data.guestName.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
            document.getElementById('view-guest-avatar').textContent = initials;
            document.getElementById('view-guest-name').textContent = data.guestName;
            document.getElementById('view-guest-code').textContent = data.guestCode;
            document.getElementById('view-guest-email').textContent = data.guestEmail;
            document.getElementById('view-guest-phone').textContent = data.guestPhone;

            // Stay
            document.getElementById('view-checkin').textContent = data.stayCheckin;
            document.getElementById('view-checkout').textContent = data.stayCheckout;
            document.getElementById('view-room-number').textContent = data.roomNumber;
            document.getElementById('view-floor').textContent = 'Floor ' + data.roomFloor;
            document.getElementById('view-room-type').textContent = data.roomType;
            document.getElementById('view-nights').textContent = data.nights + ' ' + (data.nights == 1 ? 'night' : 'nights');
            document.getElementById('view-guests').textContent = data.guests + ' ' + (data.guests == 1 ? 'guest' : 'guests');

            const specialBlock = document.getElementById('view-special-block');
            if (data.specialRequests && data.specialRequests.trim() !== '') {
                document.getElementById('view-special').textContent = data.specialRequests;
                specialBlock.style.display = 'block';
            } else {
                specialBlock.style.display = 'none';
            }

            document.getElementById('viewModal').classList.add('active');
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.remove('active');
        }

        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                openViewModal({
                    code:            this.dataset.requestCode,
                    title:           this.dataset.requestTitle,
                    desc:            this.dataset.requestDesc,
                    dept:            this.dataset.requestDept,
                    status:          this.dataset.requestStatus,
                    statusColor:     this.dataset.requestStatusColor,
                    created:         this.dataset.requestCreated,
                    completed:       this.dataset.requestCompleted,
                    confirmed:       this.dataset.requestConfirmed,
                    notes:           this.dataset.requestNotes,
                    age:             this.dataset.requestAge,
                    guestName:       this.dataset.guestName,
                    guestCode:       this.dataset.guestCode,
                    guestEmail:      this.dataset.guestEmail,
                    guestPhone:      this.dataset.guestPhone,
                    roomNumber:      this.dataset.roomNumber,
                    roomFloor:       this.dataset.roomFloor,
                    roomType:        this.dataset.roomType,
                    stayCheckin:     this.dataset.stayCheckin,
                    stayCheckout:    this.dataset.stayCheckout,
                    nights:          this.dataset.stayNights,
                    guests:          this.dataset.stayGuests,
                    specialRequests: this.dataset.stayRequests,
                    assignee:        this.dataset.assignee,
                    assigneeDept:    this.dataset.assigneeDept,
                });
            });
        });

        document.getElementById('viewModal').addEventListener('click', function (e) {
            if (e.target === this) closeViewModal();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeViewModal();
        });
    </script>
@endsection