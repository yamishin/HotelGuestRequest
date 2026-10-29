@extends('layouts.guest')
@section('title', 'Request Details')

@section('content')
    {{-- REQUEST HEADER --}}
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
            <div>
                <h1 style="font-size: 1.4rem; color: #58595B; margin-bottom: 5px;">{{ $serviceRequest->title }}</h1>
                <p style="font-size: 0.8rem; color: #999;">{{ $serviceRequest->request_code }}</p>
            </div>
            <span class="badge badge-{{ $serviceRequest->status_color }}" style="font-size: 0.8rem; padding: 8px 16px;">
                {{ $serviceRequest->status_label }}
            </span>
        </div>

        <div class="detail-row"><span>Department</span><strong>{{ $serviceRequest->department }}</strong></div>
        <div class="detail-row"><span>Submitted</span><strong>{{ $serviceRequest->created_at->format('M d, Y · g:i A') }}</strong></div>
        @if ($serviceRequest->completed_at)
            <div class="detail-row"><span>Completed</span><strong>{{ $serviceRequest->completed_at->format('M d, Y · g:i A') }}</strong></div>
        @endif
        @if ($serviceRequest->guest_confirmed_at)
            <div class="detail-row"><span>Confirmed</span><strong>{{ $serviceRequest->guest_confirmed_at->format('M d, Y · g:i A') }}</strong></div>
        @endif
        @if ($serviceRequest->description)
            <div class="detail-block">
                <span style="font-size: 0.8rem; color: #888;">Description</span>
                <p style="margin-top: 8px; font-size: 0.9rem; color: #58595B; line-height: 1.6;">{{ $serviceRequest->description }}</p>
            </div>
        @endif
    </div>

    {{-- STATUS TIMELINE --}}
    <div class="card">
        <h3 class="card-title">Progress</h3>
        <ul class="timeline">
            @php
                $steps = [
                    'pending'     => ['Request Received', 'We received your request and it is in the queue.'],
                    'assigned'    => ['Assigned', 'A staff member has been assigned to your request.'],
                    'in_progress' => ['In Progress', 'Someone is working on your request right now.'],
                    'completed'   => ['Awaiting Your Confirmation', 'Staff marked this done. Please confirm it\'s resolved.'],
                    'verified'    => ['Verified', 'You confirmed this was resolved. Thank you!'],
                ];
                $statusOrder = array_keys($steps);
                $currentIndex = array_search($serviceRequest->status, $statusOrder);
                if ($currentIndex === false) $currentIndex = -1;
            @endphp

            @foreach ($steps as $key => $step)
                @php
                    $stepIndex = array_search($key, $statusOrder);
                    $done = $stepIndex <= $currentIndex;
                    $isCurrent = $stepIndex === $currentIndex;
                @endphp
                <li class="timeline-item {{ $done ? 'done' : '' }} {{ $isCurrent ? 'current' : '' }}">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <strong>{{ $step[0] }}</strong>
                        <p>{{ $step[1] }}</p>
                    </div>
                </li>
            @endforeach

            @if ($serviceRequest->status === 'cancelled')
                <li class="timeline-item cancelled">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <strong>Cancelled</strong>
                        <p>This request was cancelled. Please contact the front desk if this was a mistake.</p>
                    </div>
                </li>
            @endif
        </ul>
    </div>

    {{-- STAFF NOTES --}}
    @if ($serviceRequest->staff_notes)
        <div class="card">
            <h3 class="card-title">📝 Staff Notes</h3>
            <p style="font-size: 0.9rem; color: #58595B; line-height: 1.7; background: #fafafa; padding: 15px; border-radius: 8px; border-left: 3px solid #E84E1B;">
                {{ $serviceRequest->staff_notes }}
            </p>
        </div>
    @endif

    {{-- AWAITING CONFIRMATION BLOCK --}}
    @if ($serviceRequest->status === 'completed')
        <div class="confirm-card">
            <div class="confirm-icon">🤔</div>
            <h3 class="confirm-title">Is this service completed?</h3>
            <p class="confirm-desc">
                Our staff marked <strong>"{{ $serviceRequest->title }}"</strong> as done.
                Please confirm if it has been resolved to your satisfaction.
            </p>

            <div class="confirm-actions">
                <form method="POST" action="{{ route('guest.request.confirm', $serviceRequest) }}" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn-confirm-yes">✅ Yes, it's resolved</button>
                </form>

                <button type="button" class="btn-confirm-no"
                    onclick="document.getElementById('rejectForm').style.display='block'; this.style.display='none';">
                    ⚠️ Not resolved
                </button>
            </div>

            <div id="rejectForm" style="display: none; margin-top: 20px;">
                <form method="POST" action="{{ route('guest.request.reject', $serviceRequest) }}">
                    @csrf
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #58595B; margin-bottom: 8px;">
                        What's still wrong? (optional)
                    </label>
                    <textarea name="reason" rows="3" placeholder="Tell us what's still not working..."
                        style="width: 100%; padding: 12px 15px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat', sans-serif; font-size: 0.85rem; outline: none; resize: vertical;">{{ old('reason') }}</textarea>
                    <div style="display: flex; gap: 10px; margin-top: 12px;">
                        <button type="submit" class="btn-primary" style="background: #E53935;">Report Issue</button>
                        <button type="button" class="btn-secondary"
                            onclick="document.getElementById('rejectForm').style.display='none'; document.querySelector('.btn-confirm-no').style.display='inline-block';">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @elseif ($serviceRequest->status === 'verified')
        <div class="verified-card">
            <div class="verified-icon">✅</div>
            <div>
                <div class="verified-title">Confirmed Resolved</div>
                <div class="verified-desc">You confirmed this on {{ $serviceRequest->guest_confirmed_at->format('M d, Y · g:i A') }}. Thank you!</div>
            </div>
        </div>
    @endif

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('guest.my_requests') }}" class="btn-secondary">← Back to My Requests</a>
    </div>

    <style>
        .detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f4f4f4; font-size: 0.9rem; }
        .detail-row:last-child { border-bottom: none; }
        .detail-row span { color: #888; }
        .detail-block { padding: 15px 0; }

        .timeline { list-style: none; padding: 0; }
        .timeline-item { display: flex; gap: 15px; padding: 15px 0; position: relative; }
        .timeline-item:not(:last-child)::before {
            content: ''; position: absolute; left: 11px; top: 38px; bottom: -8px;
            width: 2px; background: #eee;
        }
        .timeline-item.done:not(:last-child)::before { background: #E84E1B; }
        .timeline-dot {
            width: 24px; height: 24px; border-radius: 50%;
            background: #f4f4f4; border: 2px solid #eee;
            flex-shrink: 0; position: relative; z-index: 1;
        }
        .timeline-item.done .timeline-dot { background: #E84E1B; border-color: #E84E1B; }
        .timeline-item.current .timeline-dot {
            background: white; border-color: #E84E1B;
            box-shadow: 0 0 0 4px rgba(232, 78, 27, 0.15);
        }
        .timeline-item.current .timeline-dot::after {
            content: ''; position: absolute; inset: 4px;
            background: #E84E1B; border-radius: 50%;
        }
        .timeline-item.cancelled .timeline-dot { background: #E53935; border-color: #E53935; }
        .timeline-content strong { color: #58595B; font-size: 0.9rem; }
        .timeline-content p { font-size: 0.8rem; color: #888; margin-top: 3px; line-height: 1.5; }
        .timeline-item:not(.done):not(.current) .timeline-content strong { color: #ccc; }
        .timeline-item:not(.done):not(.current) .timeline-content p { color: #ccc; }

        .confirm-card {
            background: white; border-radius: 12px; padding: 30px;
            text-align: center; box-shadow: 0 4px 20px rgba(232, 78, 27, 0.1);
            border: 2px solid #fff5f2; margin-bottom: 20px;
        }
        .confirm-icon { font-size: 3rem; margin-bottom: 15px; }
        .confirm-title { font-size: 1.3rem; color: #58595B; margin-bottom: 10px; }
        .confirm-desc {
            font-size: 0.9rem; color: #888; line-height: 1.6;
            margin-bottom: 25px; max-width: 500px; margin-left: auto; margin-right: auto;
        }
        .confirm-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn-confirm-yes {
            width: 100%; padding: 15px 25px;
            background: linear-gradient(135deg, #4CAF50, #66bb6a);
            color: white; border: none; border-radius: 50px;
            font-family: 'Montserrat', sans-serif; font-weight: 700;
            font-size: 0.95rem; cursor: pointer; transition: all 0.2s;
        }
        .btn-confirm-yes:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3); }
        .btn-confirm-no {
            padding: 15px 25px; background: #f4f4f4; color: #E53935;
            border: 1px solid #ffebee; border-radius: 50px;
            font-family: 'Montserrat', sans-serif; font-weight: 700;
            font-size: 0.95rem; cursor: pointer; transition: all 0.2s;
        }
        .btn-confirm-no:hover { background: #ffebee; transform: translateY(-2px); }
        .verified-card {
            background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
            border-radius: 12px; padding: 20px 25px;
            display: flex; gap: 15px; align-items: center;
            margin-bottom: 20px; border-left: 4px solid #4CAF50;
        }
        .verified-icon { font-size: 2rem; }
        .verified-title { font-size: 1rem; font-weight: 700; color: #2e7d32; }
        .verified-desc { font-size: 0.85rem; color: #666; margin-top: 4px; }
    </style>
@endsection