<!DOCTYPE html>
<html>
<head>
    <title>Edit Profil</title>
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
</head>
<body>

<div class="navbar">
    <h2>Edit Profil</h2>
    <a href="{{ route('user.dashboard') }}">Kembali</a>
</div>

<div class="container">
    <div class="card">
        <div class="profile-header">
    <div class="avatar">
        {{ strtoupper(substr($user->name,0,1)) }}
    </div>
    <div>
        <h3>Edit Profil</h3>
        <p>Ubah data akun dengan verifikasi password.</p>
    </div>
</div>


        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('user.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <label>Username</label>
            <input type="text" name="username" value="{{ $user->username }}" required>
            @error('username') <div class="error">{{ $message }}</div> @enderror

            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" required>
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <label>Password Lama (Wajib)</label>
            <input type="password" name="current_password" required>

            <label>Password Baru (Opsional)</label>
            <input type="password" name="new_password">

            <label>Konfirmasi Password Baru</label>
            <input type="password" name="new_password_confirmation">

            <button class="btn btn-primary">Simpan Perubahan</button>
        </form>

    </div>
</div>

</body>
</html>
