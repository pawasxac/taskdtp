<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <h3>Database</h3>
    <div class="nav-section">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('coffee.index') }}" class="{{ request()->routeIs('coffee.*') ? 'active' : '' }}">Coffee Shops</a>
        <a href="{{ route('admin.login.monitor') }}" class="{{ request()->routeIs('admin.login.monitor') ? 'active' : '' }}">Monitoring User</a>
    </div>
</div>

<!-- MAIN WRAPPER (INI YANG TADI HILANG) -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <button class="menu-btn">☰</button>

        <div class="flex items-center gap-3" style="margin-left:auto;">
            <span class="badge">
                {{ Auth::user()->username }}
            </span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn-delete">Logout</button>
            </form>
        </div>
    </div>

    <!-- CONTENT (KELUAR DARI TOPBAR) -->
    <main class="content">
        @yield('content')
    </main>

</div>

<script src="{{ asset('js/admin.js') }}"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "{{ session('error') }}",
        timer: 4000,
        showConfirmButton: true
    });
</script>
@endif

</body>
</html>
