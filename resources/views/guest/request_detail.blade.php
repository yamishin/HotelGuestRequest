@extends('layouts.guest')
@section('title', 'Request Details')

@section('content')
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
            <div>
                <h1 style="font-size: 1.4rem; color: #58595B; margin-bottom: 5px;">{{ $serviceRequest->title }}</h1>
                <p style="font-size: 0.8rem; color: #999;">{{ $serviceRequest->request_code }}</p>
            </div>
            <span class="badge badge-{{
                $serviceRequest->status === 'completed' ? 'success' :
                ($serviceRequest->status === 'pending' ? 'danger' : 'warning')
            }}" style="font-size: 0.8rem; padding: 8px 16px;">
                {{ $serviceRequest->status_label }}
            </span>
        </div>

        <div class="detail-row"><span>Department</span><strong>{{ $serviceRequest->department }}</strong></div>
        <div class="detail-row"><span>Submitted</span><strong>{{ $serviceRequest->created_at->format('M d, Y · g:i A') }}</strong></div>
        @if ($serviceRequest->completed_at)
            <div class="detail-row"><span>Completed</span><strong>{{ $serviceRequest->completed_at->format('M d, Y · g:i A') }}</strong></div>
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
                    'completed'   => ['Completed', 'Your request has been fulfilled. Thank you!'],
                ];
                $statusOrder = array_keys($steps);
                $currentIndex = array_search($serviceRequest->status, $statusOrder);
                if ($currentIndex === false) $currentIndex = -1; // cancelled case
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

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('guest.my_requests') }}" class="btn-secondary">← Back to My Requests</a>
    </div>

    <style>
        .detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f4f4f4; font-size: 0.9rem; }
        .detail-row span { color: #888; }
        .detail-block { padding: 15px 0; }

        .timeline { list-style: none; padding: 0; }
        .timeline-item {
            display: flex; gap: 15px; padding: 15px 0; position: relative;
        }
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
    </style>
@endsection