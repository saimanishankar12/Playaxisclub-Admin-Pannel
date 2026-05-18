<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Playaxisclub - Login</title>

    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 960px;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,.12);
            overflow: hidden;
            display: flex;
            min-height: 520px;
        }

        /* ── Left Panel ── */
        .login-left {
            flex: 0 0 42%;
            background: linear-gradient(160deg, #1a56db 0%, #1e3a8a 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 36px;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 280px; height: 280px;
            background: rgba(255,255,255,.05);
            border-radius: 50%;
            top: -80px; right: -80px;
        }

        .login-left::after {
            content: '';
            position: absolute;
            width: 180px; height: 180px;
            background: rgba(255,255,255,.04);
            border-radius: 50%;
            bottom: -40px; left: -40px;
        }

        .login-left img {
            max-width: 120px;
            object-fit: contain;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,.2));
        }

        .login-left h3 {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 800;
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }

        .login-left p {
            color: rgba(255,255,255,.6);
            font-size: .82rem;
            margin-top: 6px;
            position: relative;
            z-index: 1;
        }

        .login-left-badges {
            display: flex;
            gap: 8px;
            margin-top: 28px;
            flex-wrap: wrap;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .login-left-badge {
            background: rgba(255,255,255,.12);
            color: rgba(255,255,255,.85);
            font-size: .68rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,.15);
        }

        /* ── Right Panel ── */
        .login-right {
            flex: 1;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .login-right .login-sub {
            font-size: .83rem;
            color: #94a3b8;
            margin-bottom: 30px;
        }

        /* Alerts */
        .login-alert {
            border-radius: 10px;
            padding: 11px 14px;
            font-size: .82rem;
            font-weight: 500;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .login-alert--error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .login-alert--success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

        /* Form fields */
        .lf-group {
            margin-bottom: 18px;
        }

        .lf-group label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .lf-input-wrap {
            position: relative;
        }

        .lf-input-wrap i.lf-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: .82rem;
            pointer-events: none;
        }

        .lf-input-wrap input {
            width: 100%;
            padding: 11px 14px 11px 38px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: .87rem;
            color: #1e293b;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
        }

        .lf-input-wrap input:focus {
            border-color: #1a56db;
            box-shadow: 0 0 0 3px rgba(26,86,219,.1);
            background: #fff;
        }

        .lf-input-wrap input.is-invalid {
            border-color: #ef4444;
        }

        /* Eye toggle */
        .lf-eye-btn {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            font-size: .82rem;
            padding: 4px;
            transition: color .15s;
        }

        .lf-eye-btn:hover { color: #1a56db; }

        .lf-error {
            font-size: .72rem;
            color: #ef4444;
            margin-top: 4px;
            display: block;
        }

        /* Submit button */
        .lf-submit {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1a56db, #6366f1);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .92rem;
            font-weight: 700;
            cursor: pointer;
            transition: opacity .15s, transform .1s;
            font-family: 'Inter', sans-serif;
            margin-top: 6px;
        }

        .lf-submit:hover { opacity: .92; }
        .lf-submit:active { transform: scale(.98); }

        /* Links */
        .lf-links {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            flex-wrap: wrap;
            gap: 6px;
        }

        .lf-links a {
            font-size: .78rem;
            color: #1a56db;
            text-decoration: none;
            font-weight: 500;
        }

        .lf-links a:hover { text-decoration: underline; }

        /* Divider */
        .lf-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0 0;
            color: #cbd5e1;
            font-size: .72rem;
        }

        .lf-divider::before, .lf-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        @media (max-width: 640px) {
            .login-left { display: none; }
            .login-right { padding: 32px 24px; }
        }
    </style>
</head>

<body>

<div class="login-container">
    <div class="login-card">

        {{-- Left Panel --}}
        <div class="login-left">
            <img src="{{ asset('img/logo-pa.png') }}" alt="PAC Logo">
            <h3>Playaxisclub</h3>
            <p>Admin Panel</p>
            <div class="login-left-badges">
                <span class="login-left-badge"><i class="fas fa-trophy mr-1"></i> Tournaments</span>
                <span class="login-left-badge"><i class="fas fa-users mr-1"></i> Players</span>
                <span class="login-left-badge"><i class="fas fa-chart-bar mr-1"></i> Analytics</span>
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="login-right">
            <h2></h2>
            <p class="login-sub">Sign in to your admin account</p>

            @if(session('error'))
                <div class="login-alert login-alert--error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="login-alert login-alert--success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin-login.submit') }}">
                @csrf

                {{-- Email --}}
                <div class="lf-group">
                    <label>Email Address</label>
                    <div class="lf-input-wrap">
                        <i class="fas fa-envelope lf-icon"></i>
                        <input type="email" name="email"
                            class="@error('email') is-invalid @enderror"
                            placeholder="admin@example.com"
                            value="{{ old('email') }}" required>
                    </div>
                    @error('email')<span class="lf-error">{{ $message }}</span>@enderror
                </div>

                {{-- Password --}}
                <div class="lf-group">
                    <label>Password</label>
                    <div class="lf-input-wrap">
                        <i class="fas fa-lock lf-icon"></i>
                        <input type="password" name="password" id="passwordInput"
                            class="@error('password') is-invalid @enderror"
                            placeholder="Enter your password" required>
                        <button type="button" class="lf-eye-btn" id="eyeBtn" onclick="togglePassword()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')<span class="lf-error">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="lf-submit">
                    <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                </button>

            </form>

            <div class="lf-links">
                <a href="{{ route('admin-forgot-password.form') }}"><i class="fas fa-key mr-1"></i> Forgot Password?</a>
                <a href="{{ route('admin-register') }}"><i class="fas fa-user-plus mr-1"></i> Create Account</a>
            </div>
        </div>

    </div>
</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
    function togglePassword() {
        const input   = document.getElementById('passwordInput');
        const icon    = document.getElementById('eyeIcon');
        const isText  = input.type === 'text';
        input.type    = isText ? 'password' : 'text';
        icon.className = isText ? 'fas fa-eye' : 'fas fa-eye-slash';
    }
</script>

</body>
</html>