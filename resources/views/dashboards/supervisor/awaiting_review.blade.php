@extends('layouts.dashboard')
@section('title', 'Awaiting Review')
@section('page-title', 'Awaiting Review')
@section('user-role', 'SUPERVISOR')
@section('sidebar-menu') @include('dashboards.supervisor._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Awaiting Review</h1>
            <p>Staff marked these tasks as completed. Review and confirm resolution, or send back to staff.</p>
        </div>
    </div>

    <div class="card">
        @forelse ($requests as $req)
            <div class="review-card">
                <div class="review-header">
                    <div>
                        <span class="req-code">{{ $req->request_code }}</span>
                        <span class="badge badge-orange" style="margin-left: 10px;">Awaiting Your Review</span>
                    </div>
                    <span class="req-time">Completed {{ $req->completed_at->diffForHumans() }}</span>
                </div>

                <div class="review-body">
                    <div class="review-info">
                        <h3>{{ $req->title }}</h3>
                        <div class="req-meta">
                            <span>🏠 Room {{ $req->stay->room->room_number }}</span>
                            <span>👤 {{ $req->stay->guest->full_name }}</span>
                            <span>👷 Handled by: <strong>{{ $req->assignee->name ?? 'Unknown' }}</strong></span>
                        </div>
                        @if ($req->staff_notes)
                            <div class="staff-notes-box">
                                <strong>Staff Notes:</strong> "{{ $req->staff_notes }}"
                            </div>
                        @endif
                    </div>

                    <div class="review-actions">
                        <form method="POST" action="{{ route('supervisor.request.confirm', $req) }}">
                            @csrf
                            <button type="submit" class="btn-primary" style="background: #4CAF50; width: 100%; justify-content: center;">
                                ✅ Confirm Resolved
                            </button>
                        </form>
                        
                        <button type="button" class="btn-secondary" style="color: #E53935; width: 100%; justify-content: center;" onclick="openUnresolvedModal({{ $req->id }})">
                            ⚠️ Not Resolved (Return)
                        </button>
                    </div>
                </div>
            </div>

            {{-- UNRESOLVED MODAL --}}
            <div class="modal-overlay" id="unresolvedModal-{{ $req->id }}">
                <div class="modal">
                    <div class="modal-header">
                        <h3>Send Back to Staff</h3>
                        <button type="button" class="modal-close" onclick="closeModal('unresolvedModal-{{ $req->id }}')">×</button>
                    </div>
                    <form method="POST" action="{{ route('supervisor.request.unresolved', $req) }}">
                        @csrf
                        <div class="modal-body">
                            <p style="font-size: 0.85rem; color: #666; margin-bottom: 15px;">
                                This will return the task to <strong>{{ $req->assignee->name }}</strong> and notify them immediately.
                            </p>
                            <label class="modal-label">Reason for return:</label>
                            <textarea name="reason" required rows="3" class="modal-input" placeholder="E.g., Guest reported issue still persists..."></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" onclick="closeModal('unresolvedModal-{{ $req->id }}')">Cancel</button>
                            <button type="submit" class="btn-primary" style="background: #E53935;">Send Back to Staff</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 3rem; margin-bottom: 10px;">🎉</div>
                <h3 style="color: #58595B;">All Reviewed!</h3>
                <p style="color: #999;">No tasks are waiting for your review.</p>
            </div>
        @endforelse

        <div style="margin-top: 20px;">{{ $requests->links() }}</div>
    </div>

    <style>
        .review-card { background: white; border: 1px solid #eee; border-left: 4px solid #FFC107; border-radius: 10px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        .review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .review-body { display: flex; justify-content: space-between; align-items: stretch; gap: 20px; flex-wrap: wrap; }
        .review-info { flex: 1; }
        .review-info h3 { color: #58595B; font-size: 1.1rem; margin-bottom: 5px; }
        .req-meta { display: flex; gap: 15px; font-size: 0.75rem; color: #888; font-weight: 600; margin-bottom: 15px; }
        .staff-notes-box { background: #fafafa; padding: 12px 15px; border-radius: 8px; font-size: 0.85rem; color: #58595B; border-left: 3px solid #E84E1B; }
        .review-actions { display: flex; flex-direction: column; gap: 10px; width: 200px; justify-content: center; }
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
        function openUnresolvedModal(id) { document.getElementById('unresolvedModal-' + id).classList.add('active'); }
        function closeModal(modalId) { document.getElementById(modalId).classList.remove('active'); }
    </script>
@endsection