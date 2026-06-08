@extends('admin.layouts.main')

@section('title', 'Dashboard')

@section('content')

<!-- Section Header -->
<div class="premium-section-header">
    <h2>Dashboard Admin</h2>
    <p>Ringkasan metrik platform dan status sistem real-time Brew & Breathe</p>
</div>

<!-- Main Stats Grid -->
<div class="premium-grid cols-4">
    <div class="premium-stat">
        <h4>☕ Coffee Shops</h4>
        <p>{{ \App\Models\CoffeeShop::count() }}</p>
    </div>

    <div class="premium-stat secondary">
        <h4>👥 Total Users</h4>
        <p>{{ \App\Models\User::count() }}</p>
    </div>

    <div class="premium-stat accent">
        <h4>🏘️ Komunitas</h4>
        <p>{{ \App\Models\Komunitas::count() }}</p>
    </div>

    <div class="premium-stat">
        <h4>⭐ Avg Rating</h4>
        <p>{{ number_format(\App\Models\CoffeeShop::avg('rating'), 1) ?? 0 }}/5</p>
    </div>

    <div class="premium-stat secondary">
        <h4>💬 Reviews</h4>
        <p>{{ \App\Models\CoffeeShopReview::count() }}</p>
    </div>

    <div class="premium-stat accent">
        <h4>🤝 Anggota Komunitas</h4>
        <p>{{ \App\Models\CommunityMember::count() }}</p>
    </div>

    <div class="premium-stat">
        <h4>📝 Community Posts</h4>
        <p>{{ \App\Models\CommunityPost::count() }}</p>
    </div>

    <div class="premium-stat secondary">
        <h4>🎯 Gathering Request</h4>
        <p>{{ \App\Models\GatheringRequest::count() }}</p>
    </div>
</div>

<!-- Platform Overview Section -->
<div class="premium-section-header" style="margin-top: 48px; margin-bottom: 24px;">
    <h2>Pusat Kontrol</h2>
    <p>Kelola semua aspek platform dari satu tempat</p>
</div>

<!-- Main Action Menu -->
<div class="premium-action-menu">
    <a href="{{ route('coffee.index') }}" class="premium-action-card">
        <h3>☕ Coffee Shops</h3>
        <p>Kelola data kedai kopi dengan informasi lengkap, lokasi, tingkat kebisingan, dan deskripsi</p>
    </a>

    <a href="{{ route('komunitas.index') }}" class="premium-action-card">
        <h3>🏘️ Komunitas</h3>
        <p>Kelola komunitas, anggota, dan struktur organisasi komunitas</p>
    </a>

    <a href="{{ route('community-posts.index') }}" class="premium-action-card">
        <h3>📝 Community Posts</h3>
        <p>Moderasi dan kelola postingan komunitas untuk menjaga kualitas konten</p>
    </a>

    <a href="{{ route('gathering-requests.index') }}" class="premium-action-card">
        <h3>🎯 Gathering Requests</h3>
        <p>Kelola permintaan dan penjadwalan event gathering komunitas</p>
    </a>

    <a href="{{ route('coffee-shop-reviews.index') }}" class="premium-action-card">
        <h3>⭐ Reviews</h3>
        <p>Monitor dan kelola review coffee shop dari pengguna</p>
    </a>

    <a href="{{ route('admin.login.monitor') }}" class="premium-action-card">
        <h3>👤 User Management</h3>
        <p>Kelola akun user, role, permission, dan aktivitas login</p>
    </a>

    <a href="{{ route('kecamatan.index') }}" class="premium-action-card">
        <h3>📍 Kecamatan</h3>
        <p>Kelola data wilayah dan distribusi geografis platform</p>
    </a>

    <a href="{{ route('community-members.index') }}" class="premium-action-card">
        <h3>🤝 Community Members</h3>
        <p>Lihat daftar anggota komunitas dan status keanggotaan</p>
    </a>
</div>

<!-- System Analytics & Info -->
<div class="premium-section-header" style="margin-top: 48px; margin-bottom: 24px;">
    <h2>Informasi Sistem</h2>
    <p>Detail dan statistik platform Brew & Breathe</p>
</div>

<div class="premium-grid cols-2">
    <div class="premium-card">
        <div class="premium-card-title">📊 Tentang Platform</div>
        <p>
            <strong>Brew & Breathe</strong> adalah platform terpadu untuk menemukan kedamaian mental melalui rekomendasi coffee shop berkualitas. Platform ini dirancang khusus untuk komunitas yang peduli kesehatan mental di wilayah Sidoarjo dan Surabaya.
            <br><br>
            <strong>Fitur Utama:</strong>
        </p>
        <ul style="margin: 12px 0 0 0; padding-left: 20px; color: #666;">
            <li>Smart discovery coffee shop dengan zona sunyi</li>
            <li>Social Battery tracking untuk energi sosial</li>
            <li>Community hub untuk networking dan sharing</li>
            <li>Gathering event management</li>
            <li>Sistem review dan rating yang kredibel</li>
            <li>Analytics untuk pemahaman pola pengguna</li>
        </ul>
    </div>

    <div class="premium-card">
        <div class="premium-card-title">🔐 Security & Privacy</div>
        <p>
            Data pengguna dilindungi dengan enkripsi tingkat enterprise dan sistem keamanan berlapis. Platform mematuhi standar privasi internasional.
            <br><br>
            <strong>Keamanan Data:</strong>
        </p>
        <ul style="margin: 12px 0 0 0; padding-left: 20px; color: #666;">
            <li>✓ End-to-end encryption</li>
            <li>✓ Secure authentication</li>
            <li>✓ Regular security audits</li>
            <li>✓ GDPR compliant</li>
            <li>✓ Data backup harian</li>
        </ul>
    </div>
