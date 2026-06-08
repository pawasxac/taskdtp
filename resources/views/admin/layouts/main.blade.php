<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="dashboard-body">

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <h3>Database</h3>
    <div class="nav-section">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('coffee.index') }}" class="{{ request()->routeIs('coffee.*') ? 'active' : '' }}">Coffee Shops</a>
        <a href="{{ route('kecamatan.index') }}" class="{{ request()->routeIs('kecamatan.*') ? 'active' : '' }}">Kecamatan</a>
        <a href="{{ route('komunitas.index') }}" class="{{ request()->routeIs('komunitas.*') ? 'active' : '' }}">Komunitas</a>
        <a href="{{ route('community-posts.index') }}" class="{{ request()->routeIs('community-posts.*') ? 'active' : '' }}">Community Posts</a>
        <a href="{{ route('gathering-requests.index') }}" class="{{ request()->routeIs('gathering-requests.*') ? 'active' : '' }}">Gathering Requests</a>
        <a href="{{ route('coffee-shop-reviews.index') }}" class="{{ request()->routeIs('coffee-shop-reviews.*') ? 'active' : '' }}">Reviews</a>
        <a href="{{ route('community-members.index') }}" class="{{ request()->routeIs('community-members.*') ? 'active' : '' }}">Community Members</a>
        <a href="{{ route('admin.login.monitor') }}" class="{{ request()->routeIs('admin.login.monitor') || request()->routeIs('admin.user.*') ? 'active' : '' }}">Manajemen User</a>
    </div>
</div>

<!-- MAIN WRAPPER -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <button class="menu-btn">☰</button>

        <div class="topbar-right">
            <span class="badge">
                {{ Auth::user()->username }}
            </span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn-delete">Logout</button>
            </form>
        </div>
    </div>

    <!-- CONTENT -->
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
