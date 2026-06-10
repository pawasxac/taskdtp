<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — {{ config('app.name', 'Brew & Breathe') }}</title>
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Instrument Sans', 'sans-serif'] },
                    colors: {
                        espresso: '#0f0e0d',
                        mocha: '#1a1816',
                        latte: '#c6a88b',
                        sage: '#8a9b83'
                    }
                }
            }
        }
</script>

    <style>
        body {
            background-color: #0f0e0d;
            color: #f4f4f5;
            font-family: 'Instrument Sans', sans-serif;
        }

        .noise-bg {
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.02'/%3E%3C/svg%3E");
        }

        .glass-card {
            background: rgba(26, 24, 22, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .logo-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: rgba(198, 168, 139, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            color: #f4f4f5;
            font-family: 'Instrument Sans', sans-serif;
            font-size: 14px;
            transition: all 200ms ease;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-input:focus {
            outline: none;
            background-color: rgba(255, 255, 255, 0.04);
            border-color: #c6a88b;
            box-shadow: 0 0 0 3px rgba(198, 168, 139, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #f4f4f5;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px 24px;
            background-color: #c6a88b;
            color: #0f0e0d;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 200ms ease;
            font-family: 'Instrument Sans', sans-serif;
        }

        .btn-submit:hover {
            background-color: #ddbfa3;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(198, 168, 139, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .divider {
            position: relative;
            margin: 28px 0;
            text-align: center;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.3);
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.05);
            z-index: 0;
        }

        .divider span {
            position: relative;
            background: #0f0e0d;
            padding: 0 12px;
        }

        .link-secondary {
            display: inline-block;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 14px;
            transition: color 200ms ease;
        }

        .link-secondary:hover {
            color: #c6a88b;
        }

        .link-latte {
            color: #c6a88b;
        }

        .error-box {
            padding: 12px 16px;
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            color: #fca5a5;
            font-size: 13px;
            margin-bottom: 24px;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="antialiased min-h-screen relative overflow-x-hidden noise-bg flex items-center justify-center">

    <!-- Decorative Background -->
    <div class="absolute top-[-20%] left-[10%] w-[500px] h-[500px] bg-latte/5 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[5%] w-[400px] h-[400px] bg-sage/5 blur-[100px] rounded-full pointer-events-none"></div>

    <!-- Container -->
    <div class="w-full max-w-md px-6 relative z-10 max-h-[95vh] overflow-y-auto">

        <!-- Card -->
        <div class="rounded-2xl border border-white/5 bg-mocha/50 p-8 md:p-10 backdrop-blur-md glass-card shadow-2xl fade-in">

            <!-- Header -->
            <div class="text-center mb-8">
                <a href="/" class="inline-block mb-6">
                    <div class="logo-box">
                        ☕
                    </div>
                </a>
                <h1 class="text-3xl font-bold text-white mb-2">Buat Akun</h1>
                <p class="text-white/50 text-sm">Bergabunglah dengan komunitas kami sekarang</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="error-box">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf

                <!-- Full Name Field -->
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input
                        type="text"
                        name="name"
                        class="form-input"
                        placeholder="Masukkan nama lengkap"
                        required
                        value="{{ old('name') }}"
                    >
                </div>

                <!-- Username Field -->
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input
                        type="text"
                        name="username"
                        class="form-input"
                        placeholder="Pilih username unik"
                        required
                        value="{{ old('username') }}"
                    >
                </div>

                <!-- Email Field -->
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-input"
                        placeholder="Masukkan email Anda"
                        required
                        value="{{ old('email') }}"
                    >
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-input"
                        placeholder="Buat password yang kuat"
                        required
                    >
                </div>

                <!-- Password Confirmation Field -->
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-input"
                        placeholder="Konfirmasi password Anda"
                        required
                    >
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit mt-4">
                    Daftar Sekarang
                </button>
            </form>

            <!-- Divider -->
            <div class="divider">
                <span>Sudah punya akun?</span>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-white/60 text-sm mb-2">
                    <a href="{{ route('login') }}" class="link-latte font-semibold hover:underline">
                        Masuk ke akun Anda
                    </a>
                </p>
                <p class="text-white/40 text-xs">
                    <a href="/" class="link-secondary">← Kembali ke beranda</a>
                </p>
            </div>
        </div>

        <!-- Footer Text -->
        <div class="text-center mt-8 text-white/30 text-xs pb-6">
            <p>&copy; 2026 {{ config('app.name', 'Brew & Breathe') }}. Semua hak dilindungi.</p>
        </div>
    </div>

</body>
</html>
