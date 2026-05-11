<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
</head>
<body>

<div class="navbar">
    <h2>User Dashboard</h2>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="btn btn-danger">Logout</button>
    </form>
</div>

<div class="container">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

<div class="card">
    <div class="profile-header">
        <div class="avatar">
            @if(auth()->user()->profile_picture)
                <img src="{{ asset('uploads/profile_pictures/' . auth()->user()->profile_picture) }}" alt="Foto Profil" class="profile-img" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            @else
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
            @endif
        </div>
        <div>
            <h3>Halo, {{ auth()->user()->name }} 👋</h3>
            <p>Kelola akun kamu dengan aman di sini.</p>
            @if(auth()->user()->bio)
                <p style="font-size: 14px; color: #666; margin-top: 5px;">{{ auth()->user()->bio }}</p>
            @endif
        </div>
    </div>

    <div style="display: flex; gap: 12px; margin-top: 20px;">
        <a href="{{ route('user.view-profile') }}" class="btn btn-primary">👤 Lihat Profil Lengkap</a>
        <a href="{{ route('user.profile') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">✏️ Edit Profil</a>
    </div>
</div>


</div>

</body>
</html>
