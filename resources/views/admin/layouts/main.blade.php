<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .sidebar {
            background: linear-gradient(180deg, #0a0e1a 0%, #0f1621 50%, #0a0e1a 100%);
            position: fixed;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(139, 111, 71, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(139, 111, 71, 0.02) 0%, transparent 50%);
            pointer-events: none;
        }

        .sidebar h3 {
            position: relative;
            z-index: 1;
            background: linear-gradient(135deg, #ddbfa3 0%, #c6a88b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 11px;
        }

        .sidebar a {
            position: relative;
            z-index: 1;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar a:hover {
            background: rgba(198, 168, 139, 0.1);
            color: #fff;
            border-left: 3px solid #c6a88b;
            padding-left: 11px;
        }

        .sidebar a.active {
            background: linear-gradient(90deg, rgba(198, 168, 139, 0.2) 0%, rgba(198, 168, 139, 0.08) 100%);
            color: #ddbfa3;
            border-left: 3px solid #c6a88b;
            padding-left: 11px;
        }

        .topbar {
            background: linear-gradient(90deg, #0a0e1a 0%, #0f1621 100%);
            border-bottom: 1px solid rgba(198, 168, 139, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .topbar .badge {
            background: rgba(198, 168, 139, 0.15);
            color: #ddbfa3;
            border-color: rgba(198, 168, 139, 0.2);
            font-weight: 600;
        }

        .topbar .btn-delete {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.25);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .topbar .btn-delete:hover {
            background: rgba(239, 68, 68, 0.25);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
        }
    </style>
</head>
<body>

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
