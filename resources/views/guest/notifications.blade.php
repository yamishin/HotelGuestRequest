@extends('layouts.guest')
@section('title', 'Notifications')

@section('content')
    <div class="card">
        <h2 class="card-title">Notifications</h2>

        @forelse ($notifications as $notif)
            <div style="display: flex; gap: 12px; padding: 15px 0; border-bottom: 1px solid #f4f4f4;">
                <div style="font-size: 1.2rem; flex-shrink: 0; color: var(--primary-orange); margin-top: 2px;">
                    @if($notif->type == 'request')
                        <i class="fa-solid fa-bell-concierge"></i>
                    @elseif($notif->type == 'check_in')
                        <i class="fa-solid fa-user-plus"></i>
                    @elseif($notif->type == 'check_out')
                        <i class="fa-solid fa-door-open"></i>
                    @elseif($notif->type == 'review')
                        <i class="fa-solid fa-star"></i>
                    @elseif($notif->type == 'payment')
                        <i class="fa-solid fa-money-bill-wave"></i>
                    @else
                        <i class="fa-solid fa-bell"></i>
                    @endif
                </div>
                <div style="flex: 1;">
                    <p style="font-size: 0.9rem; color: #58595B; font-weight: 600;">{{ $notif->title }}</p>
                    <p style="font-size: 0.85rem; color: #666; margin-top: 3px;">{{ $notif->message }}</p>
                    <span style="font-size: 0.7rem; color: #999;">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 40px 20px;">
                <i class="fa-solid fa-bell-slash" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                <p style="color: #999;">No notifications yet.</p>
            </div>
        @endforelse

        <div style="margin-top: 20px;">{{ $notifications->links() }}</div>
    </div>
@endsection