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
        }
        
        /* CRITICAL: Kill default cursor */
        body {
            cursor: none;
        }
        
        a, button {
            cursor: none;
        }
        
        .font-clash {
            font-family: 'Clash Display', sans-serif;
        }
        
        .font-mono {
            font-family: 'Space Mono', monospace;
        }
        
        /* Custom Cursor */
        #custom-cursor {
            pointer-events: none !important;
            z-index: 99999;
            will-change: transform;
        }
        
        /* Backdrop Blur for Header */
        .header-blur {
            backdrop-filter: blur(8px);
            background-color: rgba(251, 249, 246, 0.8);
        }
        
        /* Button Styling */
        .btn-premium {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            z-50;
        }
        
        .btn-premium:hover {
            background-color: var(--color-espresso);
            color: var(--color-alabaster);
        }
        
        /* Modal Backdrop */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(22, 15, 11, 0.6);
            z-index: 50;
            display: none;
            align-items: center;
            justify-content: center;
        }
        
        .modal-backdrop.active {
            display: flex;
        }
        
        .modal-content {
            background-color: var(--color-alabaster);
            border: 1px solid var(--color-border);
            padding: 40px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        /* Grid responsive classes */
        .grid-cols-cafe {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
    </style>
</head>
<body class="bg-alabaster">
    <!-- Custom Cursor -->
    <div id="custom-cursor" class="fixed w-4 h-4 bg-white rounded-full pointer-events-none mix-blend-difference z-[99999] transition-transform duration-75 ease-out" style="transform: translate(-8px, -8px);"></div>
    
    <!-- Header -->
    <header class="header-blur fixed top-0 left-0 right-0 z-40 border-b border-[#E6E1DA]">
        <div class="max-w-full px-6 md:px-12 py-4 flex items-center justify-between">
            <div class="flex-1">
                <h1 class="font-clash text-2xl md:text-3xl font-bold" style="color: #160F0B;">NGOPI.</h1>
            </div>
            
            <nav class="flex-1 hidden md:flex items-center justify-center gap-8">
                <a href="#directory" class="font-mono text-sm hover:opacity-60 transition z-50" style="color: #160F0B;">Direktori</a>
                <a href="#forum" class="font-mono text-sm hover:opacity-60 transition z-50" style="color: #160F0B;">Forum</a>
            </nav>
            
            <div class="flex-1 flex items-center justify-end gap-4">
                <a href="/login" class="btn-premium font-mono text-sm px-4 py-2 border border-[#160F0B] z-50" style="color: #160F0B;">MASUK</a>
                <a href="/register" class="btn-premium font-mono text-sm px-6 py-2 z-50" style="background-color: #160F0B; color: #FBF9F6;">DAFTAR</a>
            </div>
        </div>
    </header>
    
    <!-- Hero Section -->
    <section class="pt-24 md:pt-32 pb-12 md:pb-20 px-6 md:px-12">
        <div class="max-w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center border-b border-[#E6E1DA] pb-12 md:pb-20">
                <!-- Left: Typography -->
                <div class="flex flex-col justify-center">
                    <h2 class="font-clash text-4xl md:text-6xl lg:text-7xl font-bold leading-tight mb-6" style="color: #160F0B; letter-spacing: -0.02em;">
                        ARSITEKTUR NONGKRONG
                    </h2>
                    <p class="font-mono text-sm md:text-base mb-8" style="color: #160F0B; opacity: 0.7;">
                        SURABAYA - SIDOARJO
                    </p>
                    <p class="font-mono text-xs md:text-sm leading-relaxed mb-8" style="color: #160F0B; opacity: 0.6;">
                        Jelajahi ruang nongkrong terbaik, komunitas coffee enthusiast, dan forum diskusi untuk menemukan destinasi sempurna Anda.
                    </p>
                </div>
                
                <!-- Right: Image -->
                <div class="w-full aspect-[4/3] overflow-hidden rounded-none border border-[#E6E1DA]">
                    <img 
                        src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80&w=1200" 
                        alt="Coffee Culture Surabaya" 
                        class="w-full h-full object-cover"
                    />
                </div>
            </div>
        </div>
    </section>
    
    <!-- Directory Section -->
    <section id="directory" class="py-16 md:py-24 px-6 md:px-12 border-b border-[#E6E1DA]">
        <div class="max-w-full">
            <h3 class="font-clash text-4xl md:text-6xl font-bold mb-12 md:mb-16" style="color: #160F0B; letter-spacing: -0.02em;">
                DIREKTORI RUANG
            </h3>
            
            <div class="grid-cols-cafe">
                <!-- Card 1: Volks Coffee -->
                <div class="border border-[#E6E1DA] overflow-hidden flex flex-col">
                    <div class="w-full aspect-square overflow-hidden">
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
                        <button class="btn-premium mt-auto font-mono text-sm px-4 py-2 border border-[#160F0B] z-50 cafe-detail-btn" data-cafe="volks" style="color: #160F0B;">
                            LIHAT DETAIL →
                        </button>
                    </div>
                </div>
                
                <!-- Card 2: Kopitagram -->
                <div class="border border-[#E6E1DA] overflow-hidden flex flex-col">
                    <div class="w-full aspect-square overflow-hidden">
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
                        <button class="btn-premium mt-auto font-mono text-sm px-4 py-2 border border-[#160F0B] z-50 cafe-detail-btn" data-cafe="kopitagram" style="color: #160F0B;">
                            LIHAT DETAIL →
                        </button>
                    </div>
                </div>
                
                <!-- Card 3: Moengkopi -->
                <div class="border border-[#E6E1DA] overflow-hidden flex flex-col">
                    <div class="w-full aspect-square overflow-hidden">
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
                        <button class="btn-premium mt-auto font-mono text-sm px-4 py-2 border border-[#160F0B] z-50 cafe-detail-btn" data-cafe="moengkopi" style="color: #160F0B;">
                            LIHAT DETAIL →
                        </button>
                    </div>
                </div>
                
                <!-- Card 4: Titik Koma -->
                <div class="border border-[#E6E1DA] overflow-hidden flex flex-col">
                    <div class="w-full aspect-square overflow-hidden">
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
                        <button class="btn-premium mt-auto font-mono text-sm px-4 py-2 border border-[#160F0B] z-50 cafe-detail-btn" data-cafe="titik-koma" style="color: #160F0B;">
                            LIHAT DETAIL →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="py-16 md:py-24 px-6 md:px-12 border-t border-[#E6E1DA]">
        <div class="max-w-full">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-24">
                <!-- Map -->
                <div class="md:col-span-1">
                    <div class="w-full aspect-square border border-[#E6E1DA] overflow-hidden">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.6968626181636!2d112.73814!3d-7.25045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fb69381c14b5%3A0x542c949e7e2dd80d!2sSurabaya%2C%20East%20Java!5e0!3m2!1sen!2sid!4v1234567890" 
                            width="100%" 
                            height="100%" 
                            style="border: none; filter: grayscale(100%);" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
                
                <!-- Links -->
                <div class="md:col-span-1">
                    <h4 class="font-clash text-lg font-bold mb-6" style="color: #160F0B;">LEGAL</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="font-mono text-xs z-50 hover:opacity-50 transition" style="color: #160F0B;">PRIVACY POLICY</a></li>
                        <li><a href="#" class="font-mono text-xs z-50 hover:opacity-50 transition" style="color: #160F0B;">TERMS OF SERVICE</a></li>
                        <li><a href="#" class="font-mono text-xs z-50 hover:opacity-50 transition" style="color: #160F0B;">COMMUNITY GUIDELINES</a></li>
                    </ul>
                </div>
                
                <!-- Contact & Copyright -->
                <div class="md:col-span-1">
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
                <button class="btn-premium font-mono text-sm z-50" id="closeModal" style="color: #160F0B;">✕</button>
            </div>
            <p id="modalText" class="font-mono text-sm mb-6" style="opacity: 0.7;"></p>
            <div class="w-full aspect-video border border-[#E6E1DA] mb-6 overflow-hidden">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.6968626181636!2d112.73814!3d-7.25045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fb69381c14b5%3A0x542c949e7e2dd80d!2sSurabaya%2C%20East%20Java!5e0!3m2!1sen!2sid!4v1234567890" 
                    width="100%" 
                    height="100%" 
                    style="border: none;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <button class="btn-premium font-mono text-sm px-6 py-2 w-full z-50" style="background-color: #160F0B; color: #FBF9F6;">
                BUKA LOKASI
            </button>
        </div>
    </div>
    
    <script type="module">
        // Custom Cursor Tracking
        const cursor = document.getElementById('custom-cursor');
        
        document.addEventListener('mousemove', (e) => {
            cursor.style.transform = `translate(${e.clientX - 8}px, ${e.clientY - 8}px)`;
        });
        
        // Modal Logic
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
            btn.addEventListener('click', () => {
                const cafe = btn.dataset.cafe;
                const data = cafeData[cafe];
                document.getElementById('modalTitle').textContent = data.title;
                document.getElementById('modalText').textContent = data.text;
                modal.classList.add('active');
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
    </script>
</body>
</html>
