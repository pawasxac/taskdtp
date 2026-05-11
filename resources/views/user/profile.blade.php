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

        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Foto Profil</label>
                <div class="profile-picture-container" style="margin-bottom: 15px;">
                    @if($user->profile_picture)
                        <img id="profilePreview" src="{{ asset('uploads/profile_pictures/' . $user->profile_picture) }}" alt="Foto Profil" class="profile-img" style="max-width: 200px; border-radius: 8px;">
                    @else
                        <div id="profilePreview" class="avatar-placeholder" style="width: 200px; height: 200px; background: #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 80px; color: #666;">
                            {{ strtoupper(substr($user->name,0,1)) }}
                        </div>
                    @endif
                </div>
                <input type="file" name="profile_picture" id="profileInput" accept="image/*" onchange="previewImage(event)">
                <small>Format: JPG, PNG, GIF, SVG | Max: 2MB</small>
                @error('profile_picture') <div class="error">{{ $message }}</div> @enderror
            </div>

            <label>Username</label>
            <input type="text" name="username" value="{{ $user->username }}" required>
            @error('username') <div class="error">{{ $message }}</div> @enderror

            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" required>
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <label>Bio (Opsional)</label>
            <textarea name="bio" rows="3" placeholder="Cerita singkat tentang kamu">{{ $user->bio ?? '' }}</textarea>
            @error('bio') <div class="error">{{ $message }}</div> @enderror

            <label>Nomor Telepon (Opsional)</label>
            <input type="text" name="phone_number" value="{{ $user->phone_number ?? '' }}" placeholder="Nomor telepon kamu">
            @error('phone_number') <div class="error">{{ $message }}</div> @enderror

            <label>Password Lama (Wajib)</label>
            <input type="password" name="current_password" required>

            <label>Password Baru (Opsional)</label>
            <input type="password" name="new_password">

            <label>Konfirmasi Password Baru</label>
            <input type="password" name="new_password_confirmation">

            <button class="btn btn-primary">Simpan Perubahan</button>
        </form>

        <script>
            function previewImage(event) {
                const reader = new FileReader();
                reader.onload = function() {
                    const preview = document.getElementById('profilePreview');
                    preview.innerHTML = '<img src="' + reader.result + '" alt="Preview" style="max-width: 200px; border-radius: 8px;">';
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        </script>

    </div>
</div>

</body>
</html>
