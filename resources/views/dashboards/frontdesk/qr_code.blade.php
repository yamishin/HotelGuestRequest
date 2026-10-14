@extends('layouts.dashboard')
@section('title', 'Guest QR Code')
@section('page-title', 'Guest QR Code')
@section('user-role', 'FRONT DESK')
@section('sidebar-menu') @include('dashboards.frontdesk._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Guest Access QR Code</h1>
            <p>Print or show this to the guest for their portal access</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-primary">🖨️ Print</button>
            <a href="{{ route('frontdesk.guests_stays') }}" class="btn-secondary">← Back to Guests</a>
        </div>
    </div>

    <div class="qr-card">
        <div class="qr-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 40px;">
            <h2>Welcome to Swiss-Belhotel Blulane</h2>
        </div>

        <div class="qr-info">
            <div class="info-row"><span>Guest</span><strong>{{ $stay->guest->full_name }}</strong></div>
            <div class="info-row"><span>Room</span><strong>{{ $stay->room->room_number }} · Floor {{ $stay->room->floor }}</strong></div>
            <div class="info-row"><span>Check-in</span><strong>{{ $stay->check_in_date->format('M d, Y') }}</strong></div>
            <div class="info-row"><span>Check-out</span><strong>{{ $stay->check_out_date->format('M d, Y') }}</strong></div>
        </div>

        <div class="qr-image-wrapper">
            {!! QrCode::size(260)->margin(1)->generate($accessUrl) !!}
        </div>

        <div class="qr-instructions">
            <h3>How to use this QR code</h3>
            <ol>
                <li><strong>Scan</strong> the QR code with your phone's camera</li>
                <li><strong>Tap</strong> the link that appears</li>
                <li><strong>Access</strong> your guest portal to request services and view hotel info</li>
            </ol>
        </div>

        <div class="qr-footer">
            <p style="font-size: 0.75rem; color: #999;">
                This link is valid only during your stay · {{ $accessUrl }}
            </p>
        </div>
    </div>

    <style>
        .qr-card { background: white; max-width: 550px; margin: 0 auto; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); text-align: center; }
        .qr-header { padding-bottom: 20px; border-bottom: 2px solid #f4f4f4; margin-bottom: 25px; }
        .qr-header h2 { color: #58595B; font-size: 1.2rem; margin-top: 10px; }
        .qr-info { text-align: left; margin-bottom: 25px; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f4f4f4; font-size: 0.9rem; }
        .info-row:last-child { border-bottom: none; }
        .info-row span { color: #888; }
        .qr-image-wrapper { padding: 20px; background: #fafafa; border-radius: 12px; display: inline-block; margin: 15px 0; }
        .qr-instructions { text-align: left; background: #fff9f6; border-left: 4px solid #E84E1B; padding: 20px; border-radius: 8px; margin-top: 25px; }
        .qr-instructions h3 { font-size: 0.95rem; color: #58595B; margin-bottom: 12px; }
        .qr-instructions ol { padding-left: 20px; font-size: 0.85rem; color: #666; line-height: 1.8; }
        .qr-footer { margin-top: 20px; padding-top: 15px; border-top: 1px solid #f4f4f4; }
        
        @media print {
            .sidebar, .topbar, .page-header, nav, .btn-primary, .btn-secondary, .flash { display: none !important; }
            .content-area { padding: 0 !important; }
            body { background: white; }
            .qr-card { box-shadow: none; }
        }
    </style>
@endsection