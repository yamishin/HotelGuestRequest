<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Swiss-Belhotel Blulane</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background-color: #f8f9fa; min-height: 100vh; }
        :root { --primary-orange: #E84E1B; --hover-orange: #c43d12; --text-grey: #58595B; }

        /* TOP BAR */
        .guest-topbar { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .guest-topbar img { height: 45px; }
        .guest-info { text-align: right; }
        .guest-info p { font-size: 0.85rem; font-weight: 600; color: var(--text-grey); }
        .guest-info span { font-size: 0.75rem; color: #999; }
        .logout-btn { background: #f4f4f4; color: #58595B; padding: 8px 15px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; text-decoration: none; margin-left: 15px; }

        /* NAVIGATION TABS */
        .guest-nav { background: white; padding: 0 30px; display: flex; gap: 5px; overflow-x: auto; border-bottom: 1px solid #eee; }
        .guest-nav a { padding: 15px 20px; font-size: 0.85rem; font-weight: 600; color: #888; text-decoration: none; border-bottom: 3px solid transparent; transition: all 0.3s; white-space: nowrap; }
        .guest-nav a:hover { color: var(--primary-orange); }
        .guest-nav a.active { color: var(--primary-orange); border-bottom-color: var(--primary-orange); }

        /* MAIN */
        .guest-main { max-width: 1000px; margin: 0 auto; padding: 30px 20px 60px; }

        /* FLASH */
        .flash { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; }
        .flash-success { background: #e8f5e9; color: #2e7d32; border-left: 4px solid #4CAF50; }
        .flash-error { background: #ffebee; color: #c62828; border-left: 4px solid #E53935; }

        /* CARDS */
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .card-title { font-size: 1.05rem; color: #58595B; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 2px solid #f4f4f4; }

        /* BUTTONS */
        .btn-primary { background: var(--primary-orange); color: white; padding: 12px 22px; border: none; border-radius: 50px; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: background 0.3s; text-decoration: none; display: inline-block; }
        .btn-primary:hover { background: var(--hover-orange); }
        .btn-secondary { background: #f4f4f4; color: #58595B; padding: 12px 22px; border: none; border-radius: 50px; font-weight: 600; font-size: 0.85rem; cursor: pointer; text-decoration: none; display: inline-block; }

        /* BADGES */
        .badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #e8f5e9; color: #4CAF50; }
        .badge-warning { background: #fff8e1; color: #F57C00; }
        .badge-danger  { background: #ffebee; color: #E53935; }
        .badge-info    { background: #e3f2fd; color: #1976D2; }
        .badge-grey    { background: #f4f4f4; color: #58595B; }

        /* MOBILE NAV /SCROLL */
        .guest-nav::-webkit-scrollbar { height: 4px; }
        .guest-nav::-webkit-scrollbar-thumb { background: #E84E1B; border-radius: 2px; }
    </style>
</head>
<body>

    <header class="guest-topbar">
        <img src="{{ asset('images/logo.png') }}" alt="Swiss-Belhotel Blulane">
        <div style="display: flex; align-items: center;">
            <div class="guest-info">
                <p>{{ $currentStay->guest->full_name }}</p>
                <span>Room {{ $currentStay->room->room_number }} · Until {{ $currentStay->check_out_date->format('M d') }}</span>
            </div>
            <form method="POST" action="{{ route('guest.logout') }}" style="margin-left: 15px;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>

    <nav class="guest-nav">
        <a href="{{ route('guest.dashboard') }}" class="{{ request()->routeIs('guest.dashboard') ? 'active' : '' }}">Home</a>
        <a href="{{ route('guest.my_stay') }}" class="{{ request()->routeIs('guest.my_stay') ? 'active' : '' }}">My Stay</a>
        <a href="{{ route('guest.request.form') }}" class="{{ request()->routeIs('guest.request.*') ? 'active' : '' }}">Request Service</a>
        <a href="{{ route('guest.my_requests') }}" class="{{ request()->routeIs('guest.my_requests', 'guest.request.detail') ? 'active' : '' }}">My Requests</a>
        <a href="{{ route('guest.notifications') }}" class="{{ request()->routeIs('guest.notifications') ? 'active' : '' }}">Notifications</a>
        <a href="{{ route('guest.hotel_info') }}" class="{{ request()->routeIs('guest.hotel_info') ? 'active' : '' }}">Hotel Info</a>
        <a href="{{ route('guest.review.form') }}" class="{{ request()->routeIs('guest.review.*') ? 'active' : '' }}">Leave Review</a>
    </nav>

    <main class="guest-main">
        @if (session('success'))
            <div class="flash flash-success">✅ {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash flash-error">⚠️ {{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

</body>
</html>