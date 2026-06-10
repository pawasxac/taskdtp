<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGOPI. Dashboard — Live Community</title>
    
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
            --color-terracotta: #D4A574;
        }
        
        html, body {
            background-color: var(--color-alabaster);
            color: var(--color-espresso);
            font-family: 'Space Mono', monospace;
            height: 100%;
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
        
        /* Main Layout */
        .dashboard-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        
        .sidebar {
            width: 240px;
            border-right: 1px solid var(--color-border);
            padding: 20px;
            overflow-y: auto;
            background-color: var(--color-alabaster);
        }
        
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .content-header {
            border-bottom: 1px solid var(--color-border);
            padding: 20px 24px;
        }
        
        .content-body {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }
        
        /* Sidebar Styling */
        .user-card {
            border: 1px solid var(--color-border);
            padding: 16px;
            margin-bottom: 20px;
            border-radius: 0;
        }
        
        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 1px solid var(--color-border);
            margin-bottom: 12px;
        }
        
        .user-name {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .user-status {
            font-size: 11px;
            opacity: 0.6;
        }
        
        .sidebar-section {
            margin-bottom: 20px;
        }
        
        .sidebar-section-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
            text-transform: uppercase;
            opacity: 0.6;
        }
        
        .sidebar-link {
            display: block;
            font-size: 12px;
            padding: 8px 0;
            transition: opacity 0.2s;
            cursor: pointer;
            color: var(--color-espresso);
            text-decoration: none;
        }
        
        .sidebar-link:hover {
            opacity: 0.6;
        }
        
        /* Social Buttons */
        .social-btn {
            display: block;
            width: 100%;
            text-align: center;
            font-size: 11px;
            padding: 10px;
            border: 1px solid var(--color-border);
            margin-bottom: 8px;
            transition: all 0.3s;
            cursor: pointer;
            background-color: var(--color-alabaster);
            color: var(--color-espresso);
            text-decoration: none;
            z-index: 50;
        }
        
        .social-btn:hover {
            background-color: var(--color-espresso);
            color: var(--color-alabaster);
        }
        
        /* Chat Messages */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .message {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .message-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--color-border);
            flex-shrink: 0;
            background-color: var(--color-espresso);
            color: var(--color-alabaster);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }
        
        .message-content {
            flex: 1;
        }
        
        .message-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }
        
        .message-username {
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            color: var(--color-espresso);
            z-index: 50;
        }
        
        .message-username:hover {
            opacity: 0.6;
        }
        
        .message-time {
            font-size: 10px;
            opacity: 0.5;
        }
        
        .message-text {
            font-size: 12px;
            line-height: 1.5;
            opacity: 0.8;
        }
        
        /* System Warning Banner */
        .system-warning {
            background-color: var(--color-terracotta);
            color: var(--color-espresso);
            padding: 16px;
            border: 1px solid var(--color-border);
            margin: 16px 0;
            font-size: 11px;
            line-height: 1.6;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        
        /* Chat Input (Disabled State) */
        .chat-input-container {
            border-top: 1px solid var(--color-border);
            padding: 16px;
            display: flex;
            gap: 8px;
        }
        
        .chat-input {
            flex: 1;
            padding: 12px;
            border: 1px solid var(--color-border);
            background-color: var(--color-alabaster);
            color: var(--color-espresso);
            font-family: 'Space Mono', monospace;
            font-size: 12px;
            outline: none;
            transition: border-color 0.2s;
        }
        
        .chat-input:focus {
            border-color: var(--color-espresso);
        }
        
        .chat-input:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        
        .chat-send-btn {
            padding: 12px 20px;
            border: 1px solid var(--color-espresso);
            background-color: var(--color-alabaster);
            color: var(--color-espresso);
            font-family: 'Space Mono', monospace;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 50;
        }
        
        .chat-send-btn:hover {
            background-color: var(--color-espresso);
            color: var(--color-alabaster);
        }
        
        .chat-send-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        
        /* Profile Card Modal */
        .profile-card-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(22, 15, 11, 0.4);
            z-index: 99;
            display: none;
            align-items: flex-start;
            justify-content: flex-start;
            padding-top: 100px;
            padding-left: 24px;
        }
        
        .profile-card-overlay.active {
            display: flex;
        }
        
        .profile-card {
            position: fixed;
            background-color: var(--color-alabaster);
            border: 1px solid var(--color-border);
            padding: 20px;
            width: 300px;
            z-index: 100;
            box-shadow: 0 10px 40px rgba(22, 15, 11, 0.1);
        }
        
        .profile-header {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .profile-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 1px solid var(--color-border);
            background-color: var(--color-espresso);
            color: var(--color-alabaster);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            flex-shrink: 0;
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-name {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .profile-status {
            font-size: 11px;
            opacity: 0.6;
        }
        
        .profile-section-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin-top: 16px;
            margin-bottom: 8px;
            text-transform: uppercase;
            opacity: 0.6;
        }
        
        .profile-link {
            display: block;
            width: 100%;
            text-align: center;
            font-size: 11px;
            padding: 10px;
            border: 1px solid var(--color-border);
            margin-bottom: 8px;
            transition: all 0.3s;
            cursor: pointer;
            background-color: var(--color-alabaster);
            color: var(--color-espresso);
            text-decoration: none;
            z-index: 101;
        }
        
        .profile-link:hover {
            background-color: var(--color-espresso);
            color: var(--color-alabaster);
        }
        
        .profile-close {
            font-size: 11px;
            width: 100%;
            padding: 10px;
            border: 1px solid var(--color-border);
            margin-top: 16px;
            background-color: var(--color-alabaster);
            color: var(--color-espresso);
            cursor: pointer;
            transition: all 0.3s;
            z-index: 101;
        }
        
        .profile-close:hover {
            background-color: var(--color-espresso);
            color: var(--color-alabaster);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 180px;
                padding: 12px;
            }
            
            .profile-card {
                width: 280px;
            }
        }
    </style>
