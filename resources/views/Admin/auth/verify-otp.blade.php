<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Verify OTP</title>

    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <style>
        .otp-input {
            letter-spacing: 6px;
            font-size: 1.4rem;
            font-weight: 700;
            text-align: center;
        }
        .resend-link { font-size: 0.85rem; }
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
                                        <h1 class="h4 text-gray-900 mb-2">Verify OTP</h1>
                                        <p class="mb-1">An OTP has been sent to:</p>
                                        <p class="mb-4 font-weight-bold text-primary">{{ $email }}</p>
                                    </div>

                                    {{-- Success message --}}
                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success') }}
                                            <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button> -->
                                        </div>
                                    @endif

                                    {{-- Errors --}}
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="successAlert">
                                            @foreach ($errors->all() as $error)
                                                <div>{{ $error }}</div>
                                            @endforeach
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <form class="user" action="{{ route('admin-verify-otp.submit') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label class="text-gray-800 small mb-1" for="otp">Enter 6-digit OTP</label>
                                            <input
                                                type="text"
                                                name="otp"
                                                id="otp"
                                                class="form-control form-control-user otp-input @error('otp') is-invalid @enderror"
                                                placeholder="• • • • • •"
                                                maxlength="6"
                                                inputmode="numeric"
                                                pattern="[0-9]{6}"
                                                autocomplete="one-time-code"
                                                value="{{ old('otp') }}"
                                                required
                                            >
                                            @error('otp')
                                                <div class="invalid-feedback text-center">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            <i class="fas fa-check-circle mr-1"></i> Verify OTP
                                        </button>
                                    </form>

                                    <hr>

                                    <div class="text-center resend-link">
                                        <span class="text-gray-600">Didn't receive the OTP?</span>
                                        <a href="{{ route('admin-resend-otp') }}" class="ml-1">Resend OTP</a>
                                    </div>
                                    <div class="text-center mt-2">
                                        <a class="small" href="{{ route('admin-forgot-password.form') }}">
                                            <i class="fas fa-arrow-left mr-1"></i> Back to Forgot Password
                                        </a>
                                    </div>
                                    <div class="text-center mt-2">
                                        <a class="small" href="{{ route('admin-login') }}">Already remember? Login!</a>
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
        // Allow only digits in the OTP field
        document.getElementById('otp').addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

          setTimeout(function () {
            document.getElementById('successAlert').style.display = 'none';
        }, 5000);
    </script>

</body>

</html>