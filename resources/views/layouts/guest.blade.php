<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Swiss-Belhotel Blulane</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background-color: #f8f9fa; min-height: 100vh; }
        :root { --primary-orange: #E84E1B; --hover-orange: #c43d12; --text-grey: #58595B; }

        /* TOP BAR */
        .guest-topbar { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .guest-topbar img { height: 45px; }
        
        .guest-top-right { display: flex; align-items: center; gap: 15px; }
        .guest-info { text-align: right; }
        .guest-info p { font-size: 0.85rem; font-weight: 600; color: var(--text-grey); }
        .guest-info span { font-size: 0.75rem; color: #999; }
        .logout-btn { background: #f4f4f4; color: #58595B; padding: 8px 15px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px; }
        .logout-btn:hover { background: #e8e8e8; }

        /* DESKTOP NAVIGATION (Horizontal) */
        .guest-nav { background: white; padding: 0 30px; display: flex; gap: 5px; overflow-x: auto; border-bottom: 1px solid #eee; }
        .guest-nav::-webkit-scrollbar { height: 3px; }
        .guest-nav::-webkit-scrollbar-thumb { background: var(--primary-orange); border-radius: 3px; }
        .guest-nav a { padding: 15px 20px; font-size: 0.85rem; font-weight: 600; color: #888; text-decoration: none; border-bottom: 3px solid transparent; transition: all 0.3s; white-space: nowrap; display: flex; align-items: center; gap: 8px; }
        .guest-nav a i { color: var(--primary-orange); font-size: 0.95rem; }
        .guest-nav a:hover { color: var(--primary-orange); }
        .guest-nav a.active { color: var(--primary-orange); border-bottom-color: var(--primary-orange); }

        /* HAMBURGER BUTTON (Hidden on Desktop) */
        .mobile-menu-btn { display: none; background: none; border: none; font-size: 1.5rem; color: var(--primary-orange); cursor: pointer; }

        /* MOBILE SIDEBAR CLOSE BUTTON */
        .mobile-close-btn { display: none; background: none; border: none; font-size: 1.5rem; color: #999; cursor: pointer; padding: 10px 20px; text-align: right; width: 100%; }

        /* OVERLAY (Dim Background) */
        .mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s;
        }

        /* MAIN */
        .guest-main { max-width: 1000px; margin: 0 auto; padding: 30px 20px 60px; }

        /* FLASH */
        .flash { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .flash-success { background: #e8f5e9; color: #2e7d32; border-left: 4px solid #4CAF50; }
        .flash-error { background: #ffebee; color: #c62828; border-left: 4px solid #E53935; }

        /* CARDS & BUTTONS */
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .card-title { font-size: 1.05rem; color: #58595B; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 2px solid #f4f4f4; }
        .btn-primary { background: var(--primary-orange); color: white; padding: 12px 22px; border: none; border-radius: 50px; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: background 0.3s; text-decoration: none; display: inline-block; text-align: center; }
        .btn-primary:hover { background: var(--hover-orange); }
        .btn-secondary { background: #f4f4f4; color: #58595B; padding: 12px 22px; border: none; border-radius: 50px; font-weight: 600; font-size: 0.85rem; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; }
        .badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #e8f5e9; color: #4CAF50; }
        .badge-warning { background: #fff8e1; color: #F57C00; }
        .badge-danger  { background: #ffebee; color: #E53935; }
        .badge-info    { background: #e3f2fd; color: #1976D2; }
        .badge-grey    { background: #f4f4f4; color: #58595B; }

        /* 🟢 MOBILE RESPONSIVENESS (SLIDING SIDEBAR) 🟢 */
        @media (max-width: 768px) {
            .guest-topbar { padding: 15px; }
            .guest-topbar img { height: 35px; }
            .mobile-menu-btn { display: block; }
            .guest-info { display: none; } 

            /* Turn the horizontal nav into a slide-out sidebar */
            .guest-nav { 
                position: fixed;
                left: -260px; 
                top: 0; 
                height: 100vh; 
                width: 260px;
                background: white;
                flex-direction: column;
                padding: 0;
                z-index: 1000;
                transition: left 0.3s ease;
                box-shadow: 4px 0 10px rgba(0,0,0,0.1);
                border-bottom: none;
            }

            /* Show the close button */
            .mobile-close-btn { display: block; }

            /* Slide in */
            .guest-nav.active { left: 0; }

            /* Show overlay */
            .mobile-overlay.active { display: block; opacity: 1; }

            /* Style the links for the sidebar */
            .guest-nav a { 
                padding: 16px 20px; 
                border-bottom: 1px solid #f4f4f4; 
                border-left: 4px solid transparent; 
                border-radius: 0;
                width: 100%;
            }

            .guest-nav a.active { 
                border-left-color: var(--primary-orange); 
                background: #fff5f2;
            }

            .guest-main { padding: 20px 15px; }
            .card { padding: 20px; }
            .btn-primary, .btn-secondary { width: 100%; margin-bottom: 10px; }
        }
    </style>    
</head>
<body>

    <header class="guest-topbar">
        <div style="display: flex; align-items: center; gap: 15px;">
            {{-- HAMBURGER BUTTON (Mobile Only) --}}
            <button type="button" class="mobile-menu-btn" onclick="toggleGuestSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <img src="{{ asset('images/logo.png') }}" alt="Swiss-Belhotel Blulane">
        </div>

        <div class="guest-top-right">
            <div class="guest-info">
                <p>{{ $currentStay->guest->full_name }}</p>
                <span>Room {{ $currentStay->room->room_number }} · Until {{ $currentStay->check_out_date->format('M d') }}</span>
            </div>
            <form method="POST" action="{{ route('guest.logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fa-solid fa-power-off"></i> Logout
                </button>
            </form>
        </div>
    </header>

    {{-- MOBILE OVERLAY --}}
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleGuestSidebar()"></div>

    <nav class="guest-nav" id="guestNav">
        {{-- CLOSE BUTTON FOR MOBILE --}}
        <button class="mobile-close-btn" onclick="toggleGuestSidebar()">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <a href="{{ route('guest.dashboard') }}" class="{{ request()->routeIs('guest.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> Home</a>
        <a href="{{ route('guest.my_stay') }}" class="{{ request()->routeIs('guest.my_stay') ? 'active' : '' }}"><i class="fa-solid fa-hotel"></i> My Stay</a>
        <a href="{{ route('guest.request.form') }}" class="{{ request()->routeIs('guest.request.*') ? 'active' : '' }}"><i class="fa-solid fa-bell-concierge"></i> Request Service</a>
        <a href="{{ route('guest.my_requests') }}" class="{{ request()->routeIs('guest.my_requests', 'guest.request.detail') ? 'active' : '' }}"><i class="fa-solid fa-list-check"></i> My Requests</a>
        <a href="{{ route('guest.notifications') }}" class="{{ request()->routeIs('guest.notifications') ? 'active' : '' }}"><i class="fa-solid fa-bell"></i> Notifications</a>
        <a href="{{ route('guest.hotel_info') }}" class="{{ request()->routeIs('guest.hotel_info') ? 'active' : '' }}"><i class="fa-solid fa-circle-info"></i> Hotel Info</a>
        <a href="{{ route('guest.review.form') }}" class="{{ request()->routeIs('guest.review.*') ? 'active' : '' }}"><i class="fa-solid fa-star"></i> Leave Review</a>
    </nav>

    <main class="guest-main">
        @if (session('success'))
            <div class="flash flash-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash flash-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    <script>
        function toggleGuestSidebar() {
            document.getElementById('guestNav').classList.toggle('active');
            document.getElementById('mobileOverlay').classList.toggle('active');
        }
    </script>
</body>
</html>