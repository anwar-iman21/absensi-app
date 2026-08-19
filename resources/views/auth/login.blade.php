<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Absensi Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 25px 60px rgba(0,0,0,.25);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            padding: 2rem;
            text-align: center;
        }
        .login-header .logo-circle {
            width: 72px; height: 72px;
            background: rgba(255,255,255,.15);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem; color: #fff;
        }
        .login-body { padding: 2rem; }
        .form-control {
            border-radius: .6rem;
            border: 1.5px solid #e2e8f0;
            padding: .75rem 1rem;
            transition: all .2s;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,.15);
        }
        .btn-login {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: .6rem;
            padding: .75rem;
            font-weight: 600;
            letter-spacing: .02em;
            transition: all .2s;
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(79,70,229,.4); }
        .input-group-text { background: #f8fafc; border-color: #e2e8f0; border-radius: .6rem 0 0 .6rem; }
    </style>
</head>
<body>
<div class="login-card card">
    <div class="login-header">
        <div class="logo-circle"><i class="bi bi-fingerprint"></i></div>
        <h4 class="text-white fw-bold mb-1">Absensi Digital</h4>
        <p class="text-white-50 mb-0 small">Sistem Absensi Karyawan</p>
    </div>
    <div class="login-body">
        @if($errors->any())
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 py-2">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold small">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill text-muted"></i></span>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="email@perusahaan.com" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold small">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill text-muted"></i></span>
                    <input type="password" name="password" id="pwd" class="form-control" placeholder="••••••••" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd()">
                        <i class="bi bi-eye-fill" id="eye-icon"></i>
                    </button>
                </div>
            </div>
            <div class="form-check mb-4">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label small" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-login btn-primary w-100 text-white">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>

        <div class="text-center mt-4 p-3 rounded" style="background:#f8fafc;">
            <p class="small text-muted mb-1"><strong>Demo Akun:</strong></p>
            <p class="small mb-0">Admin: <code>admin@absensi.com</code></p>
            <p class="small mb-0">Karyawan: <code>budi@absensi.com</code></p>
            <p class="small text-muted mb-0">Password: <code>password123</code></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd() {
    const p = document.getElementById('pwd');
    const i = document.getElementById('eye-icon');
    if (p.type === 'password') { p.type = 'text'; i.className = 'bi bi-eye-slash-fill'; }
    else { p.type = 'password'; i.className = 'bi bi-eye-fill'; }
}
</script>
</body>
</html>
