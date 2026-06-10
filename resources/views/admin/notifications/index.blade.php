@extends('admin.layouts.main')

@section('title', 'System Notifications')

@section('content')

<div class="page-title">
    <span>Data System Notifications Table</span>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tipe</th>
                <th>Notifiable Type / ID</th>
                <th>Data</th>
                <th>Tanggal Baca</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($data as $index => $item)
            <tr>
                <td>{{ $index + 1 + ($data->currentPage() - 1) * $data->perPage() }}</td>
                <td>{{ $item->type ?? 'Notification' }}</td>
                <td>{{ $item->notifiable_type ?? '-' }} (ID: {{ $item->notifiable_id ?? '-' }})</td>
                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item->data }}">
                    {{ $item->data }}
                </td>
                <td>{{ $item->read_at ? \Carbon\Carbon::parse($item->read_at)->format('d/m/Y H:i') : 'Belum dibaca' }}</td>
                <td>
                    <form action="{{ route('notifications.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="empty">Belum ada notifikasi sistem di database.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="table-footer">
        {{ $data->links() }}
    </div>
</div>

@endsection
