<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGOPI. — Arsitektur Nongkrong</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Clash+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Theme Colors */
        :root {
            --color-alabaster: #FBF9F6;
            --color-espresso: #160F0B;
            --color-border: #E6E1DA;
        }
        
        html, body {
            background-color: var(--color-alabaster);
            color: var(--color-espresso);
            font-family: 'Space Mono', monospace;
            overflow-x: hidden;
        }
        
        .font-clash { font-family: 'Clash Display', sans-serif; }
        .font-mono { font-family: 'Space Mono', monospace; }
        
        /* Preloader */
        #preloader {
            position: fixed;
            inset: 0;
            background-color: var(--color-espresso);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: transform 1.2s cubic-bezier(0.85, 0, 0.15, 1), opacity 1.2s cubic-bezier(0.85, 0, 0.15, 1);
            transform-origin: top;
        }
        #preloader.loaded {
            transform: translateY(-100%);
            opacity: 0;
            pointer-events: none;
        }
        .cup-wrapper { width: 80px; height: 80px; position: relative; }
        .cup-outline {
            fill: none;
            stroke: var(--color-alabaster);
            stroke-width: 3;
            stroke-dasharray: 200;
            stroke-dashoffset: 200;
            animation: drawCup 1.5s ease-in-out forwards;
        }
        .cup-fill {
            fill: var(--color-alabaster);
            transform-origin: bottom;
            transform: scaleY(0) translateY(12px); /* adjusted for bottom filling */
            animation: fillCup 1.5s ease-in-out 0.5s forwards;
        }
        @keyframes drawCup { to { stroke-dashoffset: 0; } }
        @keyframes fillCup { to { transform: scaleY(1) translateY(0); } }

        /* Cursor Graceful Degradation */
        @media (pointer: fine) {
            body, a, button { cursor: none; }
            #custom-cursor-dot, #custom-cursor-ring { display: block; }
        }
        @media (pointer: coarse) {
            #custom-cursor-dot, #custom-cursor-ring { display: none !important; }
        }
        
        #custom-cursor-dot {
            position: fixed; top: 0; left: 0;
            width: 6px; height: 6px;
            background-color: var(--color-alabaster);
            border-radius: 50%; pointer-events: none;
            z-index: 99999; mix-blend-mode: difference;
            transform: translate(-50%, -50%);
            will-change: transform;
        }
        
        #custom-cursor-ring {
            position: fixed; top: 0; left: 0;
            width: 40px; height: 40px;
            border: 1px solid var(--color-alabaster);
            border-radius: 50%; pointer-events: none;
            z-index: 99998; mix-blend-mode: difference;
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease, border-width 0.3s ease, background-color 0.3s ease, transform 0.1s linear;
            will-change: transform, width, height;
        }
        
        #custom-cursor-ring.magnetic {
            width: 60px; height: 60px;
            background-color: rgba(251, 249, 246, 0.1);
            border-width: 2px;
        }
        
        /* Direction Aware Navbar Underline */
        .nav-link {
            position: relative; display: inline-block; overflow: hidden;
        }
        .nav-link::after {
            content: ''; position: absolute; bottom: -2px; left: 0;
            width: 100%; height: 1px; background-color: currentColor;
            transform: scaleX(0); transform-origin: right;
            transition: transform 0.4s cubic-bezier(0.86, 0, 0.07, 1);
        }
        .nav-link:hover::after {
            transform: scaleX(1); transform-origin: left;
        }

        /* Backdrop Blur for Header */
        .header-blur {
            backdrop-filter: blur(8px);
            background-color: rgba(251, 249, 246, 0.8);
            transition: transform 0.5s ease-in-out;
        }

        /* Button Styling & Shimmer */
        .btn-premium {
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.4s ease, background-color 0.3s ease, color 0.3s ease;
            position: relative; z-index: 50; overflow: hidden;
        }
        .btn-premium::before {
            content: ''; position: absolute; top: 0; left: -100%;
            width: 50%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: skewX(-20deg); transition: none;
            mix-blend-mode: overlay; pointer-events: none;
        }
        .btn-premium:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 20px rgba(22, 15, 11, 0.1);
        }
        .btn-premium.dark:hover {
            box-shadow: 0 10px 20px rgba(22, 15, 11, 0.2);
        }
        .btn-premium:hover::before {
            left: 150%; transition: left 0.7s ease-in-out;
        }
        
        .btn-premium-outline:hover {
            background-color: var(--color-espresso);
            color: var(--color-alabaster) !important;
        }

        /* Scroll Reveal Elements */
        .reveal-up {
            opacity: 0; transform: translateY(40px);
            transition: opacity 0.8s cubic-bezier(0.21, 0.47, 0.32, 0.98), transform 0.8s cubic-bezier(0.21, 0.47, 0.32, 0.98);
        }
        .reveal-up.active {
            opacity: 1; transform: translateY(0);
        }
        .stagger-1 { transition-delay: 0.1s; }
        .stagger-2 { transition-delay: 0.2s; }
        .stagger-3 { transition-delay: 0.3s; }
        .stagger-4 { transition-delay: 0.4s; }
        
        /* 3D Tilt Wrapper */
        .tilt-card-wrapper {
            perspective: 1000px; transform-style: preserve-3d;
        }
        .tilt-card-inner {
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            height: 100%; display: flex; flex-direction: column;
            background: var(--color-alabaster);
        }
        .tilt-card-inner:hover {
            /* Transition is handled by JS on move, we only want transition on leave */
        }
        .tilt-card-img-wrapper { overflow: hidden; flex-shrink: 0; }
        .tilt-card-img-wrapper img {
            transition: transform 0.8s cubic-bezier(0.21, 0.47, 0.32, 0.98);
            transform-origin: center;
        }
        .tilt-card-wrapper:hover .tilt-card-img-wrapper img {
            transform: scale(1.08);
        }

        /* Modal Backdrop */
        .modal-backdrop {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(22, 15, 11, 0.6); z-index: 50;
            display: none; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.4s ease;
        }
        .modal-backdrop.active { display: flex; opacity: 1; }
        
        .modal-content {
            background-color: var(--color-alabaster);
            border: 1px solid var(--color-border);
            padding: 40px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;
            transform: translateY(20px); transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .modal-backdrop.active .modal-content { transform: translateY(0); }

        .grid-cols-cafe {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;
        }
    </style>
</head>
<body class="bg-alabaster">
    <!-- Preloader -->
    <div id="preloader">
        <div class="cup-wrapper">
            <svg viewBox="0 0 64 64" width="80" height="80">
                <path class="cup-outline" d="M16 12v24c0 8.837 7.163 16 16 16s16-7.163 16-16V12H16zm32 8h4c4.418 0 8 3.582 8 8s-3.582 8-8 8h-4"/>
                <path class="cup-fill" d="M16 36c0 8.837 7.163 16 16 16s16-7.163 16-16H16z"/>
            </svg>
        </div>
        <p class="font-mono text-xs mt-6" style="color: var(--color-alabaster); letter-spacing: 0.2em;">BREWING EXPERIENCE...</p>
    </div>

    <!-- Custom Cursor -->
    <div id="custom-cursor-dot"></div>
    <div id="custom-cursor-ring"></div>
    
    <!-- Header -->
    <header class="header-blur fixed top-0 left-0 right-0 z-40 border-b border-[#E6E1DA]" id="header">
        <div class="max-w-full px-6 md:px-12 py-4 flex items-center justify-between">
            <div class="flex-1">
                <h1 class="font-clash text-2xl md:text-3xl font-bold magnetic-target" style="color: #160F0B; display: inline-block;">NGOPI.</h1>
            </div>
            
            <nav class="flex-1 hidden md:flex items-center justify-center gap-8">
                <a href="#directory" class="nav-link font-mono text-sm transition z-50 magnetic-target" style="color: #160F0B;">Direktori</a>
                <a href="#forum" class="nav-link font-mono text-sm transition z-50 magnetic-target" style="color: #160F0B;">Forum</a>
            </nav>
            
            <div class="flex-1 flex items-center justify-end gap-4">
                <a href="/login" class="btn-premium btn-premium-outline font-mono text-sm px-4 py-2 border border-[#160F0B] z-50 magnetic-target" style="color: #160F0B;">MASUK</a>
                <a href="/register" class="btn-premium dark font-mono text-sm px-6 py-2 z-50 magnetic-target" style="background-color: #160F0B; color: #FBF9F6;">DAFTAR</a>
            </div>
        </div>
    </header>
    
    <!-- Hero Section -->
    <section class="pt-24 md:pt-32 pb-12 md:pb-20 px-6 md:px-12">
        <div class="max-w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center border-b border-[#E6E1DA] pb-12 md:pb-20">
                <!-- Left: Typography -->
                <div class="flex flex-col justify-center relative">
                    <h2 class="font-clash text-4xl md:text-6xl lg:text-7xl font-bold leading-tight mb-6 reveal-up stagger-1" style="color: #160F0B; letter-spacing: -0.02em;">
                        ARSITEKTUR NONGKRONG
                    </h2>
                    <p class="font-mono text-sm md:text-base mb-8 reveal-up stagger-2" style="color: #160F0B; opacity: 0.7;">
                        SURABAYA - SIDOARJO
                    </p>
                    <p class="font-mono text-xs md:text-sm leading-relaxed mb-8 reveal-up stagger-3" style="color: #160F0B; opacity: 0.6; max-width: 80%;">
                        Jelajahi ruang nongkrong terbaik, komunitas coffee enthusiast, dan forum diskusi untuk menemukan destinasi sempurna Anda.
                    </p>
                </div>
                
                <!-- Right: Image -->
                <div class="w-full aspect-[4/3] overflow-hidden rounded-none border border-[#E6E1DA] reveal-up stagger-4 relative">
                    <img id="hero-img"
                        src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80&w=1200" 
                        alt="Coffee Culture Surabaya" 
                        class="w-full h-full object-cover"
                        style="transform: scale(1.1); will-change: transform;"
                    />
                </div>
            </div>
        </div>
    </section>
    
    <!-- Directory Section -->
    <section id="directory" class="py-16 md:py-24 px-6 md:px-12 border-b border-[#E6E1DA]">
        <div class="max-w-full">
            <h3 class="font-clash text-4xl md:text-6xl font-bold mb-12 md:mb-16 reveal-up" style="color: #160F0B; letter-spacing: -0.02em;">
                DIREKTORI RUANG
            </h3>
            
            <div class="grid-cols-cafe">
                <!-- Card 1: Volks Coffee -->
                <div class="tilt-card-wrapper reveal-up stagger-1">
                    <div class="tilt-card-inner border border-[#E6E1DA] overflow-hidden flex flex-col magnetic-target">
                        <div class="w-full aspect-square tilt-card-img-wrapper">
                            <img 
                                src="https://images.unsplash.com/photo-1515694346937-94d85e41e6f0?auto=format&fit=crop&q=80&w=600" 
                                alt="Volks Coffee" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h4 class="font-clash text-xl md:text-2xl font-bold mb-2" style="color: #160F0B;">VOLKS COFFEE</h4>
                            <p class="font-mono text-xs mb-2" style="opacity: 0.6;">Jl. Pemuda No. 118, Surabaya</p>
                            <p class="font-mono text-xs mb-4" style="opacity: 0.7;">Specialty Grade • Modern Brutalist</p>
                            <button class="btn-premium btn-premium-outline mt-auto font-mono text-sm px-4 py-2 border border-[#160F0B] z-50 cafe-detail-btn" data-cafe="volks" style="color: #160F0B;">
                                LIHAT DETAIL →
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Card 2: Kopitagram -->
                <div class="tilt-card-wrapper reveal-up stagger-2">
                    <div class="tilt-card-inner border border-[#E6E1DA] overflow-hidden flex flex-col magnetic-target">
                        <div class="w-full aspect-square tilt-card-img-wrapper">
                            <img 
                                src="https://images.unsplash.com/photo-1495474472902-4d71bcdd2085?auto=format&fit=crop&q=80&w=600" 
                                alt="Kopitagram" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h4 class="font-clash text-xl md:text-2xl font-bold mb-2" style="color: #160F0B;">KOPITAGRAM</h4>
                            <p class="font-mono text-xs mb-2" style="opacity: 0.6;">Jl. Tunjungan No. 87, Surabaya</p>
                            <p class="font-mono text-xs mb-4" style="opacity: 0.7;">Premium Grade • Minimalist Design</p>
                            <button class="btn-premium btn-premium-outline mt-auto font-mono text-sm px-4 py-2 border border-[#160F0B] z-50 cafe-detail-btn" data-cafe="kopitagram" style="color: #160F0B;">
                                LIHAT DETAIL →
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Card 3: Moengkopi -->
                <div class="tilt-card-wrapper reveal-up stagger-3">
                    <div class="tilt-card-inner border border-[#E6E1DA] overflow-hidden flex flex-col magnetic-target">
                        <div class="w-full aspect-square tilt-card-img-wrapper">
                            <img 
                                src="https://images.unsplash.com/photo-1501339847302-ac426a36c72d?auto=format&fit=crop&q=80&w=600" 
                                alt="Moengkopi" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h4 class="font-clash text-xl md:text-2xl font-bold mb-2" style="color: #160F0B;">MOENGKOPI</h4>
                            <p class="font-mono text-xs mb-2" style="opacity: 0.6;">Jl. Genteng Kali No. 56, Surabaya</p>
                            <p class="font-mono text-xs mb-4" style="opacity: 0.7;">Artisan Grade • Heritage Vibes</p>
                            <button class="btn-premium btn-premium-outline mt-auto font-mono text-sm px-4 py-2 border border-[#160F0B] z-50 cafe-detail-btn" data-cafe="moengkopi" style="color: #160F0B;">
                                LIHAT DETAIL →
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Card 4: Titik Koma -->
                <div class="tilt-card-wrapper reveal-up stagger-4">
                    <div class="tilt-card-inner border border-[#E6E1DA] overflow-hidden flex flex-col magnetic-target">
                        <div class="w-full aspect-square tilt-card-img-wrapper">
                            <img 
                                src="https://images.unsplash.com/photo-1509785307050-d4066910ec1e?auto=format&fit=crop&q=80&w=600" 
                                alt="Titik Koma" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h4 class="font-clash text-xl md:text-2xl font-bold mb-2" style="color: #160F0B;">TITIK KOMA</h4>
                            <p class="font-mono text-xs mb-2" style="opacity: 0.6;">Jl. Basuki Rachmat No. 234, Sidoarjo</p>
                            <p class="font-mono text-xs mb-4" style="opacity: 0.7;">Contemporary • Type-Focused</p>
                            <button class="btn-premium btn-premium-outline mt-auto font-mono text-sm px-4 py-2 border border-[#160F0B] z-50 cafe-detail-btn" data-cafe="titik-koma" style="color: #160F0B;">
                                LIHAT DETAIL →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="py-16 md:py-24 px-6 md:px-12 border-t border-[#E6E1DA] reveal-up">
        <div class="max-w-full">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-24">
                <!-- Map -->
                <div class="md:col-span-1 reveal-up stagger-1">
                    <div class="w-full aspect-square border border-[#E6E1DA] overflow-hidden magnetic-target tilt-card-wrapper">
                        <div class="tilt-card-inner">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.6968626181636!2d112.73814!3d-7.25045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fb69381c14b5%3A0x542c949e7e2dd80d!2sSurabaya%2C%20East%20Java!5e0!3m2!1sen!2sid!4v1234567890" 
                                width="100%" height="100%" style="border: none; filter: grayscale(100%); pointer-events: none;" allowfullscreen="" loading="lazy">
                            </iframe>
                        </div>
                    </div>
                </div>
                
                <!-- Links -->
                <div class="md:col-span-1 reveal-up stagger-2">
                    <h4 class="font-clash text-lg font-bold mb-6" style="color: #160F0B;">LEGAL</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="nav-link font-mono text-xs z-50 transition magnetic-target" style="color: #160F0B;">PRIVACY POLICY</a></li>
                        <li><a href="#" class="nav-link font-mono text-xs z-50 transition magnetic-target" style="color: #160F0B;">TERMS OF SERVICE</a></li>
                        <li><a href="#" class="nav-link font-mono text-xs z-50 transition magnetic-target" style="color: #160F0B;">COMMUNITY GUIDELINES</a></li>
                    </ul>
                </div>
                
                <!-- Contact & Copyright -->
                <div class="md:col-span-1 reveal-up stagger-3">
                    <h4 class="font-clash text-lg font-bold mb-6" style="color: #160F0B;">KONTAK</h4>
                    <p class="font-mono text-xs mb-4" style="opacity: 0.7;">
                        COMM@NGOPI.ID<br>
                        +62 811 0000 0000
                    </p>
                    <p class="font-mono text-xs" style="opacity: 0.5;">
                        ©2026 NGOPI. ALL RIGHTS RESERVED.
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Modal -->
    <div id="detailModal" class="modal-backdrop">
        <div class="modal-content">
            <div class="flex justify-between items-start mb-6">
                <h3 id="modalTitle" class="font-clash text-2xl font-bold" style="color: #160F0B;">VOLKS COFFEE</h3>
                <button class="btn-premium btn-premium-outline font-mono text-sm z-50 border border-[#160F0B] px-3 py-1 magnetic-target" id="closeModal" style="color: #160F0B;">✕</button>
            </div>
            <p id="modalText" class="font-mono text-sm mb-6" style="opacity: 0.7;"></p>
            <div class="w-full aspect-video border border-[#E6E1DA] mb-6 overflow-hidden">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.6968626181636!2d112.73814!3d-7.25045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fb69381c14b5%3A0x542c949e7e2dd80d!2sSurabaya%2C%20East%20Java!5e0!3m2!1sen!2sid!4v1234567890" 
                    width="100%" height="100%" style="border: none;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
            <button class="btn-premium dark font-mono text-sm px-6 py-2 w-full z-50 magnetic-target" style="background-color: #160F0B; color: #FBF9F6;">
                BUKA LOKASI
            </button>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Preloader Logic
            const preloader = document.getElementById('preloader');
            // Wait slightly past the SVG animation
            setTimeout(() => {
                preloader.classList.add('loaded');
                // Trigger reveals after preloader is gone
                setTimeout(() => {
                    document.querySelectorAll('.reveal-up.stagger-1, .reveal-up.stagger-2, .reveal-up.stagger-3, .reveal-up.stagger-4').forEach(el => {
                        const rect = el.getBoundingClientRect();
                        if (rect.top < window.innerHeight) {
                            el.classList.add('active');
                        }
                    });
                }, 400);
            }, 2500);

            // 2. Cursor Logic (Graceful Degradation)
            const isFinePointer = window.matchMedia("(pointer: fine)").matches;
            if (isFinePointer) {
                const dot = document.getElementById('custom-cursor-dot');
                const ring = document.getElementById('custom-cursor-ring');
                
                let mouseX = window.innerWidth / 2;
                let mouseY = window.innerHeight / 2;
                let ringX = mouseX;
                let ringY = mouseY;
                
                // Track mouse
                document.addEventListener('mousemove', (e) => {
                    mouseX = e.clientX;
                    mouseY = e.clientY;
                    dot.style.transform = `translate(${mouseX}px, ${mouseY}px) translate(-50%, -50%)`;
                });
                
                // Ring follow logic (requestAnimationFrame for smooth lag)
                const render = () => {
                    ringX += (mouseX - ringX) * 0.15;
                    ringY += (mouseY - ringY) * 0.15;
                    ring.style.transform = `translate(${ringX}px, ${ringY}px) translate(-50%, -50%)`;
                    requestAnimationFrame(render);
                };
                requestAnimationFrame(render);
                
                // Magnetic targets
                const magneticTargets = document.querySelectorAll('.magnetic-target, .btn-premium');
                magneticTargets.forEach(target => {
                    target.addEventListener('mouseenter', () => {
                        ring.classList.add('magnetic');
                        dot.style.opacity = '0';
                    });
                    target.addEventListener('mouseleave', () => {
                        ring.classList.remove('magnetic');
                        dot.style.opacity = '1';
                        // Reset transforms that might have been added by magnetic pull
                        target.style.transform = '';
                    });
                    
                    // Button pull effect
                    if(target.classList.contains('btn-premium') || target.tagName === 'A') {
                        target.addEventListener('mousemove', (e) => {
                            const rect = target.getBoundingClientRect();
                            const x = e.clientX - rect.left - rect.width / 2;
                            const y = e.clientY - rect.top - rect.height / 2;
                            // Only apply if it's a small button, avoid big cards
                            if(rect.width < 300) {
                                target.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px) scale(1.02)`;
                            }
                        });
                    }
                });
            }

            // 3. Scroll Reveal & Parallax
            const revealElements = document.querySelectorAll('.reveal-up');
            const heroImg = document.getElementById('hero-img');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        // Optional: unobserve after reveal
                        // observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });
            
            revealElements.forEach(el => observer.observe(el));
            
            // Parallax
            window.addEventListener('scroll', () => {
                const scrollY = window.scrollY;
                if (scrollY < window.innerHeight) {
                    heroImg.style.transform = `translateY(${scrollY * 0.2}px) scale(1.1)`;
                }
            }, { passive: true });

            // 4. 3D Tilt Logic
            const tiltCards = document.querySelectorAll('.tilt-card-wrapper');
            if (isFinePointer) {
                tiltCards.forEach(card => {
                    const inner = card.querySelector('.tilt-card-inner');
                    
                    card.addEventListener('mousemove', (e) => {
                        const rect = card.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        
                        const centerX = rect.width / 2;
                        const centerY = rect.height / 2;
                        
                        // Limits: +/- 8 degrees
                        const rotateX = ((y - centerY) / centerY) * -8;
                        const rotateY = ((x - centerX) / centerX) * 8;
                        
                        inner.style.transition = 'none'; // remove transition during move
                        inner.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
                    });
                    
                    card.addEventListener('mouseleave', () => {
                        inner.style.transition = 'transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                        inner.style.transform = 'rotateX(0deg) rotateY(0deg)';
                    });
                });
            }

            // 5. Modal Logic
            const modal = document.getElementById('detailModal');
            const closeModalBtn = document.getElementById('closeModal');
            const cafeButtons = document.querySelectorAll('.cafe-detail-btn');
            
            const cafeData = {
                volks: {
                    title: 'VOLKS COFFEE',
                    text: 'Specialty Grade Coffee Roastery di jantung Surabaya. Menawarkan single origin dari berbagai daerah Indonesia. Suasana modern brutalist dengan fokus pada kualitas dan komunitas.'
                },
                kopitagram: {
                    title: 'KOPITAGRAM',
                    text: 'Premium Espresso Bar dengan koleksi third wave coffee terlengkap. Aesthetic Instagram-friendly dengan barista profesional. Sempurna untuk diskusi dan networking.'
                },
                moengkopi: {
                    title: 'MOENGKOPI',
                    text: 'Heritage Coffee Space dengan sentuhan tradisional Indonesia. Melayani kopi tubruk, manual brew, dan espresso. Ruang nyaman untuk komunitas lokal.'
                },
                'titik-koma': {
                    title: 'TITIK KOMA',
                    text: 'Type-Focused Contemporary Coffee Shop. Desain minimalis dengan perpustakaan kopi dan komunitas penulis. Tempat sempurna untuk brainstorming dan kolaborasi kreatif.'
                }
            };
            
            cafeButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault(); // Stop default jump
                    const cafe = btn.dataset.cafe;
                    const data = cafeData[cafe];
                    document.getElementById('modalTitle').textContent = data.title;
                    document.getElementById('modalText').textContent = data.text;
                    modal.classList.add('active');
                    if(isFinePointer) {
                        document.getElementById('custom-cursor-ring').classList.remove('magnetic');
                        document.getElementById('custom-cursor-dot').style.opacity = '1';
                    }
                });
            });
            
            closeModalBtn.addEventListener('click', () => {
                modal.classList.remove('active');
            });
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
