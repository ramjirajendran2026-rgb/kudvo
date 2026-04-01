<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kudvo Admin | Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a2234 0%, #2c3e50 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .entry-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 50px;
            width: 100%;
            max-width: 500px;
            text-align: center;
            color: white;
        }
        .btn-auth {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 10px;
            transition: 0.3s;
            width: 100%;
        }
        .btn-login {
            background-color: #2ecc71;
            border: none;
            color: white;
        }
        .btn-login:hover { background-color: #27ae60; color: white; }
        .btn-register {
            background-color: transparent;
            border: 2px solid rgba(255,255,255,0.2);
            color: white;
        }
        .btn-register:hover { border-color: #2ecc71; color: #2ecc71; }
        .logo-text { font-size: 3rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="entry-card shadow-lg">
        <div class="logo-text">kudvo</div>
        <p class="text-secondary mb-5">Secure Voting Administration Panel</p>

        <div class="d-grid gap-3">
            <a href="{{ route('admin.login') }}" class="btn btn-auth btn-login shadow">
                SIGN IN TO DASHBOARD
            </a>
            
            <div class="d-flex align-items-center my-2">
                <hr class="flex-grow-1 opacity-25">
                <span class="mx-3 text-secondary small">OR</span>
                <hr class="flex-grow-1 opacity-25">
            </div>

            <a href="{{ route('admin.register') }}" class="btn btn-auth btn-register">
                CREATE NEW ACCOUNT
            </a>
        </div>

        <div class="mt-5 pt-3 border-top border-secondary opacity-50">
            <small>&copy; 2026 kudvo Admin Panel</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>