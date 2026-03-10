@extends('admin.layouts.main')

@section('title', 'Daftar User Terdaftar')

@section('content')

<div class="page-title">
    <span>Daftar User Terdaftar</span>
    <a href="{{ route('admin.user.create') }}" class="btn-add">Tambah User</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $index => $user)
            <tr>
                <td>{{ $index + 1 + ($users->currentPage() - 1) * $users->perPage() }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                        {{ $user->role }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.user.edit', $user->id) }}" class="btn-edit">Edit</a>
                    <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="table-footer">
        {{ $users->links() }}
    </div>
</div>

@endsection
