<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | Swiss-Belhotel Blulane</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .login-container img {
            height: 60px;
            margin-bottom: 20px;
        }
        .login-container h2 {
            color: #58595B;
            margin-bottom: 8px;
            font-size: 1.4rem;
        }
        .login-container .subtitle {
            color: #999;
            font-size: 0.85rem;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #58595B;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .form-group input {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            outline: none;
            transition: border 0.3s;
            font-size: 0.9rem;
        }
        .form-group input:focus {
            border-color: #E84E1B;
        }
        .btn-submit {
            width: 100%;
            background-color: #E84E1B;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background-color: #c43d12;
        }
        .error-box {
            background: #ffebee;
            color: #c62828;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            text-align: left;
            border-left: 4px solid #E53935;
        }
        .demo-creds {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: left;
        }
        .demo-creds p {
            font-size: 0.75rem;
            color: #999;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .demo-creds ul {
            list-style: none;
            font-size: 0.8rem;
            color: #666;
            line-height: 1.8;
        }
        .demo-creds code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            color: #E84E1B;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <img src="{{ asset('images/logo.png') }}" alt="Swiss-Belhotel Blulane Logo">
        <h2>Staff Portal</h2>
        <p class="subtitle">Sign in to your dashboard</p>

        @if ($errors->any())
            <div class="error-box">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn-submit">Log In</button>
        </form>

        <!-- Demo credentials for testing -->
        <div class="demo-creds">
            <p>Demo Accounts (Password: <code>password</code>)</p>
            <ul>
                <li>Front Desk — <code>frontdesk@blulane.com</code></li>
                <li>Supervisor — <code>supervisor@blulane.com</code></li>
                <li>Staff (Housekeeping) — <code>maria.housekeeping@blulane.com</code></li>
                <li>Staff (Maintenance) — <code>carlos.maintenance@blulane.com</code></li>
                <li>Staff (IT) — <code>sam.it@blulane.com</code></li>
            </ul>
        </div>
    </div>

</body>
</html>