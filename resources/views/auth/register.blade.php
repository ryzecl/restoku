<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Mazer Admin Dashboard</title>
    <link rel="stylesheet" crossorigin="" href="{{ asset('assets/admin/compiled/css/app.css') }}" />
    <link rel="stylesheet" crossorigin="" href="{{ asset('assets/admin/compiled/css/app-dark.css') }}" />
    <link rel="stylesheet" crossorigin="" href="{{ asset('assets/admin/compiled/css/auth.css') }}" />
    <link rel="icon" href="https://apps.codepolitan.com/sites/learn/uploads/original/2/logo-codepolitan.png">
</head>

<body>
    <script src="{{ asset('assets/admin/static/js/initTheme.js') }}"></script>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo">

                    </div>
                    <h1 class="auth-title">Register Restoku.</h1>
                    <p class="auth-subtitle mb-5">
                        Buat akun baru untuk mengakses layanan Restoku.
                    </p>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" class="form-control form-control-xl" name="name"
                                value="{{ old('name') }}" required autofocus placeholder="Nama Lengkap"
                                autocomplete="name" />
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" class="form-control form-control-xl" name="email"
                                value="{{ old('email') }}" required placeholder="Email" autocomplete="email" />
                            <div class="form-control-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl" name="password" required
                                placeholder="Password" autocomplete="new-password" />
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl" name="password_confirmation"
                                required placeholder="Konfirmasi Password" autocomplete="new-password" />
                            <div class="form-control-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">
                            {{ __('Register') }}
                        </button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        <p class="text-gray-600">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="font-bold">Log in</a>.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right"></div>
            </div>
        </div>
    </div>
</body>

</html>
