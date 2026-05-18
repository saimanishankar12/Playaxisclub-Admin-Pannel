<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Change Password</title>

    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <style>
        .password-toggle { position: relative; }
        .password-toggle .toggle-eye {
            position: absolute; right: 14px; top: 50%;
            transform: translateY(-50%); cursor: pointer; color: #858796;
        }
        .strength-bar { height: 5px; border-radius: 3px; transition: width .3s, background-color .3s; width: 0; }
    </style>
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Set New Password</h1>
                                        <p class="mb-4">Create a strong password for your admin account.</p>
                                    </div>

                                    {{-- Errors --}}
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            @foreach ($errors->all() as $error)
                                                <div>{{ $error }}</div>
                                            @endforeach
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <form class="user" action="{{ route('admin-change-password.submit') }}" method="POST">
                                        @csrf

                                        {{-- New Password --}}
                                        <div class="form-group password-toggle">
                                            <input
                                                type="password"
                                                name="password"
                                                id="password"
                                                class="form-control form-control-user @error('password') is-invalid @enderror"
                                                placeholder="New Password"
                                                required
                                                minlength="8"
                                            >
                                            <span class="toggle-eye" onclick="toggleVisibility('password', this)">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Password strength indicator --}}
                                        <div class="mb-3 px-1">
                                            <div class="bg-light rounded" style="height:5px;">
                                                <div id="strengthBar" class="strength-bar rounded"></div>
                                            </div>
                                            <small id="strengthText" class="text-muted"></small>
                                        </div>

                                        {{-- Confirm Password --}}
                                        <div class="form-group password-toggle">
                                            <input
                                                type="password"
                                                name="password_confirmation"
                                                id="password_confirmation"
                                                class="form-control form-control-user @error('password_confirmation') is-invalid @enderror"
                                                placeholder="Confirm New Password"
                                                required
                                                minlength="8"
                                            >
                                            <span class="toggle-eye" onclick="toggleVisibility('password_confirmation', this)">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Match hint --}}
                                        <div class="mb-3 px-1">
                                            <small id="matchHint"></small>
                                        </div>

                                        <button type="submit" id="submitBtn" class="btn btn-primary btn-user btn-block">
                                            <i class="fas fa-lock mr-1"></i> Change Password
                                        </button>
                                    </form>

                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="{{ route('admin-login') }}">Back to Login</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <script>
        // Toggle password visibility
        function toggleVisibility(fieldId, icon) {
            const field = document.getElementById(fieldId);
            const i = icon.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                i.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                i.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Password strength meter
        document.getElementById('password').addEventListener('input', function () {
            const val = this.value;
            const bar = document.getElementById('strengthBar');
            const txt = document.getElementById('strengthText');

            let score = 0;
            if (val.length >= 8)            score++;
            if (/[A-Z]/.test(val))          score++;
            if (/[0-9]/.test(val))          score++;
            if (/[^A-Za-z0-9]/.test(val))   score++;

            const levels = [
                { label: '',         color: '',          pct: '0%'   },
                { label: 'Weak',     color: '#e74a3b',   pct: '25%'  },
                { label: 'Fair',     color: '#f6c23e',   pct: '50%'  },
                { label: 'Good',     color: '#1cc88a',   pct: '75%'  },
                { label: 'Strong',   color: '#36b9cc',   pct: '100%' },
            ];

            bar.style.width           = levels[score].pct;
            bar.style.backgroundColor = levels[score].color;
            txt.textContent           = score > 0 ? levels[score].label : '';
            txt.style.color           = levels[score].color;
        });

        // Password match hint
        document.getElementById('password_confirmation').addEventListener('input', function () {
            const hint = document.getElementById('matchHint');
            const match = this.value === document.getElementById('password').value;
            hint.textContent    = this.value.length ? (match ? '✔ Passwords match' : '✘ Passwords do not match') : '';
            hint.style.color    = match ? '#1cc88a' : '#e74a3b';
        });
    </script>

</body>

</html>