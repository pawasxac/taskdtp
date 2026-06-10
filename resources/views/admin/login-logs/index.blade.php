@extends('admin.layouts.main')

@section('title', 'Login Logs')

@section('content')

<div class="page-title">
    <span>Riwayat Login User (Login Logs)</span>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>User</th>
                <th>IP Address</th>
                <th>User Agent</th>
                <th>Waktu Login</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($data as $index => $item)
            <tr>
                <td>{{ $index + 1 + ($data->currentPage() - 1) * $data->perPage() }}</td>
                <td>{{ $item->user->name ?? 'User #' . $item->user_id }} ({{ $item->user->username ?? 'Unknown' }})</td>
                <td>{{ $item->ip_address }}</td>
                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item->user_agent }}">
                    {{ $item->user_agent }}
                </td>
                <td>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : ($item->login_at ?? '-') }}</td>
                <td>
                    <form action="{{ route('login-logs.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus log ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="empty">Belum ada log login.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="table-footer">
        {{ $data->links() }}
    </div>
</div>

@endsection
