@extends('layouts.guest')
@section('title', 'Notifications')

@section('content')
    <div class="card">
        <h2 class="card-title">Notifications</h2>

        @forelse ($notifications as $notif)
            <div style="display: flex; gap: 12px; padding: 15px 0; border-bottom: 1px solid #f4f4f4;">
                <div style="font-size: 1.4rem; flex-shrink: 0;">
                    {{ match($notif->type) {
                        'request'   => '🛎️',
                        'check_in'  => '👤',
                        'check_out' => '🚪',
                        'review'    => '⭐',
                        'payment'   => '💰',
                        default     => '🔔',
                    } }}
                </div>
                <div style="flex: 1;">
                    <p style="font-size: 0.9rem; color: #58595B; font-weight: 600;">{{ $notif->title }}</p>
                    <p style="font-size: 0.85rem; color: #666; margin-top: 3px;">{{ $notif->message }}</p>
                    <span style="font-size: 0.7rem; color: #999;">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <p style="color: #999;">No notifications yet.</p>
        @endforelse

        <div style="margin-top: 20px;">{{ $notifications->links() }}</div>
    </div>
@endsection