</head>
<body class="bg-alabaster">
    <!-- Custom Cursor -->
    <div id="custom-cursor" class="fixed w-4 h-4 bg-white rounded-full pointer-events-none mix-blend-difference z-[99999]" style="transform: translate(-8px, -8px);"></div>
    
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- User Card -->
            <div class="user-card">
                <div class="user-avatar" style="background: linear-gradient(135deg, #160F0B, #E6E1DA);"></div>
                <div class="user-name">ADITYA SKENA</div>
                <div class="user-status">Status: Sedang di TITIK KOMA</div>
            </div>
            
            <!-- Connect Socials Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Koneksi Langsung</div>
                <a href="#" class="social-btn" title="Instagram DM" onclick="return false;">📷 INSTAGRAM</a>
                <a href="#" class="social-btn" title="Twitter/X DM" onclick="return false;">𝕏 TWITTER</a>
            </div>
            
            <!-- Navigation Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Navigasi</div>
                <a href="#" class="sidebar-link">LIVE FORUM</a>
                <a href="#" class="sidebar-link">BOOKMARK RUANG</a>
                <a href="#" class="sidebar-link">PENGATURAN</a>
            </div>
            
            <!-- Connection Settings Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Pengaturan Koneksi</div>
                <p style="font-size: 11px; opacity: 0.6; line-height: 1.6;">
                    Edit profile Anda untuk menambahkan Discord ID, WhatsApp, dan Instagram handle untuk matchmaking yang lebih baik.
                </p>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="content-header">
                <h1 class="font-clash text-2xl font-bold" style="color: #160F0B; letter-spacing: -0.01em;">LIVE COMMUNITY CHAT</h1>
            </div>
            
            <!-- Chat Body -->
            <div class="content-body" style="display: flex; flex-direction: column;">
                <div class="chat-messages">
                    <!-- Message 1 -->
                    <div class="message">
                        <div class="message-avatar">RA</div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-username user-trigger" data-user-id="radha">@radha_studio</span>
                                <span class="message-time">14:32</span>
                            </div>
                            <div class="message-text">Ada yang nugas di Volks Coffee hari ini? Mau meetup diskusi project.</div>
                        </div>
                    </div>
                    
                    <!-- Message 2 -->
                    <div class="message">
                        <div class="message-avatar" style="background-color: #E6E1DA; color: #160F0B;">BN</div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-username user-trigger" data-user-id="bimo">@bimo_tech</span>
                                <span class="message-time">14:35</span>
                            </div>
                            <div class="message-text">Aku di Kopitagram sekarang. Barusan selesai standup. WiFi bagus, kopi enak banget.</div>
                        </div>
                    </div>
                    
                    <!-- Message 3 -->
                    <div class="message">
                        <div class="message-avatar" style="background-color: #D4A574;">JL</div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-username user-trigger" data-user-id="joli">@joli_freelance</span>
                                <span class="message-time">14:38</span>
                            </div>
                            <div class="message-text">Heading ke Moengkopi, siapa mau join? Tempat sepi tapi suasana heritage banget.</div>
                        </div>
                    </div>
                    
                    <!-- Message 4 -->
                    <div class="message">
                        <div class="message-avatar" style="background-color: #160F0B; color: #FBF9F6;">SK</div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-username user-trigger" data-user-id="suryank">@suryank_dev</span>
                                <span class="message-time">14:41</span>
                            </div>
                            <div class="message-text">@radha_studio count me in! Bimo kamu bisa dari Kopitagram? Or kita semua kumpul di satu tempat?</div>
                        </div>
                    </div>
                    
                    <!-- Message 5 -->
                    <div class="message">
                        <div class="message-avatar" style="background-color: #E6E1DA; color: #160F0B;">BN</div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-username user-trigger" data-user-id="bimo">@bimo_tech</span>
                                <span class="message-time">14:44</span>
                            </div>
                            <div class="message-text">Yess! Titik Koma terdekat dari sini. Let's meetup ada 30 min. Share location lagi!</div>
                        </div>
                    </div>
                    
                    <!-- System Warning Banner -->
                    <div class="system-warning">
                        [ SYS_WARNING: BATAS OBROLAN RINGAN TERCAPAI. UNTUK DISKUSI MENDALAM, KUNJUNGI PROFIL USER DAN HUBUNGKAN VIA DISCORD / WHATSAPP. ]
                    </div>
                </div>
                
                <!-- Chat Input (Disabled) -->
                <div class="chat-input-container">
                    <input 
                        type="text" 
                        class="chat-input" 
                        placeholder="Limit tercapai. Gunakan DM/Sosmed untuk lanjut chat..." 
                        disabled
                    />
                    <button class="chat-send-btn" disabled>SEND</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profile Card Overlay -->
    <div id="profileCardOverlay" class="profile-card-overlay">
        <div class="profile-card" id="profileCard">
            <div class="profile-header">
                <div class="profile-avatar" id="profileCardAvatar">RA</div>
                <div class="profile-info">
                    <div class="profile-name" id="profileCardName">@radha_studio</div>
                    <div class="profile-status" id="profileCardStatus">SEDANG DI: VOLKS COFFEE</div>
                </div>
            </div>
            
            <div class="profile-section-title">Koneksi Langsung</div>
            <a href="#" class="profile-link" onclick="event.preventDefault(); alert('Opening Discord invite...');">BUKA DISCORD</a>
            <a href="#" class="profile-link" onclick="event.preventDefault(); alert('Opening WhatsApp chat...');">CHAT WHATSAPP</a>
            <a href="#" class="profile-link" onclick="event.preventDefault(); alert('Opening Instagram DM...');">INSTAGRAM DM</a>
            
            <button class="profile-close" id="closeProfileCard">TUTUP</button>
        </div>
    </div>
    
    <script type="module">

        const cursor = document.getElementById('custom-cursor');

        document.addEventListener('mousemove', (e) => {
            cursor.style.transform = `translate(${e.clientX - 8}px, ${e.clientY - 8}px)`;
        });


        const userProfiles = {
            radha: {
                name: '@radha_studio',
                status: 'SEDANG DI: VOLKS COFFEE',
                avatar: 'RA',
                avatarBg: '#160F0B'
            },
            bimo: {
                name: '@bimo_tech',
                status: 'SEDANG DI: KOPITAGRAM',
                avatar: 'BN',
                avatarBg: '#E6E1DA'
            },
            joli: {
                name: '@joli_freelance',
                status: 'SEDANG DI: MOENGKOPI',
                avatar: 'JL',
                avatarBg: '#D4A574'
            },
            suryank: {
                name: '@suryank_dev',
                status: 'SEDANG DI: TITIK KOMA',
                avatar: 'SK',
                avatarBg: '#160F0B'
            }
        };


        const profileCardOverlay = document.getElementById('profileCardOverlay');
        const profileCard = document.getElementById('profileCard');
        const closeProfileCardBtn = document.getElementById('closeProfileCard');
        const userTriggers = document.querySelectorAll('.user-trigger');

        userTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const userId = trigger.dataset.userId;
                const user = userProfiles[userId];

                if (user) {
                    document.getElementById('profileCardName').textContent = user.name;
                    document.getElementById('profileCardStatus').textContent = user.status;
                    document.getElementById('profileCardAvatar').textContent = user.avatar;
                    document.getElementById('profileCardAvatar').style.backgroundColor = user.avatarBg;
                    document.getElementById('profileCardAvatar').style.color = user.avatarBg === '#E6E1DA' || user.avatarBg === '#D4A574' ? '#160F0B' : '#FBF9F6';

                    profileCardOverlay.classList.add('active');
                }
            });
        });

        closeProfileCardBtn.addEventListener('click', () => {
            profileCardOverlay.classList.remove('active');
        });

        profileCardOverlay.addEventListener('click', (e) => {
            if (e.target === profileCardOverlay) {
                profileCardOverlay.classList.remove('active');
            }
        });
</script>
</body>
</html>
