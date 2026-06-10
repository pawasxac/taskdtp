<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — {{ config('app.name', 'Brew & Breathe') }}</title>
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
            margin: 32px 0;
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

        .success-box {
            padding: 12px 16px;
            background-color: rgba(138, 155, 131, 0.1);
            border: 1px solid rgba(138, 155, 131, 0.2);
            border-radius: 12px;
            color: #8a9b83;
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
    <div class="absolute top-[-15%] right-[5%] w-[300px] h-[300px] bg-latte/3 blur-[100px] rounded-full pointer-events-none"></div>

    <!-- Container -->
    <div class="w-full max-w-md px-6 relative z-10">

        <!-- Card -->
        <div class="rounded-2xl border border-white/5 bg-mocha/50 p-8 md:p-10 backdrop-blur-md glass-card shadow-2xl fade-in">

            <!-- Header -->
            <div class="text-center mb-8">
                <a href="/" class="inline-block mb-6">
                    <div class="logo-box" style="background: linear-gradient(135deg, rgba(198, 168, 139, 0.2), rgba(198, 168, 139, 0.05)); border: 1.5px solid rgba(198, 168, 139, 0.3);">
                        <span style="font-size: 28px; font-weight: 700; background: linear-gradient(135deg, #c6a88b, #ddbfa3); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">B</span>
                    </div>
                </a>
                <h1 class="text-3xl font-bold text-white mb-2">Brew & Breathe</h1>
                <p class="text-white/50 text-sm">Platform komunitas coffee shop untuk ketenangan mental</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="error-box">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('error'))
                <div class="error-box">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="success-box">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.post') }}" novalidate>
                @csrf

                <!-- Email/Username Field -->
                <div class="form-group">
                    <label class="form-label">Email atau Username</label>
                    <input
                        type="text"
                        name="login"
                        class="form-input"
                        placeholder="user1@example.com"
                        required
                        autofocus
                        value="{{ old('login') }}"
                    >
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-input"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit mt-4">
                    Masuk ke Dashboard
                </button>
            </form>

            <!-- Divider -->
            <div class="divider">
                <span>Tidak punya akun?</span>
            </div>

            <!-- Demo Credentials Section -->
            <div style="padding: 16px; background-color: rgba(198, 168, 139, 0.05); border: 1px solid rgba(198, 168, 139, 0.15); border-radius: 12px; margin-bottom: 20px;">
                <p style="font-size: 12px; color: #c6a88b; font-weight: 600; text-transform: uppercase; margin-bottom: 10px;">Demo Accounts</p>
                <div style="font-size: 13px; color: #f4f4f5; line-height: 1.6;">
                    <p style="margin-bottom: 6px;"><strong>Admin:</strong> admin@dailycoffee.com</p>
                    <p style="margin-bottom: 10px; color: rgba(255, 255, 255, 0.5);">Password: admin123</p>
                    <p style="margin-bottom: 6px; padding-top: 6px; border-top: 1px solid rgba(198, 168, 139, 0.1);"><strong>User:</strong> user1@example.com</p>
                    <p style="color: rgba(255, 255, 255, 0.5);">Password: password</p>
                </div>
            </div>
        </div>

        <!-- Footer Text -->
        <div class="text-center mt-8 text-white/30 text-xs">
            <p>&copy; 2026 {{ config('app.name', 'Brew & Breathe') }}. Semua hak dilindungi.</p>
        </div>
    </div>

</body>
</html>
