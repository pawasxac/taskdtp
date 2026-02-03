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
            {{ strtoupper(substr(auth()->user()->name,0,1)) }}
        </div>
        <div>
            <h3>Halo, {{ auth()->user()->name }} 👋</h3>
            <p>Kelola akun kamu dengan aman di sini.</p>
        </div>
    </div>

    <a href="{{ route('user.profile') }}" class="btn btn-primary">Edit Profil</a>
</div>


</div>

</body>
</html>
