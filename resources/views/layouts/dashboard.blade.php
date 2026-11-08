<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Swiss-Belhotel Blulane</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        :root {
            --primary-orange: #E84E1B;
            --hover-orange: #c43d12;
            --text-grey: #58595B;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background-color: #ffffff;
            border-right: 1px solid #eee;
            display: flex;
            flex-direction: column;
            height: 100%;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .sidebar-header img {
            height: 50px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            flex: 1;
            overflow-y: auto;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 25px;
            color: #58595B;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        .sidebar-menu li a i {
            color: var(--primary-orange);
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background-color: #fff5f2;
            border-left: 4px solid var(--primary-orange);
            color: var(--primary-orange);
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid #eee;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            background-color: #58595B;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-logout:hover {
            background-color: var(--primary-orange);
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .topbar {
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
        }

        .topbar h2 {
            color: #58595B;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .topbar h2 i {
            color: var(--primary-orange);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-meta {
            text-align: right;
        }

        .user-meta .user-name {
            font-size: 0.9rem;
            color: var(--primary-orange);
            font-weight: 700;
        }

        .user-meta .user-role {
            font-size: 0.7rem;
            color: var(--primary-orange);
            opacity: 0.75;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-top: 2px;
        }

        .logout-btn {
            background: #f4f4f4;
            color: #58595B;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: var(--primary-orange);
            color: white;
        }

        .logout-btn i {
            color: var(--primary-orange);
            transition: color 0.2s;
        }

        .logout-btn:hover i {
            color: white;
        }

        .content-area {
            padding: 30px;
            overflow-y: auto;
            flex: 1;
        }

        /* GENERIC CARDS & BUTTONS */
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.1rem;
            color: #58595B;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f4f4f4;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-primary {
            background: var(--primary-orange);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: var(--hover-orange);
        }

        .btn-secondary {
            background: #f4f4f4;
            color: #58595B;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: #e8e8e8;
        }

        /* TABLES */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead th {
            text-align: left;
            padding: 12px 15px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #999;
            border-bottom: 2px solid #f4f4f4;
            white-space: nowrap;
        }

        .data-table tbody td {
            padding: 15px;
            font-size: 0.85rem;
            color: #58595B;
            border-bottom: 1px solid #f4f4f4;
        }

        .data-table tbody tr:hover {
            background: #fafafa;
        }

        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        /* BADGES */
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background: #e8f5e9;
            color: #4CAF50;
        }

        .badge-warning {
            background: #fff8e1;
            color: #F57C00;
        }

        .badge-danger {
            background: #ffebee;
            color: #E53935;
        }

        .badge-info {
            background: #e3f2fd;
            color: #1976D2;
        }

        .badge-orange {
            background: #fff5f2;
            color: var(--primary-orange);
        }

        .badge-grey {
            background: #f4f4f4;
            color: #58595B;
        }

        /* PAGE HEADER */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header h1 {
            font-size: 1.6rem;
            color: #58595B;
        }

        .page-header p {
            font-size: 0.9rem;
            color: #888;
            margin-top: 5px;
        }

        /* FORM & SEARCH */
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-bar input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            outline: none;
        }

        .search-bar input:focus {
            border-color: var(--primary-orange);
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #58595B;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border 0.3s;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary-orange);
        }

        .error-msg {
            font-size: 0.75rem;
            color: #E53935;
            margin-top: 4px;
            display: block;
        }

        /* FLASH & PAGINATION */
        .flash {
            padding: 14px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4CAF50;
        }

        .flash-error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #E53935;
        }

        .pagination {
            display: flex;
            gap: 6px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .pagination .page-item .page-link {
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid #eee;
            background: white;
            color: #58595B;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-orange);
            color: white;
            border-color: var(--primary-orange);
        }

        /* --- MOBILE RESPONSIVENESS (HAMBURGER MENU) --- */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-orange);
            cursor: pointer;
            padding: 5px;
        }

        .mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s;
        }

        @media (max-width: 992px) {
            .mobile-menu-btn {
                display: block;
            }

            .sidebar {
                position: fixed;
                left: -260px;
                top: 0;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
                box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            }

            .sidebar.active {
                left: 0;
            }

            .mobile-overlay.active {
                display: block;
                opacity: 1;
            }

            @media (max-width: 576px) {
                .user-meta {
                    display: none;
                }
            }

            .stats-grid,
            .main-grid,
            .detail-grid,
            .info-grid,
            .profile-grid,
            .staff-grid,
            .quick-actions {
                grid-template-columns: 1fr !important;
            }

            .welcome-banner {
                flex-direction: column;
                text-align: center;
                gap: 15px;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="mainSidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            @yield('sidebar-menu')
        </ul>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Log Out
                </button>
            </form>
        </div>
    </aside>

    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleSidebar()"></div>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="topbar">
            <div style="display: flex; align-items: center; gap: 15px;">
                {{-- HAMBURGER BUTTON (Mobile Only) --}}
                <button type="button" class="mobile-menu-btn" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <h2>
                    <i class="fa-solid fa-gauge-high"></i>
                    @yield('page-title')
                </h2>
            </div>

            <div class="user-info">
                <div class="user-meta">
                    <div class="user-name">
                        @php
                        $currentUser = \Illuminate\Support\Facades\Auth::user();
                        $roleIcon = match($currentUser?->role) {
                        'frontdesk' => 'fa-solid fa-bell-concierge',
                        'supervisor' => 'fa-solid fa-user-tie',
                        'staff' => 'fa-solid fa-screwdriver-wrench',
                        default => 'fa-solid fa-user'
                        };
                        @endphp
                        <i class="{{ $roleIcon }}" style="font-size: 0.8rem; margin-right: 4px;"></i>
                        {{ $currentUser?->name ?? 'Guest' }}
                    </div>
                    <div class="user-role">{{ $currentUser?->role ?? 'Visitor' }}</div>
                </div>
                <div class="sidebar-footer">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-logout">
                            <i class="fa-solid fa-right-from-bracket"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
        </header>
        <div class="content-area">
            @if (session('success'))
            <div class="flash flash-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
            @endif
            @if (session('error'))
            <div class="flash flash-error">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('mainSidebar').classList.toggle('active');
            document.getElementById('mobileOverlay').classList.toggle('active');
        }
    </script>

</body>

</html>