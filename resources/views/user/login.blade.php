<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Playaxisclub - Player Login</title>
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">

                        {{-- Left Side --}}
                        <div class="col-lg-6 d-none d-lg-block"
                            style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 p-5">
                                <img src="{{ asset('img/logo-pa.png') }}" alt="PAC Logo"
                                    style="max-width:65%;object-fit:contain;">
                                <h4 class="text-white mt-4 font-weight-bold">Playaxisclub</h4>
                                <p class="text-white-50 small text-center">Player Portal</p>
                            </div>
                        </div>

                        {{-- Right Side --}}
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-1">Player Login</h1>
                                    <p class="text-muted small mb-4">Enter your Player ID, Season ID and Email to continue</p>
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

                                <form method="POST" action="{{ route('user-login.submit') }}">
                                    @csrf

                                    {{-- Player ID --}}
                                    <div class="form-group">
                                        <label class="text-gray-700 small font-weight-bold">
                                            Player ID <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white">
                                                    <i class="fas fa-id-badge text-primary"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="player_id"
                                                class="form-control @error('player_id') is-invalid @enderror"
                                                placeholder="e.g. PAC00001"
                                                value="{{ old('player_id') }}" required autofocus>
                                        </div>
                                        @error('player_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- Season ID --}}
                                    <div class="form-group">
                                        <label class="text-gray-700 small font-weight-bold">
                                            Season ID <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white">
                                                    <i class="fas fa-calendar-alt text-primary"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="season_id"
                                                class="form-control @error('season_id') is-invalid @enderror"
                                                placeholder="e.g. S2025"
                                                value="{{ old('season_id') }}" required>
                                        </div>
                                        @error('season_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div class="form-group">
                                        <label class="text-gray-700 small font-weight-bold">
                                            Email Address <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white">
                                                    <i class="fas fa-envelope text-primary"></i>
                                                </span>
                                            </div>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="Enter your registered email"
                                                value="{{ old('email') }}" required>
                                        </div>
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-user btn-block mt-4">
                                        <i class="fas fa-paper-plane mr-2"></i> Send OTP
                                    </button>
                                </form>
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
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
</body>
</html>