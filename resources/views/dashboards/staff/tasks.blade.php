@extends('layouts.dashboard')
@section('title', 'My Tasks')
@section('page-title', 'My Tasks')
@section('user-role', 'STAFF')
@section('sidebar-menu') @include('dashboards.staff._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>My Tasks</h1>
            <p>Update task status as you work through your assignments</p>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="card">
        <form method="GET" class="filters-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, code, or room...">

            <select name="status">
                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Active</option>
                <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>

            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('staff.tasks') }}" class="btn-secondary">Reset</a>
        </form>
    </div>

    {{-- TASK LIST --}}
    @forelse ($tasks as $task)
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
                    <form method="POST" action="{{ route('staff.tasks.updateStatus', $task) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn-primary" style="background: #4CAF50;" onclick="return confirm('Mark this task as completed?')">
                            ✅ Mark Completed
                        </button>
                    </form>
                @elseif ($task->status === 'completed')
                    <span class="badge badge-success">✅ Done</span>
                @endif
            </div>
        </div>
    @empty
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">🎉</div>
                <h3>All caught up!</h3>
                <p>You have no active tasks right now.</p>
            </div>
        </div>
    @endforelse

    <div style="margin-top: 20px;">{{ $tasks->links() }}</div>

    <style>
        .filters-bar {
            display: grid;
            grid-template-columns: 2fr 1fr auto auto;
            gap: 10px;
            align-items: center;
        }
        @media (max-width: 700px) { .filters-bar { grid-template-columns: 1fr 1fr; } }
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
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        .task-card-left { flex: 1; min-width: 0; }

        .task-card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }
        .task-code {
            font-size: 0.7rem;
            font-weight: 600;
            color: #E84E1B;
            background: #fff5f2;
            padding: 3px 8px;
            border-radius: 5px;
        }

        .task-card-title {
            font-size: 1.05rem;
            color: #58595B;
            margin-bottom: 6px;
        }
        .task-card-desc {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        .task-card-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            font-size: 0.75rem;
            color: #999;
        }
        .task-card-right { flex-shrink: 0; }

        @media (max-width: 700px) {
            .task-card { flex-direction: column; align-items: stretch; }
            .task-card-right { margin-top: 15px; }
        }

        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-icon { font-size: 3rem; margin-bottom: 15px; }
        .empty-state h3 { color: #58595B; margin-bottom: 6px; }
        .empty-state p { color: #999; font-size: 0.9rem; }
    </style>
@endsection