@extends('layouts.guest')
@section('title', 'My Requests')

@section('content')
    <div class="card">
        <h2 class="card-title">My Requests</h2>

        @forelse ($requests as $req)
            <a href="{{ route('guest.request.detail', $req) }}" style="text-decoration: none; color: inherit; display: block;">
                <div class="request-row">
                    <div style="flex: 1;">
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
                </div>
            </a>
        @empty
            <p style="color: #999;">No requests yet. <a href="{{ route('guest.request.form') }}" style="color: #E84E1B;">Make your first request →</a></p>
        @endforelse

        <div style="margin-top: 20px;">{{ $requests->links() }}</div>
    </div>

    <style>
        .request-row {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding: 15px 15px; margin: 0 -15px; border-bottom: 1px solid #f4f4f4;
            transition: background 0.2s; border-radius: 8px;
        }
        .request-row:hover { background: #fff9f6; }
    </style>
@endsection