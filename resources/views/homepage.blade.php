<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swiss-Belhotel Blulane | Guest Request</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #ffffff;
            color: #333;
        }

        /* --- VARIABLES --- */
        :root {
            --primary-orange: #E84E1B; /* Matched from logo */
            --hover-orange: #c43d12;
            --text-grey: #58595B; /* Matched from logo text */
        }

        /* --- NAVIGATION BAR --- */
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
            height: 60px; /* Adjust based on your actual logo file size */
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

        /* The Log In Button specific style */
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

        /* --- HERO SECTION --- */
        .hero {
            height: 85vh;
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7)), url('{{ asset("images/hotel-bg.png") }}');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 20px;
        }

        .hero h1 {
            font-size: 3rem;
            color: var(--text-grey);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero h1 span {
            color: var(--primary-orange);
        }

        .hero p {
            font-size: 1.2rem;
            color: #666;
            max-width: 600px;
            margin-bottom: 40px;
        }

        .cta-button {
            background-color: var(--primary-orange);
            color: white;
            padding: 15px 40px;
            font-size: 1rem;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            background-color: var(--hover-orange);
            transform: scale(1.05);
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
            .hero h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

    <!-- NAVIGATION -->
    <nav>
        <div class="logo-container">
            <!-- Make sure the file exists in public/images/logo.png -->
            <img src="{{ asset('images/logo.png') }}" alt="Swiss-Belhotel Blulane Logo">
        </div>
        <ul class="nav-links">
            <li><a href="/">Home</a></li>
            <li><a href="/about">About</a></li>
            <!-- Assuming you have a login route, otherwise set href="#" -->
            <li><a href="/login" class="btn-login">Log In</a></li>
        </ul>
    </nav>

    <!-- HERO SECTION -->
    <header class="hero">
        <h1>Welcome to <br> <span>Swiss-Belhotel Blulane</span></h1>
        <p>Manage your guest requests efficiently and enhance the hospitality experience in Chinatown, Manila.</p>
        
        <a href="/login" class="cta-button">Make a Request</a>
    </header>

    <footer>
        &copy; {{ date('Y') }} Swiss-Belhotel Blulane. All Rights Reserved.
    </footer>

</body>
</html>