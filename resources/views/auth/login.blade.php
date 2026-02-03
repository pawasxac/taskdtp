<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">

<div class="auth-wrapper">
    <div class="auth-card slide-up">
        <h1 class="auth-title">Welcome Back</h1>
        <p class="auth-subtitle">Masuk ke dashboard admin</p>

        @if ($errors->any())
            <div class="auth-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <input type="text" name="login" required>
                <label>Email atau Username</label>
            </div>

            <div class="form-group">
                <input type="password" name="password" required>
                <label>Password</label>
            </div>

            <button type="submit" class="auth-btn">Login</button>
        </form>

        <div class="auth-link">
            Belum punya akun?
            <a href="{{ route('register') }}">Daftar</a>
        </div>
    </div>
</div>

</body>
</html>
