<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — {{ config('app.name', 'Brew & Breathe') }}</title>
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

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 20px;
            border: 2px solid rgba(255, 255, 255, 0.05);
            background: linear-gradient(135deg, rgba(198, 168, 139, 0.1), rgba(138, 155, 131, 0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 700;
            color: #c6a88b;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .action-card {
            transition: all 200ms cubic-bezier(0.4, 0, 1, 1);
            cursor: pointer;
        }

        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .action-card:hover .action-icon {
            transform: scale(1.1);
        }

        .action-icon {
            font-size: 32px;
            transition: transform 200ms ease;
        }

        .status-badge {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #8a9b83;
            animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .breadcrumb-link {
            display: inline-flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            transition: color 200ms ease;
        }

        .breadcrumb-link:hover {
            color: #c6a88b;
        }
    </style>
</head>

<body class="antialiased min-h-screen relative overflow-x-hidden noise-bg">

    <!-- Decorative Background Elements -->
    <div class="absolute top-0 right-[-5%] w-[500px] h-[500px] bg-latte/5 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[5%] w-[400px] h-[400px] bg-sage/5 blur-[100px] rounded-full pointer-events-none"></div>

    <!-- Header -->
    <header class="sticky top-0 z-50 border-b border-white/5 bg-espresso/80 backdrop-blur-xl">
        <div class="max-w-4xl mx-auto px-6 h-20 flex items-center justify-between">

            <!-- Breadcrumb -->
            <div class="flex items-center gap-3">
                <a href="/" class="breadcrumb-link">← Kembali</a>
                <span class="text-white/20">|</span>
                <h2 class="text-lg font-bold tracking-tight text-white">Dashboard</h2>
            </div>

            <!-- Logout Button -->
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-full bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-red-300 text-xs font-semibold transition-all border border-red-500/20 hover:border-red-500/40">
                    Logout
                </button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 py-16 px-6 min-h-[calc(100vh-80px)]">
        <div class="max-w-4xl mx-auto">

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-8 p-4 rounded-xl border border-sage/20 bg-sage/10 text-sage text-sm font-medium animate-slideInDown" role="alert">
                    <div class="flex items-start gap-3">
                        <span class="text-base flex-shrink-0">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 p-4 rounded-xl border border-red-500/20 bg-red-500/10 text-red-400 text-sm font-medium animate-slideInDown" role="alert">
                    <div class="flex items-start gap-3">
                        <span class="text-base flex-shrink-0">!</span>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Profile Card -->
            <section class="mb-16 fade-in">
                <div class="rounded-3xl border border-white/5 bg-mocha/50 p-8 md:p-12 backdrop-blur-md glass-card shadow-xl">

                    <!-- Gradient Accent -->
                    <div class="absolute top-0 right-0 w-80 h-80 bg-latte/5 rounded-full blur-3xl pointer-events-none -z-10"></div>

                    <!-- Profile Grid -->
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-8 md:gap-12">

                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="profile-avatar">
                                @if(auth()->user()->profile_picture)
                                    <img src="{{ asset('uploads/profile_pictures/' . auth()->user()->profile_picture) }}" alt="Foto Profil">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                        </div>

                        <!-- Profile Info -->
                        <div class="flex-1 text-center md:text-left">
                            <!-- Greeting -->
                            <div class="flex items-center gap-2 justify-center md:justify-start mb-2">
                                <h1 class="text-3xl md:text-4xl font-bold text-white">{{ auth()->user()->name }}</h1>
                            </div>

                            <!-- Status -->
                            <div class="flex items-center gap-2 justify-center md:justify-start mb-4">
                                <div class="status-badge"></div>
                                <p class="text-white/50 text-sm font-medium">Online</p>
                            </div>

                            <!-- Email -->
                            <p class="text-white/60 text-base mb-4">{{ auth()->user()->email }}</p>

                            <!-- Bio -->
                            @if(auth()->user()->bio)
                                <div class="mb-6 p-4 rounded-2xl border border-white/5 bg-black/30 backdrop-blur-sm">
                                    <p class="text-white/70 text-base italic leading-relaxed">
                                        "{{ auth()->user()->bio }}"
                                    </p>
                                </div>
                            @else
                                <div class="mb-6 p-4 rounded-2xl border border-white/5 bg-black/30 backdrop-blur-sm">
                                    <p class="text-white/50 text-base italic">
                                        Belum ada bio. <a href="{{ route('user.profile') }}" class="text-latte hover:text-white transition-colors">Tambahkan bio Anda</a>
                                    </p>
                                </div>
                            @endif

                            <!-- Member Since -->
                            <p class="text-white/40 text-xs uppercase tracking-widest font-mono">
                                Bergabung sejak {{ auth()->user()->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mt-8 pt-8 border-t border-white/5">
                        <a href="{{ route('user.view-profile') }}" class="px-6 py-2.5 rounded-full bg-white text-espresso text-xs font-bold hover:bg-latte transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            Lihat Profil Lengkap
                        </a>
                        <a href="{{ route('user.profile') }}" class="px-6 py-2.5 rounded-full border border-white/10 bg-black/30 text-white text-xs font-semibold hover:border-latte/40 hover:bg-black/50 transition-all">
                            Edit Profil
                        </a>
                        <a href="#" class="px-6 py-2.5 rounded-full border border-white/10 bg-black/30 text-white text-xs font-semibold hover:border-sage/40 hover:bg-black/50 transition-all">
                            Pengaturan
                        </a>
                    </div>
                </div>
            </section>

            <!-- Quick Actions Section -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-6">Akses Cepat</h2>

                <!-- Actions Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Browse Coffee Shops -->
                    <a href="#" class="action-card rounded-2xl border border-white/5 bg-mocha/50 p-8 backdrop-blur-md glass-card hover:border-white/10">
                        <div class="action-icon mb-4" style="width: 48px; height: 48px; background: linear-gradient(135deg, #c6a88b, #ddbfa3); border-radius: 12px;"></div>
                        <h3 class="text-xl font-bold text-white mb-2">Jelajahi Coffee Shops</h3>
                        <p class="text-white/50 text-sm">Temukan kedai kopi terbaik dengan suasana tenang untuk produktivitas dan keseimbangan mental.</p>
                        <p class="text-white/30 text-xs mt-4 uppercase tracking-widest font-mono">Lihat Daftar →</p>
                    </a>

                    <!-- Community Posts -->
                    <a href="#" class="action-card rounded-2xl border border-white/5 bg-mocha/50 p-8 backdrop-blur-md glass-card hover:border-white/10">
                        <div class="action-icon mb-4" style="width: 48px; height: 48px; background: linear-gradient(135deg, #8a9b83, #a8b89a); border-radius: 12px;"></div>
                        <h3 class="text-xl font-bold text-white mb-2">Komunitas & Diskusi</h3>
                        <p class="text-white/50 text-sm">Bergabung dengan komunitas, baca postingan, dan bagikan pengalaman dengan sesama pecinta kopi.</p>
                        <p class="text-white/30 text-xs mt-4 uppercase tracking-widest font-mono">Lihat Komunitas →</p>
                    </a>

                    <!-- Social Battery Tracker -->
                    <a href="#" class="action-card rounded-2xl border border-white/5 bg-mocha/50 p-8 backdrop-blur-md glass-card hover:border-white/10">
                        <div class="action-icon mb-4" style="width: 48px; height: 48px; background: linear-gradient(135deg, #ddbfa3, #e8cdb4); border-radius: 12px;"></div>
                        <h3 class="text-xl font-bold text-white mb-2">Monitor Energi Sosial</h3>
                        <p class="text-white/50 text-sm">Pantau tingkat energi sosial Anda dan dapatkan rekomendasi ruang yang paling sesuai dengan kondisi mental Anda.</p>
                        <p class="text-white/30 text-xs mt-4 uppercase tracking-widest font-mono">Mulai Tracking →</p>
                    </a>

                    <!-- My Insights -->
                    <a href="#" class="action-card rounded-2xl border border-white/5 bg-mocha/50 p-8 backdrop-blur-md glass-card hover:border-white/10">
                        <div class="action-icon mb-4" style="width: 48px; height: 48px; background: linear-gradient(135deg, #b8a89b, #c9b8aa); border-radius: 12px;"></div>
                        <h3 class="text-xl font-bold text-white mb-2">Analytics & Insights</h3>
                        <p class="text-white/50 text-sm">Lihat pola produktivitas Anda, preferensi ruang terbaik, dan dapatkan insights untuk kesehatan mental yang lebih baik.</p>
                        <p class="text-white/30 text-xs mt-4 uppercase tracking-widest font-mono">Lihat Insights →</p>
                    </a>

                </div>
            </section>

            <!-- Secondary CTA -->
            <section class="mt-16 text-center">
                <p class="text-white/50 mb-4">Ingin kembali ke landing page?</p>
                <a href="/" class="inline-block px-6 py-2.5 rounded-full border border-white/10 bg-black/30 text-white text-xs font-semibold hover:border-latte/40 hover:bg-black/50 transition-all">
                    ← Kembali ke Beranda
                </a>
            </section>

        </div>
    </main>

</body>
</html>
