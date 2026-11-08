@php
$isFrontDesk = auth()->user()->department === 'Front Desk';
@endphp

<li><a href="{{ route('supervisor.dashboard') }}" class="{{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>

{{-- FRONT DESK ONLY LINKS --}}
@if($isFrontDesk)
<li><a href="{{ route('supervisor.check_in.form') }}" class="{{ request()->routeIs('supervisor.check_in.*') ? 'active' : '' }}"><i class="fa-solid fa-user-plus"></i> Check-in Guest</a></li>
<li><a href="{{ route('supervisor.guests_stays') }}" class="{{ request()->routeIs('supervisor.guests_stays') || request()->routeIs('supervisor.guest_detail') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Guests & Stays</a></li>
<li><a href="{{ route('supervisor.rooms') }}" class="{{ request()->routeIs('supervisor.rooms') ? 'active' : '' }}"><i class="fa-solid fa-bed"></i> Room Status</a></li>
@endif

<li><a href="{{ route('supervisor.queue') }}" class="{{ request()->routeIs('supervisor.queue') ? 'active' : '' }}"><i class="fa-solid fa-inbox"></i> {{ $isFrontDesk ? 'Request Inbox' : 'Request Queue' }}</a></li>
<li><a href="{{ route('supervisor.awaiting_review') }}" class="{{ request()->routeIs('supervisor.awaiting_review') ? 'active' : '' }}"><i class="fa-solid fa-clipboard-check"></i> Awaiting Review</a></li>
<li><a href="{{ route('supervisor.staff') }}" class="{{ request()->routeIs('supervisor.staff*') ? 'active' : '' }}"><i class="fa-solid fa-user-group"></i> My Staff</a></li>
<li><a href="{{ route('supervisor.history') }}" class="{{ request()->routeIs('supervisor.history') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
<li><a href="{{ route('supervisor.reports') }}" class="{{ request()->routeIs('supervisor.reports') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> Reports</a></li>
<li><a href="{{ route('supervisor.notifications') }}" class="{{ request()->routeIs('supervisor.notifications') ? 'active' : '' }}"><i class="fa-solid fa-bell"></i> Notifications</a></li>
<li><a href="{{ route('supervisor.profile') }}" class="{{ request()->routeIs('supervisor.profile') ? 'active' : '' }}"><i class="fa-solid fa-user"></i> Profile</a></li>