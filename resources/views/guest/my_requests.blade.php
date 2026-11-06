@extends('layouts.guest')
@section('title', 'My Requests')

@section('content')
    <div class="card">
        <h2 class="card-title">My Requests</h2>

        @forelse ($requests as $req)
            <div class="request-row">
                {{-- CLICKABLE AREA FOR VIEWING DETAILS --}}
                <a href="{{ route('guest.request.detail', $req) }}" style="text-decoration: none; color: inherit; display: flex; justify-content: space-between; flex: 1;">
                    <div style="flex: 1; padding-right: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                            <strong style="color: #58595B; font-size: 0.95rem;">{{ $req->title }}</strong>
                            <span style="font-size: 0.7rem; color: #999;">{{ $req->request_code }}</span>
                        </div>
                        <p style="font-size: 0.8rem; color: #888;">{{ $req->department }}</p>
                        @if ($req->description)
                            <p style="font-size: 0.8rem; color: #999; margin-top: 5px;">{{ $req->description }}</p>
                        @endif
                        <span style="font-size: 0.7rem; color: #999;">Submitted {{ $req->created_at->diffForHumans() }}</span>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge badge-{{ $req->status_color }}">
                            {{ $req->status_label }}
                        </span>
                        <div style="font-size: 0.7rem; color: #E84E1B; margin-top: 8px; font-weight: 600;">View →</div>
                    </div>
                </a>

                {{-- CANCEL BUTTON (Outside the <a> tag) --}}
                @if(in_array($req->status, ['pending', 'routed']))
                    <div style="display: flex; align-items: center; padding-left: 15px; border-left: 1px solid #eee;">
                        <form method="POST" action="{{ route('guest.request.cancel', $req) }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-secondary" style="padding: 6px 12px; font-size: 0.7rem; color: #E53935; border: 1px solid #ffebee;" onclick="return confirm('Are you sure you want to cancel this request?')">
                                <i class="fa-solid fa-xmark"></i> Cancel
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <p style="color: #999;">No requests yet. <a href="{{ route('guest.request.form') }}" style="color: #E84E1B;">Make your first request →</a></p>
        @endforelse

        <div style="margin-top: 20px;">{{ $requests->links() }}</div>
    </div>

    <style>
        .request-row {
            display: flex; 
            align-items: center; /* Center items vertically */
            padding: 15px 15px; 
            margin: 0 -15px; 
            border-bottom: 1px solid #f4f4f4;
            transition: background 0.2s; 
            border-radius: 8px;
        }
        .request-row:hover { background: #fff9f6; }
        
        /* Mobile responsiveness for the cancel button */
        @media (max-width: 600px) {
            .request-row { flex-direction: column; align-items: flex-start; }
            .request-row > div:last-child { padding-left: 0; padding-top: 15px; border-left: none; border-top: 1px solid #eee; width: 100%; }
            .request-row > a { width: 100%; }
        }
    </style>
@endsection