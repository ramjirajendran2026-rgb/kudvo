<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kudvo Admin | Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a2234; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; }
        .register-card { background: #ffffff; border-radius: 15px; width: 100%; max-width: 450px; overflow: hidden; }
        .card-header { background: #2ecc71; color: white; text-align: center; padding: 25px; border: none; }
        .form-control { padding: 12px; border-radius: 8px; border: 1px solid #e0e0e0; }
        .btn-register { background: #1a2234; color: white; padding: 12px; border-radius: 8px; font-weight: 600; width: 100%; transition: 0.3s; }
        .btn-register:hover { background: #2c3e50; color: #2ecc71; }
    </style>
</head>
<body>

    <div class="register-card shadow-lg">
        <div class="card-header">
            <h4 class="fw-bold mb-0 text-uppercase">Create Admin Account</h4>
            <small>Enter details to access kudvo Panel</small>
        </div>
        <div class="card-body p-4">
            
            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.register.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter your name" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="admin@kudvo.com" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                </div>

                <button type="submit" class="btn btn-register border-0">REGISTER NOW</button>
            </form>

            <div class="text-center mt-4">
                <p class="small text-muted">Already have an account? <a href="{{ route('admin.login') }}" class="text-success fw-bold text-decoration-none">Login Here</a></p>
            </div>
        </div>
    </div>

</body>
</html>