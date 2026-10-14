<li><a href="{{ route('frontdesk.dashboard') }}" class="{{ request()->routeIs('frontdesk.dashboard') ? 'active' : '' }}">Dashboard</a></li>
<li><a href="{{ route('frontdesk.guests_stays') }}" class="{{ request()->routeIs('frontdesk.guests_stays') ? 'active' : '' }}">Guests & Stays</a></li>
<li><a href="{{ route('frontdesk.rooms') }}" class="{{ request()->routeIs('frontdesk.rooms') ? 'active' : '' }}">Rooms</a></li>
<li><a href="{{ route('frontdesk.notifications') }}" class="{{ request()->routeIs('frontdesk.notifications') ? 'active' : '' }}">Notifications</a></li>
<li><a href="{{ route('frontdesk.history') }}" class="{{ request()->routeIs('frontdesk.history') ? 'active' : '' }}">History</a></li>
<li><a href="{{ route('frontdesk.reports') }}" class="{{ request()->routeIs('frontdesk.reports') ? 'active' : '' }}">Reports</a></li>
<li><a href="{{ route('frontdesk.profile') }}" class="{{ request()->routeIs('frontdesk.profile') ? 'active' : '' }}">Profile</a></li>