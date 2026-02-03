<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">

<div class="auth-wrapper">
    <div class="auth-card slide-up">
        <h1 class="auth-title">Buat Akun</h1>
        <p class="auth-subtitle">Daftar untuk masuk ke dashboard</p>

        @if ($errors->any())
            <div class="auth-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <input type="text" name="name" required>
                <label>Nama Lengkap</label>
            </div>

            <div class="form-group">
                <input type="text" name="username" required>
                <label>Username</label>
            </div>

            <div class="form-group">
                <input type="email" name="email" required>
                <label>Email</label>
            </div>

            <div class="form-group">
                <input type="password" name="password" required>
                <label>Password</label>
            </div>

            <div class="form-group">
                <input type="password" name="password_confirmation" required>
                <label>Konfirmasi Password</label>
            </div>

            <!-- INI TOMBOL REGISTER -->
            <button type="submit" class="auth-btn">
                Register
            </button>
        </form>

        <!-- INI LINK LOGIN -->
        <div class="auth-link">
            Sudah punya akun?
            <a href="{{ route('login') }}">Login</a>
        </div>
    </div>
</div>

</body>
</html>
