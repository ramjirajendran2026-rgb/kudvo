<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kudvo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --sidebar-bg: #1a2234; --sidebar-active: #2ecc71; }
        body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; }
        
        /* Sidebar Styles */
        .sidebar { width: 250px; background: var(--sidebar-bg); min-height: 100vh; position: fixed; color: white; }
        .nav-link { color: #bdc3c7; padding: 12px 20px; display: flex; align-items: center; text-decoration: none; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid var(--sidebar-active); }
        .nav-link i { margin-right: 15px; font-size: 1.2rem; }
        
        /* Main Content area */
        .main-content { margin-left: 250px; padding: 20px; }
        .topbar { background: white; padding: 15px 30px; border-bottom: 1px solid #e0e0e0; margin-bottom: 30px; border-radius: 8px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="p-4 border-bottom border-secondary mb-3">
        <h4 class="fw-bold mb-0 text-white">KUDVO</h4>
        <small class="text-success">● Online</small>
    </div>
    <nav>
        <a href="#" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="#" class="nav-link"><i class="bi bi-layers"></i> Plans & Options</a>
        <a href="#" class="nav-link"><i class="bi bi-person-badge"></i> Positions</a>
        <a href="#" class="nav-link"><i class="bi bi-people"></i> Candidates</a>
        <a href="#" class="nav-link"><i class="bi bi-credit-card"></i> Payment</a>
        
    </nav>
</div>

<div class="main-content">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>