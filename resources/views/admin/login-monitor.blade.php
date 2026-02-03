@extends('admin.layouts.main')

@section('title', 'Monitoring User Login')

@section('content')

<div class="page-title">Monitoring Login User</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>IP Address</th>
                <th>Device</th>
                <th>Waktu Login</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($logs as $log)
            <tr>
                <td>{{ $log->user->username }}</td>
                <td>{{ $log->user->email }}</td>
                <td>{{ $log->ip_address }}</td>
                <td style="max-width:250px">{{ $log->user_agent }}</td>
                <td>{{ $log->login_at }}</td>
                <td>
                    <form action="{{ route('admin.login.monitor.delete', $log->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn-delete">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="empty">Belum ada log login</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="table-footer">
        {{ $logs->links() }}
    </div>
</div>

@endsection
