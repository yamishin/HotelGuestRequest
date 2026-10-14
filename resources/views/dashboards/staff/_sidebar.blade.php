<li>
    <a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
        Dashboard
    </a>
</li>
<li>
    <a href="{{ route('staff.tasks') }}" class="{{ request()->routeIs('staff.tasks') ? 'active' : '' }}">
        My Tasks
    </a>
</li>
<li>
    <a href="{{ route('staff.all_requests') }}" class="{{ request()->routeIs('staff.all_requests') ? 'active' : '' }}">
        Department Requests
    </a>
</li>
<li>
    <a href="{{ route('staff.notifications') }}" class="{{ request()->routeIs('staff.notifications') ? 'active' : '' }}">
        Notifications
    </a>
</li>
<li>
    <a href="{{ route('staff.history') }}" class="{{ request()->routeIs('staff.history') ? 'active' : '' }}">
        History
    </a>
</li>
<li>
    <a href="{{ route('staff.profile') }}" class="{{ request()->routeIs('staff.profile') ? 'active' : '' }}">
        Profile
    </a>
</li>