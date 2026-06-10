@extends('admin.layouts.main')

@section('title', 'Conversations')

@section('content')

<div class="page-title">
    <span>Data Conversations</span>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Tipe</th>
                <th>Nama Group (Jika Ada)</th>
                <th>Tanggal Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($data as $index => $item)
            <tr>
                <td>{{ $index + 1 + ($data->currentPage() - 1) * $data->perPage() }}</td>
                <td>{{ $item->id }}</td>
                <td>{{ $item->type ?? 'Direct' }}</td>
                <td>{{ $item->name ?? '-' }}</td>
                <td>{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') : '-' }}</td>
                <td>
                    <form action="{{ route('conversations.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus percakapan ini beserta seluruh pesan dan anggotanya?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="empty">Belum ada percakapan.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="table-footer">
        {{ $data->links() }}
    </div>
</div>

@endsection
