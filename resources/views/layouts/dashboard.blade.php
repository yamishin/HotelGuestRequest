<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Swiss-Belhotel Blulane</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Font Awesome 6 (Free) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Montserrat', sans-serif; background-color: #f8f9fa; display: flex; height: 100vh; overflow: hidden; }
    :root { --primary-orange: #E84E1B; --hover-orange: #c43d12; --text-grey: #58595B; }

    /* SIDEBAR */
    .sidebar { width: 260px; background-color: #ffffff; border-right: 1px solid #eee; display: flex; flex-direction: column; height: 100%; }
    .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid #eee; }
    .sidebar-header img { height: 50px; }

    .sidebar-menu { list-style: none; padding: 20px 0; flex: 1; overflow-y: auto; }
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
        transition: transform 0.2s;
    }
    .sidebar-menu li a:hover i,
    .sidebar-menu li a.active i {
        transform: scale(1.15);
    }
    .sidebar-menu li a:hover,
    .sidebar-menu li a.active {
        background-color: #fff5f2;
        border-left: 4px solid var(--primary-orange);
        color: var(--primary-orange);
    }

    .sidebar-footer { padding: 20px; border-top: 1px solid #eee; }
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
        font-family: 'Montserrat', sans-serif;
        cursor: pointer;
        transition: background 0.3s;
    }
    .btn-logout:hover { background-color: var(--primary-orange); }
    .btn-logout i { font-size: 0.85rem; }

    /* MAIN CONTENT */
    .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
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
    }
    .topbar h2 i { color: var(--primary-orange); }

    .user-info { display: flex; align-items: center; gap: 15px; }

    .user-meta { text-align: right; }
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
    .logout-btn i { color: var(--primary-orange); transition: color 0.2s; }
    .logout-btn:hover i { color: white; }

    .content-area { padding: 30px; overflow-y: auto; flex: 1; }

    /* GENERIC CARD */
    .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .card-title { font-size: 1.1rem; color: #58595B; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f4f4f4; display: flex; justify-content: space-between; align-items: center; }

    /* BUTTONS */
    .btn-primary { background: var(--primary-orange); color: white; padding: 10px 20px; border: none; border-radius: 5px; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: background 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary:hover { background: var(--hover-orange); }
    .btn-primary i { color: white; }

    .btn-secondary { background: #f4f4f4; color: #58595B; padding: 10px 20px; border: none; border-radius: 5px; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: background 0.3s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-secondary:hover { background: #e8e8e8; }
    .btn-secondary i { color: var(--primary-orange); }

    /* TABLES */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th { text-align: left; padding: 12px 15px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #999; border-bottom: 2px solid #f4f4f4; }
    .data-table tbody td { padding: 15px; font-size: 0.85rem; color: #58595B; border-bottom: 1px solid #f4f4f4; }
    .data-table tbody tr:hover { background: #fafafa; }

    /* BADGES */
    .badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
    .badge-success { background: #e8f5e9; color: #4CAF50; }
    .badge-warning { background: #fff8e1; color: #F57C00; }
    .badge-danger { background: #ffebee; color: #E53935; }
    .badge-info { background: #e3f2fd; color: #1976D2; }
    .badge-orange { background: #fff5f2; color: var(--primary-orange); }
    .badge-grey { background: #f4f4f4; color: #58595B; }

    /* PAGE HEADER */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; }
    .page-header h1 { font-size: 1.6rem; color: #58595B; }
    .page-header p { font-size: 0.9rem; color: #888; margin-top: 5px; }

    /* SEARCH BAR */
    .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
    .search-bar input { flex: 1; padding: 12px 15px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat', sans-serif; font-size: 0.9rem; outline: none; }
    .search-bar input:focus { border-color: var(--primary-orange); }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .sidebar { width: 70px; }
        .sidebar-menu li a { font-size: 0; padding: 15px; justify-content: center; }
        .sidebar-menu li a i { font-size: 1.3rem; width: auto; }
        .sidebar-header img { height: 30px; }
        .user-meta { display: none; }
    }

    /* FLASH */
    .flash { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
    .flash-success { background: #e8f5e9; color: #2e7d32; border-left: 4px solid #4CAF50; }
    .flash-error { background: #ffebee; color: #c62828; border-left: 4px solid #E53935; }
    .flash i { font-size: 1rem; }

    /* PAGINATION */
    .pagination { display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; }
    .pagination .page-item .page-link {
        padding: 8px 14px; border-radius: 6px; border: 1px solid #eee; background: white;
        color: #58595B; text-decoration: none; font-size: 0.85rem; font-weight: 600;
        font-family: 'Montserrat', sans-serif;
    }
    .pagination .page-item.active .page-link { background: #E84E1B; color: white; border-color: #E84E1B; }
    .pagination .page-item .page-link:hover { border-color: #E84E1B; color: #E84E1B; }
    .pagination .page-item.disabled .page-link { color: #ccc; pointer-events: none; }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
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
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="topbar">
            <h2>
                <i class="fa-solid fa-gauge-high"></i>
                @yield('page-title')
            </h2>
            <div class="user-info">
                <div class="user-meta">
                    <div class="user-name">
                        <i class="fa-solid fa-user" style="font-size: 0.8rem;"></i>
                        {{ auth()->user()->name ?? 'Guest' }}
                    </div>
                    <div class="user-role">{{ auth()->user()->role ?? 'Visitor' }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fa-solid fa-power-off"></i>
                        Log Out
                    </button>
                </form>
            </div>
        </header>
        <div class="content-area">
            @if (session('success'))
                <div class="flash flash-success">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="flash flash-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

</body>
</html>