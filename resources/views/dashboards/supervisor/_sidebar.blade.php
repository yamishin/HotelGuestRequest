<li>
    <a href="{{ route('supervisor.dashboard') }}" class="{{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
        Dashboard
    </a>
</li>
<li>
    <a href="{{ route('supervisor.queue') }}" class="{{ request()->routeIs('supervisor.queue') ? 'active' : '' }}">
        Request Queue
    </a>
</li>
<li>
    <a href="{{ route('supervisor.staff') }}" class="{{ request()->routeIs('supervisor.staff') ? 'active' : '' }}">
        Staff
    </a>
</li>
<li>
    <a href="{{ route('supervisor.history') }}" class="{{ request()->routeIs('supervisor.history') ? 'active' : '' }}">
        History
    </a>
</li>
<li>
    <a href="{{ route('supervisor.notifications') }}" class="{{ request()->routeIs('supervisor.notifications') ? 'active' : '' }}">
        Notifications
    </a>
</li>
<li>
    <a href="{{ route('supervisor.profile') }}" class="{{ request()->routeIs('supervisor.profile') ? 'active' : '' }}">
        Profile
    </a>
</li>