<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin Login</title>
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="text-center fw-bold mb-4">ADMIN LOGIN</h4>
                        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
                        <form action="{{ route('admin.login.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                            <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                            <button type="submit" class="btn btn-dark w-100">Sign In</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>