@extends('layouts.dashboard')
@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('user-role', 'STAFF')
@section('sidebar-menu') @include('dashboards.staff._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Notifications</h1>
            <p>Updates on your tasks and assignments</p>
        </div>
        <form method="POST" action="{{ route('staff.notifications.readAll') }}">
            @csrf @method('PATCH')
            <button class="btn-secondary" type="submit">Mark All as Read</button>
        </form>
    </div>

    <div class="card">
        <ul class="notif-list">
            @forelse ($notifications as $notif)
                <li class="{{ $notif->is_read ? '' : 'unread' }}">
                    <div class="notif-icon {{ $notif->is_read ? 'grey' : 'orange' }}">
                        {{ match($notif->type) {
                            'request'   => '🛎️',
                            'check_in'  => '👤',
                            'check_out' => '🚪',
                            'review'    => '⭐',
                            default     => '🔔',
                        } }}
                    </div>
                    <div class="notif-body">
                        <p><strong>{{ $notif->title }}</strong> — {{ $notif->message }}</p>
                        <span>{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                    @if (!$notif->is_read)
                        <form method="POST" action="{{ route('staff.notifications.read', $notif) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-secondary" style="font-size: 0.7rem; padding: 6px 12px;">Mark Read</button>
                        </form>
                    @endif
                </li>
            @empty
                <li style="text-align: center; padding: 60px 20px;">
                    <div style="font-size: 3rem; margin-bottom: 10px;">🔔</div>
                    <p style="color: #999;">No notifications yet.</p>
                </li>
            @endforelse
        </ul>
        <div style="margin-top: 20px;">{{ $notifications->links() }}</div>
    </div>

    <style>
        .notif-list { list-style: none; }
        .notif-list li {
            display: flex; gap: 15px; padding: 18px 15px;
            border-bottom: 1px solid #f4f4f4; align-items: center;
        }
        .notif-list li.unread {
            background: #fff9f6; border-left: 4px solid #E84E1B;
        }
        .notif-icon {
            width: 45px; height: 45px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0;
        }
        .notif-icon.orange { background: #fff5f2; }
        .notif-icon.grey   { background: #f4f4f4; }
        .notif-body { flex: 1; }
        .notif-body p { font-size: 0.9rem; color: #58595B; margin-bottom: 4px; }
        .notif-body span { font-size: 0.75rem; color: #999; }
    </style>
@endsection