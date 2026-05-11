<!DOCTYPE html>
<html>
<head>
    <title>Profil Saya</title>
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    <style>
        .profile-detail-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }

        .profile-photo-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .profile-photo-large {
            width: 250px;
            height: 250px;
            border-radius: 15px;
            object-fit: cover;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 4px solid #e5e7eb;
        }

        .avatar-large {
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg,#38bdf8,#2563eb);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 100px;
            font-weight: 700;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .detail-group {
            background: #f9fafb;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #2563eb;
        }

        .detail-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .detail-value {
            font-size: 16px;
            color: #1f2937;
            font-weight: 600;
            word-break: break-word;
        }

        .detail-value-secondary {
            font-size: 14px;
            color: #6b7280;
            margin-top: 4px;
        }

        .bio-section {
            grid-column: 1 / -1;
            background: #f9fafb;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #10b981;
        }

        .bio-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .bio-content {
            font-size: 15px;
            color: #374151;
            line-height: 1.6;
            font-style: italic;
        }

        .bio-empty {
            color: #9ca3af;
            font-style: normal;
        }

        .profile-info-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            grid-column: 1 / -1;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #1f2937;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            box-shadow: none;
            transform: none;
        }

        @media (max-width: 768px) {
            .profile-detail-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .profile-photo-large {
                width: 200px;
                height: 200px;
            }

            .avatar-large {
                width: 200px;
                height: 200px;
                font-size: 80px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>Profil Saya</h2>
    <a href="{{ route('user.dashboard') }}">Kembali</a>
</div>

<div class="container">
    <div class="card">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Profile Header -->
        <div class="profile-header">
            <div class="avatar" style="width: 100px; height: 100px; font-size: 40px;">
                @if($user->profile_picture)
                    <img src="{{ asset('uploads/profile_pictures/' . $user->profile_picture) }}" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                @else
                    {{ strtoupper(substr($user->name,0,1)) }}
                @endif
            </div>
            <div>
                <h3>{{ $user->name }}</h3>
                <p>@{{ $user->username }}</p>
            </div>
        </div>

        <!-- Detail Container -->
        <div class="profile-detail-container">
            <!-- Photo Section -->
            <div class="profile-photo-section">
                <h4 style="margin: 0; color: #6b7280; font-size: 14px; text-transform: uppercase;">Foto Profil</h4>
                @if($user->profile_picture)
                    <img src="{{ asset('uploads/profile_pictures/' . $user->profile_picture) }}" alt="Foto Profil" class="profile-photo-large">
                @else
                    <div class="avatar-large">
                        {{ strtoupper(substr($user->name,0,1)) }}
                    </div>
                @endif
            </div>

            <!-- Info Section -->
            <div class="profile-info-section">
                <!-- Name -->
                <div class="detail-group">
                    <div class="detail-label">Nama Lengkap</div>
                    <div class="detail-value">{{ $user->name }}</div>
                </div>

                <!-- Username -->
                <div class="detail-group">
                    <div class="detail-label">Username</div>
                    <div class="detail-value">@{{ $user->username }}</div>
                </div>

                <!-- Email -->
                <div class="detail-group">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">{{ $user->email }}</div>
                </div>

                <!-- Phone Number -->
                <div class="detail-group">
                    <div class="detail-label">Nomor Telepon</div>
                    <div class="detail-value">
                        @if($user->phone_number)
                            {{ $user->phone_number }}
                        @else
                            <span style="color: #9ca3af; font-weight: 400;">Belum diisi</span>
                        @endif
                    </div>
                </div>

                <!-- Role -->
                <div class="detail-group">
                    <div class="detail-label">Jenis Akun</div>
                    <div class="detail-value">
                        @if($user->role === 'admin')
                            <span style="background: #fecaca; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">ADMIN</span>
                        @elseif($user->role === 'user')
                            <span style="background: #bfdbfe; color: #1e40af; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">USER</span>
                        @endif
                    </div>
                </div>

                <!-- Joined Date -->
                <div class="detail-group">
                    <div class="detail-label">Bergabung Sejak</div>
                    <div class="detail-value">{{ $user->created_at->format('d M Y') }}</div>
                    <div class="detail-value-secondary">{{ $user->created_at->diffForHumans() }}</div>
                </div>
            </div>

            <!-- Bio Section -->
            @if($user->bio)
                <div class="bio-section">
                    <div class="bio-label">💭 Bio</div>
                    <div class="bio-content">{{ $user->bio }}</div>
                </div>
            @else
                <div class="bio-section">
                    <div class="bio-label">💭 Bio</div>
                    <div class="bio-content bio-empty">Anda belum menambahkan bio. <a href="{{ route('user.profile') }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">Edit profil</a> untuk menambahkannya.</div>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('user.profile') }}" class="btn btn-primary">✏️ Edit Profil</a>
            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">← Kembali ke Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