</div>

<!-- Quick Stats for Admins -->
<div class="premium-section-header" style="margin-top: 48px; margin-bottom: 24px;">
    <h2>Statistik Lanjutan</h2>
    <p>Data mendalam untuk analisis platform</p>
</div>

<div class="premium-grid cols-3">
    <div class="premium-card">
        <div class="premium-card-title">📈 Engagement Rate</div>
        <p style="font-size: 28px; font-weight: 700; color: #8b6f47; margin: 16px 0;">
            @php
                $totalUsers = \App\Models\User::count();
                $totalPosts = \App\Models\CommunityPost::count();
                $engagementRate = $totalUsers > 0 ? round(($totalPosts / $totalUsers) * 100, 1) : 0;
            @endphp
            {{ $engagementRate }}%
        </p>
        <p style="font-size: 13px; color: #666;">Rasio postingan terhadap pengguna</p>
    </div>

    <div class="premium-card">
        <div class="premium-card-title">⭐ Avg Shop Rating</div>
        <p style="font-size: 28px; font-weight: 700; color: #8b6f47; margin: 16px 0;">
            @php
                $avgRating = \App\Models\CoffeeShop::avg('rating') ?? 0;
                $totalShops = \App\Models\CoffeeShop::count() ?? 1;
            @endphp
            {{ number_format($avgRating, 2) }}
        </p>
        <p style="font-size: 13px; color: #666;">dari {{ $totalShops }} coffee shops</p>
    </div>

    <div class="premium-card">
        <div class="premium-card-title">👥 Member Activity</div>
        <p style="font-size: 28px; font-weight: 700; color: #8b6f47; margin: 16px 0;">
            @php
                $memberCount = \App\Models\CommunityMember::count();
                $communityCount = \App\Models\Komunitas::count();
                $avgPerCommunity = $communityCount > 0 ? round($memberCount / $communityCount) : 0;
            @endphp
            {{ $avgPerCommunity }}
        </p>
        <p style="font-size: 13px; color: #666;">member per komunitas</p>
    </div>
</div>

<!-- Work Features Section -->
<div class="premium-section-header" style="margin-top: 48px; margin-bottom: 24px;">
    <h2>Fitur Work & Collaboration</h2>
    <p>Tools untuk tim bekerja lebih efisien</p>
</div>

<div class="premium-action-menu">
    <div class="premium-action-card" style="pointer-events: none; opacity: 0.9;">
        <h3>📋 Task Management</h3>
        <p>Buat dan kelola task untuk pengelolaan coffee shop, komunitas, dan event. Assign ke tim dan track progress real-time.</p>
    </div>

    <div class="premium-action-card" style="pointer-events: none; opacity: 0.9;">
        <h3>💬 Team Chat</h3>
        <p>Komunikasi langsung antar anggota tim untuk koordinasi dan diskusi tentang platform.</p>
    </div>

    <div class="premium-action-card" style="pointer-events: none; opacity: 0.9;">
        <h3>📅 Calendar & Events</h3>
        <p>Kelola jadwal gathering events, meeting tim, dan deadline penting untuk platform.</p>
    </div>

    <div class="premium-action-card" style="pointer-events: none; opacity: 0.9;">
        <h3>📊 Analytics Dashboard</h3>
        <p>Lihat metrics mendalam tentang user engagement, coffee shop popularity, dan community growth.</p>
    </div>

    <div class="premium-action-card" style="pointer-events: none; opacity: 0.9;">
        <h3>🎯 Content Management</h3>
        <p>Kelola featured coffee shops, community highlights, dan konten promosi platform.</p>
    </div>

    <div class="premium-action-card" style="pointer-events: none; opacity: 0.9;">
        <h3>⚙️ System Settings</h3>
        <p>Atur konfigurasi platform, kategori, tags, dan sistem moderasi konten.</p>
    </div>
</div>

<!-- Footer Card -->
<div class="premium-card" style="margin-top: 48px; background: linear-gradient(135deg, rgba(139, 111, 71, 0.08) 0%, rgba(200, 168, 120, 0.04) 100%);">
    <div class="premium-card-title">🚀 Roadmap</div>
    <p>
        Platform terus berkembang dengan fitur-fitur baru. Fitur work seperti task management, team collaboration, dan advanced analytics akan diluncurkan untuk meningkatkan efisiensi manajemen platform.
        <br><br>
        Untuk pertanyaan atau feedback, hubungi tim development Brew & Breathe.
    </p>
</div>

<style>
    .premium-action-card ul {
        list-style: none;
        padding: 0;
    }

    .premium-card ul li {
        margin: 8px 0;
        display: flex;
        align-items: center;
    }

    .premium-card ul li::before {
        content: '';
        width: 6px;
        height: 6px;
        background: #8b6f47;
        border-radius: 50%;
        margin-right: 10px;
    }
</style>

@endsection
