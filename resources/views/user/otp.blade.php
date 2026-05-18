<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Playaxisclub - Verify OTP</title>
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h1 class="h4 text-gray-900 mb-1">Verify OTP</h1>
                        <p class="text-muted small">Enter the 6-digit OTP sent to your registered email address.</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user-login.otp.verify') }}">
                        @csrf
                        <div class="form-group">
                            <label class="text-gray-700 small font-weight-bold text-center d-block">
                                Enter OTP <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="otp"
                                class="form-control form-control-lg text-center @error('otp') is-invalid @enderror"
                                placeholder="_ _ _ _ _ _"
                                maxlength="6"
                                style="font-size:1.8rem;letter-spacing:8px;font-weight:700;"
                                autofocus required>
                            @error('otp')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-user btn-block mt-3">
                            <i class="fas fa-check-circle mr-2"></i> Verify & Login
                        </button>

                        <div class="text-center mt-3">
                            <a href="{{ route('user-login') }}" class="small text-muted">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
</body>
</html>