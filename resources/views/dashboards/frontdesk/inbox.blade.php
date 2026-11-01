@extends('layouts.dashboard')
@section('title', 'Request Inbox')
@section('page-title', 'Request Inbox')
@section('user-role', 'FRONT DESK')
@section('sidebar-menu') @include('dashboards.frontdesk._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Request Inbox</h1>
            <p>Review, accept, and route incoming guest requests</p>
        </div>
        <div style="background: #fff5f2; color: #E84E1B; padding: 10px 20px; border-radius: 50px; font-weight: 700;">
            {{ $pendingCount }} Pending
        </div>
    </div>

    <div class="card">
        <form method="GET" class="search-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by room, guest, or request...">
            <button type="submit" class="btn-secondary">Search</button>
        </form>

        @forelse ($requests as $req)
            <div class="inbox-card">
                <div class="inbox-header">
                    <div>
                        <span class="req-code">{{ $req->request_code }}</span>
                        <span class="badge badge-danger" style="margin-left: 10px;">Pending Review</span>
                    </div>
                    <span class="req-time">{{ $req->created_at->diffForHumans() }}</span>
                </div>
                
                <div class="inbox-body">
                    <div class="inbox-main">
                        <h3>{{ $req->title }}</h3>
                        @if ($req->description)
                            <p class="req-desc">"{{ $req->description }}"</p>
                        @endif
                        <div class="req-meta">
                            <span>🏠 Room {{ $req->stay->room->room_number }}</span>
                            <span>👤 {{ $req->stay->guest->full_name }}</span>
                            <span>🏷️ Dept: {{ $req->department }}</span>
                        </div>
                    </div>
                    
                    <div class="inbox-actions">
                        <button type="button" class="btn-primary" onclick="openAcceptModal({{ $req->id }}, '{{ addslashes($req->title) }}', '{{ $req->department }}')">
                            ✅ Accept & Route
                        </button>
                        <button type="button" class="btn-secondary" style="color: #E53935;" onclick="openRejectModal({{ $req->id }}, 'reject')">
                            ❌ Reject
                        </button>
                        <button type="button" class="btn-secondary" onclick="openRejectModal({{ $req->id }}, 'unavailable')">
                            ⚠️ Unavailable
                        </button>
                    </div>
                </div>
            </div>

            {{-- ACCEPT MODAL --}}
            <div class="modal-overlay" id="acceptModal-{{ $req->id }}">
                <div class="modal">
                    <div class="modal-header">
                        <h3>Accept & Route Request</h3>
                        <button type="button" class="modal-close" onclick="closeModal('acceptModal-{{ $req->id }}')">×</button>
                    </div>
                    <form method="POST" action="{{ route('frontdesk.request.accept', $req) }}">
                        @csrf
                        <div class="modal-body">
                            <p style="font-size: 0.85rem; color: #666; margin-bottom: 15px;">Routing: <strong id="accept-title-{{ $req->id }}"></strong></p>
                            <label class="modal-label">Send to Department:</label>
                            <select name="department" required class="modal-input">
                                <option value="Housekeeping" {{ $req->department == 'Housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                                <option value="Maintenance" {{ $req->department == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="IT" {{ $req->department == 'IT' ? 'selected' : '' }}>IT</option>
                                <option value="Front Desk" {{ $req->department == 'Front Desk' ? 'selected' : '' }}>Front Desk</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" onclick="closeModal('acceptModal-{{ $req->id }}')">Cancel</button>
                            <button type="submit" class="btn-primary">Route to Supervisor</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- REJECT / UNAVAILABLE MODAL --}}
            <div class="modal-overlay" id="rejectModal-{{ $req->id }}">
                <div class="modal">
                    <div class="modal-header">
                        <h3 id="reject-modal-title-{{ $req->id }}">Process Request</h3>
                        <button type="button" class="modal-close" onclick="closeModal('rejectModal-{{ $req->id }}')">×</button>
                    </div>
                    <form method="POST" id="reject-form-{{ $req->id }}">
                        @csrf
                        <div class="modal-body">
                            <label class="modal-label">Reason (visible to guest):</label>
                            <textarea name="reason" required rows="3" class="modal-input" placeholder="Explain why..."></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" onclick="closeModal('rejectModal-{{ $req->id }}')">Cancel</button>
                            <button type="submit" class="btn-primary" style="background: #E53935;">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 3rem; margin-bottom: 10px;">🎉</div>
                <h3 style="color: #58595B;">Inbox Zero!</h3>
                <p style="color: #999;">All guest requests have been reviewed.</p>
            </div>
        @endforelse

        <div style="margin-top: 20px;">{{ $requests->links() }}</div>
    </div>

    <style>
        .inbox-card { background: white; border: 1px solid #eee; border-left: 4px solid #E84E1B; border-radius: 10px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        .inbox-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .inbox-body { display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; flex-wrap: wrap; }
        .inbox-main { flex: 1; }
        .inbox-main h3 { color: #58595B; font-size: 1.1rem; margin-bottom: 5px; }
        .req-desc { color: #666; font-size: 0.9rem; font-style: italic; margin-bottom: 10px; }
        .req-meta { display: flex; gap: 15px; font-size: 0.75rem; color: #888; font-weight: 600; }
        .inbox-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .req-code { font-size: 0.75rem; font-weight: 700; color: #E84E1B; background: #fff5f2; padding: 3px 8px; border-radius: 5px; }
        .req-time { font-size: 0.75rem; color: #999; }
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 2000; padding: 20px; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 12px; width: 100%; max-width: 480px; overflow: hidden; }
        .modal-header { padding: 20px 25px; border-bottom: 1px solid #f4f4f4; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { font-size: 1.1rem; color: #58595B; }
        .modal-close { background: none; border: none; font-size: 1.5rem; color: #999; cursor: pointer; }
        .modal-body { padding: 25px; }
        .modal-label { display: block; font-size: 0.8rem; font-weight: 600; color: #58595B; margin-bottom: 8px; }
        .modal-input { width: 100%; padding: 12px 15px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat'; outline: none; }
        .modal-footer { padding: 15px 25px; background: #fafafa; display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f4f4f4; }
    </style>

    <script>
        function openAcceptModal(id) {
            document.getElementById('acceptModal-' + id).classList.add('active');
        }
        function openRejectModal(id, action) {
            const modal = document.getElementById('rejectModal-' + id);
            const title = document.getElementById('reject-modal-title-' + id);
            const form = document.getElementById('reject-form-' + id);
            
            if (action === 'reject') {
                title.textContent = 'Reject Request';
                form.action = "{{ url('/dashboard/frontdesk/requests') }}/" + id + "/reject";
            } else {
                title.textContent = 'Mark as Unavailable';
                form.action = "{{ url('/dashboard/frontdesk/requests') }}/" + id + "/unavailable";
            }
            modal.classList.add('active');
        }
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
    </script>
@endsection