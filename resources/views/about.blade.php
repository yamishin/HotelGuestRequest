<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Swiss-Belhotel Blulane</title>
    <!-- Importing Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- RESET & BASIC STYLES --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #ffffff;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* --- VARIABLES --- */
        :root {
            --primary-orange: #E84E1B;
            --hover-orange: #c43d12;
            --text-grey: #58595B;
        }

        /* --- NAVIGATION BAR (Same as Homepage) --- */
        nav {
            background-color: #ffffff;
            height: 90px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 5%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-container img {
            height: 60px;
            width: auto;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-grey);
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
            transition: color 0.3s ease;
            text-transform: uppercase;
        }

        .nav-links a:hover {
            color: var(--primary-orange);
        }

        /* Highlight the active page (About) */
        .nav-links a.active {
            color: var(--primary-orange);
        }

        .btn-login {
            background-color: var(--primary-orange);
            color: white !important;
            padding: 10px 25px;
            border-radius: 50px;
            transition: background-color 0.3s ease !important;
        }

        .btn-login:hover {
            background-color: var(--hover-orange);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(232, 78, 27, 0.3);
        }

        /* --- ABOUT CONTENT SECTION --- */
        .about-container {
            flex: 1; /* Pushes footer to the bottom */
            padding: 80px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fcfcfc; /* Very light grey to contrast the white nav */
        }

        .about-content {
            max-width: 800px;
            text-align: center;
            background: white;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .about-content h1 {
            font-size: 2.5rem;
            color: var(--text-grey);
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
        }

        /* Orange underline effect for the title */
        .about-content h1::after {
            content: '';
            position: absolute;
            width: 50%;
            height: 4px;
            background-color: var(--primary-orange);
            bottom: -10px;
            left: 25%;
            border-radius: 2px;
        }

        .about-content p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #666;
            margin-bottom: 20px;
            text-align: justify;
        }

        /* --- FOOTER --- */
        footer {
            background-color: var(--text-grey);
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 0.8rem;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                height: auto;
                padding: 15px;
            }
            .nav-links {
                margin-top: 15px;
                gap: 15px;
            }
            .about-content {
                padding: 30px 20px;
            }
            .about-content h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

    <!-- NAVIGATION -->
    <nav>
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="Swiss-Belhotel Blulane Logo">
        </div>
        <ul class="nav-links">
            <li><a href="/">Home</a></li>
            <!-- Added 'active' class to highlight this is the About page -->
            <li><a href="/about" class="active">About</a></li>
            <li><a href="/login" class="btn-login">Log In</a></li>
        </ul>
    </nav>

    <!-- ABOUT CONTENT -->
    <main class="about-container">
        <div class="about-content">
            <h1>About Us</h1>
            
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            
            <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            
            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
        </div>
    </main>

    <!-- FOOTER -->
    <footer>
        &copy; {{ date('Y') }} Swiss-Belhotel Blulane. All Rights Reserved.
    </footer>

</body>
</html>