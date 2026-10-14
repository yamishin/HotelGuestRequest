@extends('layouts.dashboard')
@section('title', $guest->full_name)
@section('page-title', 'Guest Profile')
@section('user-role', 'FRONT DESK')
@section('sidebar-menu') @include('dashboards.frontdesk._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $guest->full_name }}</h1>
            <p>Guest ID: {{ $guest->guest_code }}</p>
        </div>
        <a href="{{ route('frontdesk.guests_stays') }}" class="btn-secondary">← Back to Guests</a>
    </div>

    <div class="detail-grid">
        <div class="card">
            <h3 class="card-title">Contact Information</h3>
            <div class="detail-row"><span>Email</span><strong>{{ $guest->email ?? '—' }}</strong></div>
            <div class="detail-row"><span>Phone</span><strong>{{ $guest->phone ?? '—' }}</strong></div>
            <div class="detail-row"><span>Total Stays</span><strong>{{ $guest->stays->count() }}</strong></div>
            <div class="detail-row"><span>Reviews Left</span><strong>{{ $guest->reviews->count() }}</strong></div>
        </div>

        <div class="card">
            <h3 class="card-title">Stay History</h3>
            @forelse ($guest->stays->sortByDesc('check_in_date') as $stay)
                <div class="stay-row">
                    <div>
                        <strong>Room {{ $stay->room->room_number }}</strong>
                        <div style="font-size: 0.75rem; color: #999;">
                            {{ $stay->check_in_date->format('M d, Y') }} → {{ $stay->check_out_date->format('M d, Y') }}
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        @if ($stay->status === 'active')
                            <a href="{{ route('frontdesk.qr', $stay) }}" class="btn-primary" style="padding: 6px 12px; font-size: 0.7rem;">📱 QR Code</a>
                            <span class="badge badge-success">Active</span>
                        @elseif ($stay->status === 'upcoming')
                            <span class="badge badge-info">Upcoming</span>
                        @else
                            <span class="badge badge-grey">Completed</span>
                        @endif
                    </div>
                </div>
            @empty
                <p style="color: #999;">No stays recorded.</p>
            @endforelse
        </div>
    </div>

    {{-- ============================================================
         ACTIVE STAY QR CODE (BACKUP FOR FRONT DESK)
         Only shown if the guest has an active stay
         ============================================================ --}}
    @php
        $activeStay = $guest->stays->where('status', 'active')->first();
        $host = config('app.guest_access.host');
        $port = config('app.guest_access.port');
    @endphp

    @if ($activeStay)
        <div class="card" style="margin-top: 20px;">
            <h3 class="card-title">
                Backup QR Code — Room {{ $activeStay->room->room_number }}
                <span class="badge badge-success">Active Stay</span>
            </h3>
            <p style="font-size: 0.85rem; color: #888; margin-bottom: 20px;">
                Keep this as a backup in case the guest loses their printed QR card.
                This code will automatically become invalid once they check out.
            </p>

            <div style="display: flex; gap: 30px; align-items: center; flex-wrap: wrap;">
                <div style="background: #fafafa; padding: 20px; border-radius: 12px;">
                    <div id="backup-qr"></div>
                </div>
                <div style="flex: 1; min-width: 220px;">
                    <div class="detail-row"><span>Guest</span><strong>{{ $guest->full_name }}</strong></div>
                    <div class="detail-row"><span>Room</span><strong>{{ $activeStay->room->room_number }}</strong></div>
                    <div class="detail-row"><span>Check-out</span><strong>{{ $activeStay->check_out_date->format('M d, Y') }}</strong></div>
                    <div class="detail-row" style="border-bottom: none;">
                        <span>Actions</span>
                        <strong style="display: flex; gap: 8px;">
                            <a href="{{ route('frontdesk.qr', $activeStay) }}" class="btn-primary" style="padding: 6px 12px; font-size: 0.75rem;">🖨️ Print Full QR</a>
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
        <script>
            new QRCode(document.getElementById("backup-qr"), {
                text: "http://{{ $host }}:{{ $port }}/g/{{ $activeStay->access_token }}",
                width: 180,
                height: 180,
                colorDark: "#58595B",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        </script>
    @else
        <div class="card" style="margin-top: 20px; background: #f9f9f9;">
            <p style="font-size: 0.85rem; color: #888; text-align: center; padding: 15px;">
                🔒 No active stay — QR code backup is unavailable.
            </p>
        </div>
    @endif

    <style>
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }
        .detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f4f4f4; font-size: 0.9rem; }
        .detail-row:last-child { border-bottom: none; }
        .detail-row span { color: #888; }
        .stay-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f4f4f4; font-size: 0.9rem; }
        .stay-row:last-child { border-bottom: none; }
    </style>
@endsection