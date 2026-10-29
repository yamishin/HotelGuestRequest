@extends('layouts.dashboard')
@section('title', 'My Tasks')
@section('page-title', 'My Tasks')
@section('user-role', 'STAFF')
@section('sidebar-menu') @include('dashboards.staff._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>My Tasks</h1>
            <p>Manage and update your assigned tasks</p>
        </div>
    </div>

    {{-- TABS --}}
    <div class="task-tabs">
        <button class="task-tab active" data-tab="active">Active</button>
        <button class="task-tab" data-tab="awaiting">Awaiting Guest</button>
        <button class="task-tab" data-tab="returned">Returned</button>
    </div>

    {{-- =========================================================
         ACTIVE TASKS (assigned + in_progress, NOT returned)
         ========================================================= --}}
    <div class="tab-content active" id="tab-active">
        @php
            $activeTasks = $tasks->filter(fn($t) =>
                in_array($t->status, ['assigned', 'in_progress']) && !$t->returned_by_guest
            );
        @endphp

        @forelse ($activeTasks as $task)
            <div class="task-card">
                <div class="task-card-left">
                    <div class="task-card-header">
                        <span class="task-code">{{ $task->request_code }}</span>
                        <span class="badge badge-{{ $task->status_color }}">{{ $task->status_label }}</span>
                    </div>
                    <h3 class="task-card-title">{{ $task->title }}</h3>
                    @if ($task->description)
                        <p class="task-card-desc">{{ $task->description }}</p>
                    @endif
                    <div class="task-card-meta">
                        <span>🏠 Room {{ $task->stay->room->room_number ?? '—' }}</span>
                        <span>👤 {{ $task->stay->guest->full_name ?? 'Unknown' }}</span>
                        <span>🏷️ {{ $task->department }}</span>
                        <span>⏰ {{ $task->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="task-card-right">
                    @if ($task->status === 'assigned')
                        <form method="POST" action="{{ route('staff.tasks.updateStatus', $task) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="btn-primary">▶️ Start Task</button>
                        </form>
                    @elseif ($task->status === 'in_progress')
                        <button type="button" class="btn-primary" style="background: #4CAF50;" onclick="openCompleteModal({{ $task->id }})">
                            ✅ Mark Completed
                        </button>
                    @endif
                </div>
            </div>

            {{-- COMPLETE MODAL (Active tab) --}}
            <div class="modal-overlay" id="completeModal-{{ $task->id }}">
                <div class="modal">
                    <div class="modal-header">
                        <h3>Complete Task</h3>
                        <button type="button" class="modal-close" onclick="closeCompleteModal({{ $task->id }})">×</button>
                    </div>
                    <form method="POST" action="{{ route('staff.tasks.updateStatus', $task) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <div class="modal-body">
                            <p style="font-size: 0.9rem; color: #58595B; margin-bottom: 20px;">
                                Completing <strong>"{{ $task->title }}"</strong> in Room {{ $task->stay->room->room_number }}.
                            </p>
                            <label class="modal-label">Notes for Guest (optional)</label>
                            <textarea name="staff_notes" rows="3" placeholder="E.g., Replaced the router, tested and working..." class="modal-textarea">{{ $task->staff_notes }}</textarea>
                            <p style="font-size: 0.75rem; color: #999; margin-top: 10px;">
                                💡 The guest will see this note when confirming completion.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" onclick="closeCompleteModal({{ $task->id }})">Cancel</button>
                            <button type="submit" class="btn-primary" style="background: #4CAF50;">✅ Complete Task</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-card">
                <div class="empty-icon">🎉</div>
                <h3>All caught up!</h3>
                <p>No active tasks right now.</p>
            </div>
        @endforelse
    </div>

    {{-- =========================================================
         AWAITING GUEST CONFIRMATION (completed, not verified)
         ========================================================= --}}
    <div class="tab-content" id="tab-awaiting">
        @php
            $awaitingTasks = $tasks->filter(fn($t) =>
                $t->status === 'completed' && !$t->guest_confirmed_at
            );
        @endphp

        @forelse ($awaitingTasks as $task)
            <div class="task-card" style="border-left-color: #FFC107;">
                <div class="task-card-left">
                    <div class="task-card-header">
                        <span class="task-code">{{ $task->request_code }}</span>
                        <span class="badge badge-warning">⏳ Awaiting Guest Confirmation</span>
                    </div>
                    <h3 class="task-card-title">{{ $task->title }}</h3>
                    <div class="task-card-meta">
                        <span>🏠 Room {{ $task->stay->room->room_number ?? '—' }}</span>
                        <span>👤 {{ $task->stay->guest->full_name ?? 'Unknown' }}</span>
                        <span>✅ Completed {{ $task->completed_at->diffForHumans() }}</span>
                    </div>
                    @if ($task->staff_notes)
                        <div class="staff-note">
                            <strong>Your note:</strong> {{ $task->staff_notes }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-card">
                <div class="empty-icon">📬</div>
                <h3>Nothing waiting</h3>
                <p>Tasks awaiting guest confirmation will show here.</p>
            </div>
        @endforelse
    </div>

    {{-- =========================================================
         RETURNED BY GUEST (guest rejected → back to staff)
         ========================================================= --}}
    <div class="tab-content" id="tab-returned">
        @php
            $returnedTasks = $tasks->filter(fn($t) =>
                $t->returned_by_guest && $t->status === 'in_progress'
            );
        @endphp

        @forelse ($returnedTasks as $task)
            <div class="task-card" style="border-left-color: #E53935;">
                <div class="task-card-left">
                    <div class="task-card-header">
                        <span class="task-code">{{ $task->request_code }}</span>
                        <span class="badge badge-danger">⚠️ Returned by Guest</span>
                    </div>
                    <h3 class="task-card-title">{{ $task->title }}</h3>
                    <p class="task-card-desc">
                        The guest reported this is <strong>not resolved</strong>. Please follow up immediately.
                    </p>
                    <div class="task-card-meta">
                        <span>🏠 Room {{ $task->stay->room->room_number ?? '—' }}</span>
                        <span>👤 {{ $task->stay->guest->full_name ?? 'Unknown' }}</span>
                    </div>
                </div>
                <div class="task-card-right">
                    <button type="button" class="btn-primary" style="background: #4CAF50;" onclick="openCompleteModal({{ $task->id }})">
                        ✅ Mark Completed Again
                    </button>
                </div>
            </div>

            {{-- COMPLETE MODAL (Returned tab) --}}
            <div class="modal-overlay" id="completeModal-{{ $task->id }}">
                <div class="modal">
                    <div class="modal-header">
                        <h3>Complete Task Again</h3>
                        <button type="button" class="modal-close" onclick="closeCompleteModal({{ $task->id }})">×</button>
                    </div>
                    <form method="POST" action="{{ route('staff.tasks.updateStatus', $task) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <div class="modal-body">
                            <p style="font-size: 0.9rem; color: #58595B; margin-bottom: 15px;">
                                Completing <strong>"{{ $task->title }}"</strong> in Room {{ $task->stay->room->room_number }}.
                            </p>
                            <div style="background: #ffebee; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem; color: #c62828;">
                                ⚠️ The guest reported this was not resolved. Make sure the issue is fixed before completing again.
                            </div>
                            <label class="modal-label">Notes for Guest (optional)</label>
                            <textarea name="staff_notes" rows="3" placeholder="E.g., This time I replaced the entire modem..." class="modal-textarea">{{ $task->staff_notes }}</textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" onclick="closeCompleteModal({{ $task->id }})">Cancel</button>
                            <button type="submit" class="btn-primary" style="background: #4CAF50;">✅ Complete Task</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-card">
                <div class="empty-icon">✓</div>
                <h3>No returned tasks</h3>
                <p>Great job — no complaints from guests!</p>
            </div>
        @endforelse
    </div>

    <style>
        .task-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
            background: white;
            padding: 6px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .task-tab {
            flex: 1;
            padding: 12px 20px;
            border: none;
            background: transparent;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            color: #888;
            cursor: pointer;
            transition: all 0.2s;
        }
        .task-tab:hover { background: #fafafa; }
        .task-tab.active {
            background: var(--primary-orange);
            color: white;
        }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .task-card {
            background: white;
            border-radius: 10px;
            padding: 22px 25px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            gap: 20px;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid #E84E1B;
        }
        .task-card-left { flex: 1; min-width: 0; }
        .task-card-right { flex-shrink: 0; }
        .task-card-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
        .task-code {
            font-size: 0.7rem;
            font-weight: 600;
            color: #E84E1B;
            background: #fff5f2;
            padding: 3px 8px;
            border-radius: 5px;
        }
        .task-card-title { font-size: 1.05rem; color: #58595B; margin-bottom: 6px; }
        .task-card-desc { font-size: 0.85rem; color: #888; margin-bottom: 10px; line-height: 1.5; }
        .task-card-meta { display: flex; gap: 15px; flex-wrap: wrap; font-size: 0.75rem; color: #999; }
        .staff-note {
            margin-top: 12px;
            font-size: 0.8rem;
            color: #666;
            background: #fff9f6;
            padding: 10px 15px;
            border-radius: 6px;
            border-left: 3px solid #E84E1B;
        }

        .empty-card {
            background: white;
            border-radius: 10px;
            padding: 60px 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .empty-icon { font-size: 3rem; margin-bottom: 10px; }
        .empty-card h3 { color: #58595B; margin-bottom: 6px; }
        .empty-card p { color: #999; font-size: 0.9rem; }

        @media (max-width: 700px) {
            .task-tabs { flex-direction: column; }
            .task-card { flex-direction: column; align-items: stretch; }
            .task-card-right { margin-top: 15px; }
        }

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
        }
        .modal-body { padding: 25px; }
        .modal-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #58595B;
            margin-bottom: 8px;
        }
        .modal-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            outline: none;
            resize: vertical;
        }
        .modal-textarea:focus { border-color: #E84E1B; }
        .modal-footer {
            padding: 15px 25px;
            background: #fafafa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #f4f4f4;
        }
    </style>

    <script>
        document.querySelectorAll('.task-tab').forEach(tab => {
            tab.addEventListener('click', function () {
                const tabName = this.dataset.tab;
                document.querySelectorAll('.task-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('tab-' + tabName).classList.add('active');
            });
        });

        function openCompleteModal(id) {
            document.getElementById('completeModal-' + id).classList.add('active');
        }
        function closeCompleteModal(id) {
            document.getElementById('completeModal-' + id).classList.remove('active');
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
            }
        });
    </script>
@endsection